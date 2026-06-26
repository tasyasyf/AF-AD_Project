<?php

namespace App\Notifications;

use App\Models\Claim;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ClaimSubmittedNotification extends Notification
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
        $claimant = $this->claim->profile->full_name ?? optional($this->claim->profile->user)->name;

        return (new MailMessage)
            ->subject("Claim {$this->claim->claim_reference} awaiting your approval")
            ->greeting("Hello {$notifiable->name},")
            ->line("A new claim has been submitted by {$claimant} and is awaiting your approval.")
            ->line("Claim Reference: {$this->claim->claim_reference}")
            ->line('Total Amount: RM ' . number_format((float) $this->claim->total_amount, 2))
            ->action('Review Claim', route('executive.claims.show', $this->claim))
            ->line('Please review and decide on this claim through your dashboard.');
    }

    /**
     * Dashboard (database) payload.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $claimant = $this->claim->profile->full_name ?? optional($this->claim->profile->user)->name;

        return [
            'type' => 'claim_submitted',
            'claim_id' => $this->claim->id,
            'claim_reference' => $this->claim->claim_reference,
            'claimant_name' => $claimant,
            'total_amount' => (float) $this->claim->total_amount,
            'title' => 'New claim awaiting approval',
            'message' => "Claim {$this->claim->claim_reference} from {$claimant} has been submitted and needs your approval.",
            'url' => route('executive.claims.show', $this->claim),
        ];
    }
}
