<?php

namespace App\Mail;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class DailyAppointmentReportMail extends Mailable
{
    use Queueable, SerializesModels;

    public Collection $appointments;
    public string $recipientName;
    public string $date;

    /**
     * Create a new message instance.
     */
    public function __construct(Collection $appointments, string $recipientName, string $date)
    {
        $this->appointments = $appointments;
        $this->recipientName = $recipientName;
        $this->date = $date;
    }

    /**
     * Build the message with PDF report attachment.
     */
    public function build(): static
    {
        // Generate the PDF report
        $pdf = Pdf::loadView('pdf.daily-report', [
            'appointments' => $this->appointments,
            'recipientName' => $this->recipientName,
            'date' => $this->date,
        ]);

        return $this->subject('Reporte de Citas del Día - MediLink (' . $this->date . ')')
                    ->view('emails.daily-report', [
                        'appointments' => $this->appointments,
                        'recipientName' => $this->recipientName,
                        'date' => $this->date,
                    ])
                    ->attachData($pdf->output(), 'reporte-citas-' . $this->date . '.pdf', [
                        'mime' => 'application/pdf',
                    ]);
    }
}
