<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Appointment;
use App\Services\WhatsAppService;
use Illuminate\Support\Facades\Log;

class SendWhatsAppConfirmation implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    protected $appointment;

    /**
     * Create a new job instance.
     *
     * @param Appointment $appointment
     */
    public function __construct(Appointment $appointment)
    {
        $this->appointment = $appointment;
    }

    /**
     * Execute the job.
     */
    public function handle(WhatsAppService $whatsAppService): void
    {
        // Asume que el paciente tiene relación con User y el teléfono está ahí
        $phoneNumber = $this->appointment->patient->user->phone ?? null;
        
        if (!$phoneNumber) {
            Log::warning('No phone number found for appointment ID: ' . $this->appointment->id);
            return;
        }

        // Limpiar el número de teléfono para CallMeBot (solo números, con código de país)
        $phoneNumber = preg_replace('/[^0-9]/', '', $phoneNumber);

        // Se basa en los atributos date y start_time de la migración de Appointment
        $date = \Carbon\Carbon::parse($this->appointment->date)->format('d/m/Y');
        $time = \Carbon\Carbon::parse($this->appointment->start_time)->format('H:i');
        
        $message = "Hola, tu cita ha sido confirmada para el {$date} a las {$time}. ¡Te esperamos!";

        $whatsAppService->sendMessage($phoneNumber, $message);
    }
}
