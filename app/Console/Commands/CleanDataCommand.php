<?php

namespace App\Console\Commands;

use App\Models\Business;
use App\Models\Company;
use App\Models\Product;
use App\Models\User;
use Illuminate\Console\Command;

class CleanDataCommand extends Command
{
    protected $signature = 'app:clean-data';

    protected $description = 'Clean HTML tags from names and descriptions';

    public function handle()
    {
        $this->info('Cleaning Businesses...');
        Business::all()->each(function ($b) {
            $newName = $this->clean($b->name);
            $newDesc = $this->clean($b->description);
            if ($newName !== $b->name || $newDesc !== $b->description) {
                $b->update(['name' => $newName, 'description' => $newDesc]);
                $this->line("Updated Business: {$b->id}");
            }
        });

        $this->info('Cleaning Companies...');
        Company::all()->each(function ($c) {
            $newName = $this->clean($c->name);
            $newDesc = $this->clean($c->job_description);
            if ($newName !== $c->name || $newDesc !== $c->job_description) {
                $c->update(['name' => $newName, 'job_description' => $newDesc]);
                $this->line("Updated Company: {$c->id}");
            }
        });

        $this->info('Cleaning Products...');
        Product::all()->each(function ($p) {
            $newName = $this->clean($p->name);
            $newDesc = $this->clean($p->description);
            if ($newName !== $p->name || $newDesc !== $p->description) {
                $p->update(['name' => $newName, 'description' => $newDesc]);
                $this->line("Updated Product: {$p->id}");
            }
        });

        $this->info('Cleaning User Testimonies...');
        User::all()->each(function ($u) {
            $newTestimony = $this->clean($u->testimony);
            if ($newTestimony !== $u->testimony) {
                $u->update(['testimony' => $newTestimony]);
                $this->line("Updated User: {$u->id}");
            }
        });

        $this->info('Done!');
    }

    private function clean($text)
    {
        if (! $text) {
            return $text;
        }
        $cleaned = preg_replace('/<br\s*\/?>/i', ' ', $text);

        return trim(strip_tags($cleaned));
    }
}
