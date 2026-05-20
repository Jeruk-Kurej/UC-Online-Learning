<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Business;
use App\Models\Company;
use App\Models\Category;
use App\Models\Skill;
use App\Models\LegalDocument;
use App\Models\Certification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class ResetDatabase extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'db:reset-keep-admin 
                            {--force : Force the operation without confirmation}';

    /**
     * The console command description.
     */
    protected $description = 'Reset database: delete all data except admin users';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (!$this->option('force')) {
            if (!$this->confirm('⚠️  This will delete ALL businesses and non-admin users. Continue?')) {
                $this->info('Operation cancelled.');
                return 0;
            }
        }

        $this->info('🔥 Starting database reset...');
        $this->newLine();

        // 1. Delete all businesses (will cascade to products, services, etc.)
        $this->info('Deleting all businesses...');
        $businessCount = Business::count();
        Business::query()->delete();
        $this->info("✅ Deleted {$businessCount} businesses");

        // 2. Delete all companies
        $this->info('Deleting all companies...');
        $companyCount = Company::count();
        Company::query()->delete();
        $this->info("✅ Deleted {$companyCount} companies");

        // 3. Delete categories
        $this->info('Deleting all categories...');
        $catCount = Category::count();
        Category::query()->delete();
        $this->info("✅ Deleted {$catCount} categories");

        // 4. Delete skills
        $this->info('Deleting all skills...');
        $skillCount = Skill::count();
        Skill::query()->delete();
        $this->info("✅ Deleted {$skillCount} skills");

        // 5. Delete legal documents
        $this->info('Deleting all legal documents...');
        $legalCount = LegalDocument::count();
        LegalDocument::query()->delete();
        $this->info("✅ Deleted {$legalCount} legal documents");

        // 6. Delete certifications
        $this->info('Deleting all certifications...');
        $certCount = Certification::count();
        Certification::query()->delete();
        $this->info("✅ Deleted {$certCount} certifications");

        // 7. Delete ALL users
        $this->info('Deleting all users...');
        $userCount = User::count();
        User::query()->delete();
        $this->info("✅ Deleted {$userCount} users");

        // 8. Create ONE default admin
        $this->info('Creating default admin...');
        $admin = User::create([
            'name' => 'Admin UCO',
            'email' => 'admin@uco.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);
        $this->info("✅ Created admin: {$admin->email}");

        $this->newLine();
        $this->info('✅ Database reset complete!');
        $this->info('📧 Admin login: admin@uco.com / password: password');
        
        return 0;
    }
}
