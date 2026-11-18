<?php

namespace App\Features\Care\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

final class IncidentReportedMail extends Mailable
{
    public function __construct(
        public readonly string $patientName,
        public readonly string $level,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Incidencia de cuidado');
    }

    public function content(): Content
    {
        return new Content(text: 'mail.incident-reported');
    }
}
