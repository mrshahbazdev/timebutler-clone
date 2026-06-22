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
        return ['database', 'mail'];
    }

    public function toArray(object $notifiable): array
    {
        $absence = $this->absence->load('absenceType');
        $isApproved = $this->decision === 'approved';
        return [
            'type' => 'absence_decision',
            'absence_id' => $absence->id,
            'decision' => $this->decision,
            'absence_type' => $absence->absenceType->name ?? 'Absence',
            'start_date' => $absence->start_date->format('d.m.Y'),
            'end_date' => $absence->end_date->format('d.m.Y'),
            'message_de' => $isApproved ? "{$absence->absenceType->name_de} genehmigt ({$absence->start_date->format('d.m.Y')} - {$absence->end_date->format('d.m.Y')})" : "{$absence->absenceType->name_de} abgelehnt ({$absence->start_date->format('d.m.Y')} - {$absence->end_date->format('d.m.Y')})",
            'message_en' => $isApproved ? "{$absence->absenceType->name_en} approved ({$absence->start_date->format('d.m.Y')} - {$absence->end_date->format('d.m.Y')})" : "{$absence->absenceType->name_en} rejected ({$absence->start_date->format('d.m.Y')} - {$absence->end_date->format('d.m.Y')})",
            'url' => '/absences',
        ];
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
