<?php

namespace App\Notifications;

use App\Models\Claim;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ClaimRejectedNotification extends Notification
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
        return (new MailMessage)
            ->subject("Your claim {$this->claim->claim_reference} has been rejected")
            ->greeting("Hello {$notifiable->name},")
            ->line("Your claim {$this->claim->claim_reference} has been rejected by the School Executive.")
            ->line('Reason / Remarks: ' . ($this->claim->executive_remarks ?: 'No reason provided.'))
            ->action('View Claim', route('afad.claims.show', $this->claim))
            ->line('Please review the remarks above. You may submit a new claim if required.');
    }

    /**
     * Dashboard (database) payload.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'claim_rejected',
            'claim_id' => $this->claim->id,
            'claim_reference' => $this->claim->claim_reference,
            'remarks' => $this->claim->executive_remarks,
            'title' => 'Claim rejected',
            'message' => "Your claim {$this->claim->claim_reference} was rejected. Reason: " . ($this->claim->executive_remarks ?: 'No reason provided.'),
            'url' => route('afad.claims.show', $this->claim),
        ];
    }
}
