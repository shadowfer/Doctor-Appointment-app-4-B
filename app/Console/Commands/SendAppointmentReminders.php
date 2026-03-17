<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Appointment;
use App\Services\WhatsAppService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class SendAppointmentReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'appointments:send-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Envía un recordatorio por WhatsApp un día antes de la cita programada';

    /**
     * Execute the console command.
     */
    public function handle(WhatsAppService $whatsAppService)
    {
        $this->info('Iniciando el envío de recordatorios de citas...');

        // Buscar citas para exactamente 1 día a partir de hoy (mañana)
        $tomorrow = Carbon::tomorrow()->toDateString();
        
        // 1 = programado en la base de datos
        $appointments = Appointment::where('date', 'like', '%' . $tomorrow . '%')
            ->where('status', 1)
            ->get();

        if ($appointments->isEmpty()) {
            $this->info("No hay citas programadas para mañana ($tomorrow).");
            return;
        }

        $count = 0;
        foreach ($appointments as $appointment) {
            $phoneNumber = $appointment->patient->user->phone ?? null;
            
            if ($phoneNumber) {
                // Asegurar que el teléfono tenga el '+' al principio requerido por Twilio
                if (strpos($phoneNumber, '+') !== 0) {
                    $phoneNumber = '+' . ltrim($phoneNumber, '0');
                }

                // Formateamos la hora asegurando que es leída correctamente
                $time = Carbon::parse($appointment->start_time)->format('H:i');
                $message = "Recordatorio: Tienes una cita médica mañana a las {$time}. ¡Te esperamos!";
                
                if ($whatsAppService->sendMessage($phoneNumber, $message)) {
                    $count++;
                } else {
                    Log::error("Fallo al enviar a: " . $phoneNumber);
                }
            } else {
                Log::warning('No phone number found for appointment reminder ID: ' . $appointment->id);
            }
        }

        $this->info("Se enviaron $count recordatorios de citas para mañana ($tomorrow).");
    }
}
