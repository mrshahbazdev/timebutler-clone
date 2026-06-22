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
        return ['database', 'mail'];
    }

    public function toArray(object $notifiable): array
    {
        $absence = $this->absence->load(['user', 'absenceType']);
        return [
            'type' => 'absence_request',
            'absence_id' => $absence->id,
            'employee_name' => $absence->user->name,
            'absence_type' => $absence->absenceType->name ?? 'Absence',
            'start_date' => $absence->start_date->format('d.m.Y'),
            'end_date' => $absence->end_date->format('d.m.Y'),
            'total_days' => $absence->total_days,
            'message_de' => "{$absence->user->name} hat {$absence->absenceType->name_de} beantragt ({$absence->start_date->format('d.m.Y')} - {$absence->end_date->format('d.m.Y')})",
            'message_en' => "{$absence->user->name} requested {$absence->absenceType->name_en} ({$absence->start_date->format('d.m.Y')} - {$absence->end_date->format('d.m.Y')})",
            'url' => '/absences/team',
        ];
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
