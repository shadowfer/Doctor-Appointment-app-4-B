<?php

namespace App\Mail;

use App\Models\Appointment;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AppointmentReceiptMail extends Mailable
{
    use Queueable, SerializesModels;

    public Appointment $appointment;
    public string $recipientType; // 'patient' o 'doctor'
    public string $recipientName;

    /**
     * Create a new message instance.
     *
     * @param Appointment $appointment
     * @param string $recipientType 'patient' o 'doctor'
     */
    public function __construct(Appointment $appointment, string $recipientType = 'patient')
    {
        $this->appointment = $appointment;
        $this->recipientType = $recipientType;

        // Determinar el nombre del destinatario
        if ($recipientType === 'doctor') {
            $this->recipientName = 'Dr(a). ' . $appointment->doctor->user->name;
        } else {
            $this->recipientName = $appointment->patient->user->name;
        }
    }

    /**
     * Build the message with PDF attachment.
     */
    public function build(): static
    {
        // Eager load relationships for the PDF
        $this->appointment->load(['patient.user', 'doctor.user', 'doctor.speciality']);

        // Generate PDF
        $pdf = Pdf::loadView('pdf.appointment-receipt', [
            'appointment' => $this->appointment,
        ]);

        return $this->subject('Comprobante de Cita Médica - MediLink')
                    ->view('emails.appointment-receipt', [
                        'appointment' => $this->appointment,
                        'recipientType' => $this->recipientType,
                        'recipientName' => $this->recipientName,
                    ])
                    ->attachData($pdf->output(), 'comprobante-cita-' . $this->appointment->id . '.pdf', [
                        'mime' => 'application/pdf',
                    ]);
    }
}
