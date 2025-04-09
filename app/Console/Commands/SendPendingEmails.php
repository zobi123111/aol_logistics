<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Mail\DynamicTemplateMail;
use App\Models\EmailJob;

class SendPendingEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:send-pending';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send all pending emails from the email_jobs table';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $pendingEmails = EmailJob::where('status', 'pending')->limit(50)->get();

        foreach ($pendingEmails as $email) {
            try {
                $recipients = is_array($email->to_email) ? $email->to_email : json_decode($email->to_email, true);
                $payload = $email->payload ?? [];

                foreach ($recipients as $recipient) {
                    Mail::to(trim($recipient))->send(
                        new DynamicTemplateMail($payload, $email->subject, $email->template)
                    );
                }

                $email->status = 'sent';
                $email->error_message = null;
                $email->save();

                $this->info("Email sent to: " . implode(', ', $recipients));
            } catch (\Exception $e) {
                $email->status = 'failed';
                $email->error_message = $e->getMessage();
                $email->save();

                $this->error("Failed to send to: " . json_encode($email->to_email) . " | Error: " . $e->getMessage());
            }
        }

        return Command::SUCCESS;
    }
}
