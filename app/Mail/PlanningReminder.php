<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PlanningReminder extends Mailable
{
    use Queueable, SerializesModels;

    public $planning;

    /**
     * Create a new message instance.
     */
    public function __construct($planning)
    {
        $this->planning = $planning;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Recordatorio de Planificación',
        );
    }

    /**
     * Build the message.
     */
    public function build()
    {


            // Determinar dinámicamente la fecha final del modelo
        $dateEnd = $this->planning->D_date_end ?? $this->planning->date_end ?? $this->planning->F_date_end ?? 'Fecha no disponible';

        return $this->view('emails.planning_reminder')
            ->subject('Recordatorio de las Planificaciones')
            ->with([
            'planning' => $this->planning,
            'dateEnd' => $dateEnd,
        ]);

    }
    /**
     * Get the message content definition.
     */

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
