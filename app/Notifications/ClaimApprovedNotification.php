<?php

namespace App\Notifications;

use App\Models\Claim;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ClaimApprovedNotification extends Notification
{
    use Queueable;

    public function __construct(public Claim $claim)
    {
    }

    /**
     * Deliver via the dashboard (database) and email.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject("Your claim {$this->claim->claim_reference} has been approved")
            ->greeting("Hello {$notifiable->name},")
            ->line("Good news! Your claim {$this->claim->claim_reference} has been approved by the School Executive.")
            ->line('Total Amount: RM ' . number_format((float) $this->claim->total_amount, 2));

        if (!empty($this->claim->executive_remarks)) {
            $mail->line("Remarks from the School Executive: {$this->claim->executive_remarks}");
        }

        return $mail
            ->action('View Claim', route('afad.claims.show', $this->claim))
            ->line('Thank you for your submission.');
    }

    /**
     * Dashboard (database) payload.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'claim_approved',
            'claim_id' => $this->claim->id,
            'claim_reference' => $this->claim->claim_reference,
            'remarks' => $this->claim->executive_remarks,
            'title' => 'Claim approved',
            'message' => "Your claim {$this->claim->claim_reference} has been approved by the School Executive.",
            'url' => route('afad.claims.show', $this->claim),
        ];
    }
}
