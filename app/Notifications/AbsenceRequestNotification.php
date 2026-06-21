<?php

namespace App\Notifications;

use App\Models\AbsenceRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AbsenceRequestNotification extends Notification
{
    use Queueable;

    public function __construct(
        protected AbsenceRequest $absence,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $absence = $this->absence->load(['user', 'absenceType']);
        $employeeName = $absence->user->name;
        $typeName = $absence->absenceType->name ?? 'Absence';
        $dates = $absence->start_date->format('d.m.Y') . ' - ' . $absence->end_date->format('d.m.Y');

        return (new MailMessage)
            ->subject("Neuer Abwesenheitsantrag von {$employeeName}")
            ->greeting("Hallo {$notifiable->name},")
            ->line("{$employeeName} hat einen Antrag auf {$typeName} eingereicht.")
            ->line("Zeitraum: {$dates} ({$absence->total_days} Tage)")
            ->line($absence->notes ? "Anmerkungen: {$absence->notes}" : '')
            ->action('Antrag ansehen', url('/absences/team'))
            ->line('Bitte prüfen und genehmigen oder ablehnen.');
    }
}
