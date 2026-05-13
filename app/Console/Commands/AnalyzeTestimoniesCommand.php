<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Services\AiModerationService;

class AnalyzeTestimoniesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ai:analyze-testimonies';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Analyze existing testimonies that do not have an AI sentiment score.';

    /**
     * Execute the console command.
     */
    public function handle(AiModerationService $aiService)
    {
        $users = User::whereNotNull('testimony')
            ->where('testimony', '!=', '')
            ->whereNull('ai_score')
            ->get();

        if ($users->isEmpty()) {
            $this->info('No testimonies to analyze.');
            return;
        }

        $this->info('Analyzing ' . $users->count() . ' testimonies...');
        $bar = $this->output->createProgressBar($users->count());

        foreach ($users as $user) {
            $rating = 5; // Default assumption for imported ones if no rating
            $result = $aiService->analyze($user->testimony, $rating, $user->name);

            $user->update([
                'ai_score' => $result['sentiment_score'],
                'ai_sentiment' => $result['sentiment'],
                'is_visible' => $result['is_approved'],
                'ai_rejection_reason' => $result['rejection_reason'],
            ]);

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info('Analysis completed successfully!');
    }
}
