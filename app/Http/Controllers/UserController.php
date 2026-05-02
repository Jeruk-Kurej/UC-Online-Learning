<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Business;
use App\Http\Requests\UpdateUserRequest;
use App\Imports\FormResponseImport;
use App\Imports\UCOStudentImport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class UserController extends Controller
{
    /**
     * Show the form for creating a new user.
     */
    public function create()
    {
        $this->ensureAdmin();
        return view('users.create');
    }

    /**
     * Ensure the current user is an admin.
     * Annotate the local variable so static analyzers understand the type.
     */
    private function ensureAdmin(): void
    {
        /** @var User|null $user */
        $user = Auth::user();
        if (! $user || ! $user->isAdmin()) {
            abort(403);
        }
    }

    /**
     * Store a newly created user.
     */
    public function store(Request $request)
    {
        $this->ensureAdmin();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            // role can be 'student', 'alumni', or 'admin' per the create form
            'role' => 'required|in:student,alumni,admin',
            // student_status stored in DB is one of: active, inactive, cuti, alumni
            'student_status' => 'nullable|in:active,inactive,cuti,alumni',
        ]);

        // Map frontend role values to DB role enum ('user' or 'admin')
        $inputRole = $validated['role'];
        $dbRole = $inputRole === 'admin' ? 'admin' : 'user';

        // Determine student_status: map frontend choices to DB enum
        // If the form explicitly provided a valid DB status, use it. Otherwise, if role is 'alumni' mark as 'alumni', else default to 'active'.
        $studentStatus = $validated['student_status'] ?? ($inputRole === 'alumni' ? 'alumni' : 'active');

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $dbRole,
            'student_status' => $studentStatus,
            'is_visible' => true,
        ]);

        return redirect()->route('users.show', $user)->with('success', 'User created successfully!');
    }

    /**
     * Display a listing of users.
     */
    public function index(Request $request)
    {
        $this->ensureAdmin();

        $search = $request->get('search');
        $query = User::withCount('businesses');
        
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%");
            });
        }
        
        $users = $query->latest()->paginate(20);

        // Calculate stats for the view
        $totalUsers = User::count();
        $totalEntrepreneurs = User::whereHas('businesses', fn ($q) => $q->where('type', 'entrepreneur'))->count();
        $totalIntrapreneurs = User::whereHas('companies')->count();
        $totalAlumni = User::where('student_status', 'alumni')->count();
        $featuredUserCount = User::where('is_featured', true)->count();

        return view('users.index', compact('users', 'totalUsers', 'totalEntrepreneurs', 'totalIntrapreneurs', 'totalAlumni', 'featuredUserCount'));
    }

    /**
     * Display the specified user.
     */
    public function show(User $user)
    {
        $this->ensureAdmin();

        $user->load(['businesses.category', 'companies.category', 'skills']);
        return view('users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user)
    {
        $this->ensureAdmin();
        $userToEdit = $user;
        return view('users.edit', compact('userToEdit'));
    }

    /**
     * Update the specified user.
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        $this->ensureAdmin();

        $data = $request->validated();
        
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        } else {
            unset($data['password']);
        }

        // Map frontend role values to DB role enum ('user' or 'admin') if present
        if (isset($data['role'])) {
            $data['role'] = $data['role'] === 'admin' ? 'admin' : 'user';
        }

        // Boolean handling
        $data['is_visible'] = $request->has('is_visible');

        // Map student_status when role selection implies it (e.g., 'alumni' or 'student')
        if (!isset($data['student_status']) && $request->filled('role')) {
            if ($request->input('role') === 'alumni') {
                $data['student_status'] = 'alumni';
            } elseif ($request->input('role') === 'student') {
                $data['student_status'] = 'active';
            }
        }

        $user->update($data);

        return redirect()->route('users.show', $user)->with('success', 'User updated successfully!');
    }

    /**
     * Toggle the featured status of a user (admin only, max 4 featured at once).
     */
    public function toggleFeatured(User $user)
    {
        $this->ensureAdmin();

        if ($user->is_featured) {
            $user->update(['is_featured' => false]);
            return back()->with('success', "\"{$user->name}\" removed from featured.");
        }

        $featuredCount = User::where('is_featured', true)->count();
        if ($featuredCount >= 4) {
            return back()->withErrors(['featured' => 'Maximum of 4 featured users reached. Un-feature one first.']);
        }

        $user->update(['is_featured' => true]);
        return back()->with('success', "\"{$user->name}\" is now featured.");
    }

    /**
     * Remove the specified user.
     */
    public function destroy(User $user)
    {
        $this->ensureAdmin();
        if (Auth::id() === $user->id) {
            abort(403);
        }

        $user->delete();
        return redirect()->route('users.index')->with('success', 'User deleted!');
    }

    /**
     * Import users/businesses from CSV.
     * Auto-detects format:
     *  - "UCO Student Profile" CSV (row 3 headers, NIS column) → UCOStudentImport
     *  - Google Form response CSV (row 1 headers, Email Address column) → FormResponseImport
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:20480',
        ]);

        try {
            $importId = 'import_' . time();
            $file = $request->file('file');

            // Store file to local disk (Cloudinary broken)
            $path = $file->store('imports', 'local');

            // Peek at the file to auto-detect format using the local temp upload file
            $importer = $this->detectImporter($file->getRealPath(), $importId, $file->getClientOriginalName());

            // Queue it — runs in background via `php artisan queue:work`
            Excel::queueImport($importer, $path, 'local');

            $format = $importer instanceof UCOStudentImport ? 'UCO Student Profile' : 'Form Response';

            // Store importId in session so frontend can poll progress
            session(['active_import' => $importId]);

            return back()->with('success', "Import queued! Format: {$format}. Processing ~1500 rows in background...")
                         ->with('importId', $importId);
        } catch (\Exception $e) {
            Log::error('Import error: ' . $e->getMessage());
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Peek at the raw file to determine which importer to use.
     * UCO Student Profile CSV has "NIS" + "Sub Prodi" in row 3.
     * Form Response CSV has "Email Address" in row 1.
     */
    /**
     * Peek at the raw file to determine which importer to use.
     * UCO Student Profile CSV has "NIS" + "Sub Prodi" in row 3.
     * Form Response CSV has "Email Address" in row 1.
     */
    private function detectImporter(string $path, string $importId, string $originalName = '')
    {
        // For xlsx/xls we can't search raw text easily — check filename heuristic
        $ext = strtolower(pathinfo($originalName ?: $path, PATHINFO_EXTENSION));
        if (in_array($ext, ['xlsx', 'xls'])) {
            if (stripos($originalName, 'UCO') !== false || stripos($originalName, 'Student') !== false) {
                Log::info("Import: XLSX filename heuristic → UCOStudentImport");
                return new UCOStudentImport($importId);
            }
            Log::info("Import: XLSX default → FormResponseImport");
            return new FormResponseImport($importId);
        }

        // For CSV/Raw: read first 2KB and search for markers
        $handle = fopen($path, 'r');
        $peek = fread($handle, 2048);
        fclose($handle);

        // UCO Student format markers: "NIS" AND "Sub Prodi"
        // Form Response markers: "Timestamp" OR "Email Address" (row 1)
        if (stripos($peek, 'NIS') !== false && stripos($peek, 'Sub Prodi') !== false) {
            Log::info("Import: detected UCO Student Profile format via content markers");
            return new UCOStudentImport($importId);
        }

        Log::info("Import: falling back to Form Response format");
        return new FormResponseImport($importId);
    }

}

