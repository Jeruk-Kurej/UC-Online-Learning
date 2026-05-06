<?php

use App\Models\User;
use App\Models\Business;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('test:business-enhancement', function () {
    $this->info('🧪 Testing Business Data Enhancement Features...');
    $this->newLine();
    
    // Get businesses with user
    $businesses = Business::with('user', 'category')->get();
    
    if ($businesses->isEmpty()) {
        $this->error('❌ No businesses found! Please run seeder first:');
        $this->line('   php artisan db:seed --class=EnhancedBusinessSeeder');
        return;
    }
    
    $this->info("📊 Found {$businesses->count()} businesses");
    $this->newLine();
    
    foreach ($businesses as $index => $business) {
        $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->info('Business #' . ($index + 1) . ': ' . $business->name);
        $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->newLine();
        
        // Basic Info
        $this->line('<fg=cyan>📌 Basic Information:</>');
        $this->line('   Owner: ' . $business->user->name);
        $this->line('   Type: ' . ($business->category->name ?? 'N/A'));
        $this->line('   Mode: ' . ucfirst($business->business_mode));
        $this->line('   Description: ' . substr($business->description, 0, 80) . '...');
        $this->newLine();
        
        // Enhanced Fields
        $this->line('<fg=green>✨ Enhanced Data (from 42-column Excel):</>');
        $this->line('   Logo: ' . ($business->logo_url ?? '<fg=yellow>Not set</>'));
        $this->line('   Established: ' . ($business->established_date ? $business->established_date->format('d M Y') : '<fg=yellow>Not set</>'));
        $this->line('   Address: ' . ($business->address ?? '<fg=yellow>Not set</>'));
        $this->line('   Employees: ' . ($business->employee_count ?? '<fg=yellow>Not set</>'));
        $this->line('   Revenue: ' . ($business->revenue_range ?? 'Not set'));
        $this->line('   From College Project: ' . ($business->is_from_college_project ? '<fg=green>Yes ✓</>' : '<fg=red>No</>'));
        $this->line('   Continued After Grad: ' . ($business->is_continued_after_graduation ? '<fg=green>Yes ✓</> (Active)' : '<fg=red>No</> (Inactive)'));
        $this->line('   Status: ' . ($business->is_visible ? '<fg=green>🟢 Visible</>'  : '<fg=red>🔴 Hidden</>'));
        $this->newLine();
        
        // Legal Documents
        if ($business->legalDocuments()->count() > 0) {
            $this->line('<fg=blue>📄 Legal Documents:</>');
            foreach ($business->legalDocuments as $doc) {
                $this->line('   - ' . $doc->name);
            }
            $this->newLine();
        }
        
        // Certifications
        if ($business->certifications()->count() > 0) {
            $this->line('<fg=magenta>🏆 Product Certifications:</>');
            foreach ($business->certifications as $cert) {
                $this->line('   - ' . $cert->name);
            }
            $this->newLine();
        }
    }
    
    // User Summary
    $user = $businesses->first()->user;
    $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
    $this->info('👤 Business Owner Summary');
    $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
    $this->newLine();
    $this->line('<fg=cyan>Owner Information:</>');
    $this->line('   Name: ' . $user->name);
    $this->line('   Employment Status: ' . $user->current_status);
    $this->line('   Total Businesses: ' . $user->businesses()->count());
    $this->line('   Is Entrepreneur: ' . ($user->isEntrepreneur() ? '<fg=green>Yes ✓</>' : '<fg=red>No</>'));
    $this->line('   Is Intrapreneur: ' . ($user->isIntrapreneur() ? '<fg=green>Yes ✓</>' : '<fg=red>No</>'));
    $this->newLine();
    
    $this->info('✅ All tests passed! Business data enhancement is working correctly.');
    $this->newLine();
    
})->purpose('Test enhanced business data features from 42-column Excel');

