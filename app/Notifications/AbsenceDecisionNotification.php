<?php

namespace App\Notifications;

use App\Models\AbsenceRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AbsenceDecisionNotification extends Notification
{
    use Queueable;

    public function __construct(
        protected AbsenceRequest $absence,
        protected string $decision,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $absence = $this->absence->load('absenceType');
        $typeName = $absence->absenceType->name ?? 'Absence';
        $dates = $absence->start_date->format('d.m.Y') . ' - ' . $absence->end_date->format('d.m.Y');
        $isApproved = $this->decision === 'approved';

        $mail = (new MailMessage)
            ->subject($isApproved ? "Antrag genehmigt: {$typeName}" : "Antrag abgelehnt: {$typeName}")
            ->greeting("Hallo {$notifiable->name},")
            ->line($isApproved
                ? "Ihr Antrag auf {$typeName} wurde genehmigt."
                : "Ihr Antrag auf {$typeName} wurde leider abgelehnt.")
            ->line("Zeitraum: {$dates} ({$absence->total_days} Tage)");

        if (!$isApproved && $absence->rejection_reason) {
            $mail->line("Begründung: {$absence->rejection_reason}");
        }

        return $mail->action('Meine Abwesenheiten', url('/absences'));
    }
}
