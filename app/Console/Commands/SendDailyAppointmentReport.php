<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Mail\DailyAppointmentReportMail;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendDailyAppointmentReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'report:daily-appointments';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Envía un reporte por correo al administrador y a cada doctor con las citas programadas para hoy';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $today = Carbon::today()->toDateString();
        $this->info("Generando reporte de citas para: {$today}");

        // Obtener todas las citas programadas para hoy (status = 1 = programado)
        $allAppointments = Appointment::with(['patient.user', 'doctor.user', 'doctor.speciality'])
            ->where('date', $today)
            ->where('status', 1)
            ->orderBy('start_time')
            ->get();

        $this->info("Total de citas encontradas: {$allAppointments->count()}");

        // ────────────────────────────────────
        // 1. Enviar reporte al Administrador
        // ────────────────────────────────────
        $adminEmail = config('mail.admin_email', env('ADMIN_EMAIL', 'admin@medilink.com'));

        try {
            Mail::to($adminEmail)->send(
                new DailyAppointmentReportMail($allAppointments, 'Administrador', $today)
            );
            $this->info("✅ Reporte enviado al administrador: {$adminEmail}");
        } catch (\Exception $e) {
            $this->error("❌ Error al enviar al administrador: {$e->getMessage()}");
            Log::error("Error enviando reporte diario al admin: " . $e->getMessage());
        }

        // ────────────────────────────────────
        // 2. Enviar reporte a cada Doctor con sus citas del día
        // ────────────────────────────────────
        $appointmentsByDoctor = $allAppointments->groupBy('doctor_id');

        foreach ($appointmentsByDoctor as $doctorId => $doctorAppointments) {
            $doctor = Doctor::with('user')->find($doctorId);

            if (!$doctor || !$doctor->user || !$doctor->user->email) {
                $this->warn("⚠️  Doctor ID {$doctorId} sin correo electrónico, omitido.");
                continue;
            }

            try {
                Mail::to($doctor->user->email)->send(
                    new DailyAppointmentReportMail(
                        $doctorAppointments,
                        'Dr(a). ' . $doctor->user->name,
                        $today
                    )
                );
                $this->info("✅ Reporte enviado al Dr(a). {$doctor->user->name}: {$doctor->user->email}");
            } catch (\Exception $e) {
                $this->error("❌ Error al enviar al Dr(a). {$doctor->user->name}: {$e->getMessage()}");
                Log::error("Error enviando reporte diario al doctor {$doctorId}: " . $e->getMessage());
            }
        }

        $this->info("Proceso de reporte diario finalizado.");
    }
}
