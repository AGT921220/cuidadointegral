<?php

namespace App\Features\Care\Jobs;

use App\Features\Care\Mail\IncidentReportedMail;
use App\Models\Incident;
use App\Models\PatientLink;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

final class NotifyIncidentJob implements ShouldQueue
{
    use Queueable;

    public function __construct(public readonly int $incidentId)
    {
        $this->onQueue('notifications');
    }

    public function handle(): void
    {
        $incident = Incident::query()->with('patient:id,name')->find($this->incidentId);

        if ($incident === null) {
            return;
        }

        $recipientIds = PatientLink::query()
            ->where('patient_id', $incident->patient_id)
            ->whereIn('link_type', [PatientLink::FAMILY, PatientLink::SUPERVISOR])
            ->where('user_id', '!=', $incident->user_id)
            ->pluck('user_id');

        $recipients = User::query()
            ->whereIn('id', $recipientIds)
            ->whereNotNull('email')
            ->get(['id', 'name', 'email']);

        Log::info('Incidencia de cuidado', [
            'incident_id' => $incident->id,
            'patient_id' => $incident->patient_id,
            'level' => $incident->level,
            'recipients' => $recipients->count(),
        ]);

        $patientName = $incident->patient?->name ?? 'paciente';

        foreach ($recipients as $recipient) {
            Mail::to($recipient->email)->send(new IncidentReportedMail($patientName, $incident->level));
        }
    }
}
