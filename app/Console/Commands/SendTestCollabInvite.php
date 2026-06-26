<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Message;

class SendTestCollabInvite extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'inbox:test-collab {recipient_email} {--sender_email=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send a test collaboration invite to a user';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $recipientEmail = $this->argument('recipient_email');
        $senderEmail = $this->option('sender_email');

        $recipient = User::where('email', $recipientEmail)->first();
        if (!$recipient) {
            $this->error("Recipient not found: {$recipientEmail}");
            return;
        }

        $sender = null;
        if ($senderEmail) {
            $sender = User::where('email', $senderEmail)->first();
            if (!$sender) {
                $this->error("Sender not found: {$senderEmail}");
                return;
            }
        } else {
            // Pick a random sender if none provided
            $sender = User::where('id', '!=', $recipient->id)->inRandomOrder()->first();
        }

        Message::create([
            'sender_id' => $sender ? $sender->id : null,
            'recipient_id' => $recipient->id,
            'subject' => 'Collaboration Request',
            'body' => "Hi {$recipient->name},\n\nI would love to collaborate with you on a new project. Let me know if you are interested!\n\nBest,\n" . ($sender ? $sender->name : 'System'),
            'type' => 'collab_invite',
        ]);

        $this->info("Collaboration invite sent to {$recipient->email}" . ($sender ? " from {$sender->email}" : ""));
    }
}
