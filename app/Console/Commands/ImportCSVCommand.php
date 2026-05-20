<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\FormResponseImport;
use Illuminate\Support\Facades\File;

class ImportCSVCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'uco:import-csv';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import Entrepreneur and Intrapreneur CSV responses from Data_to_import folder';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting CSV import process...');

        $dir = base_path('Data_to_import');
        if (!File::exists($dir)) {
            $this->error("Directory {$dir} does not exist.");
            return Command::FAILURE;
        }

        $files = File::files($dir);
        $csvFiles = [];
        foreach ($files as $file) {
            if (strtolower($file->getExtension()) === 'csv') {
                $csvFiles[] = $file;
            }
        }

        if (empty($csvFiles)) {
            $this->warn('No CSV files found in Data_to_import folder.');
            return Command::SUCCESS;
        }

        foreach ($csvFiles as $file) {
            $this->info("Importing file: " . $file->getFilename());
            try {
                $importId = (string) Str::uuid();
                Excel::import(new FormResponseImport($importId), $file->getRealPath());
                $this->info("Successfully imported " . $file->getFilename());
            } catch (\Exception $e) {
                $this->error("Failed to import " . $file->getFilename() . ": " . $e->getMessage());
                Log::error("CLI Import failed for {$file->getFilename()}: " . $e->getMessage());
            }
        }

        $this->info('CSV import process completed.');
        return Command::SUCCESS;
    }
}
