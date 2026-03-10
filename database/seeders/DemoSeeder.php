<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Speciality;
use App\Models\BloodType;
use App\Models\DoctorSchedule;
use App\Models\Appointment;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class DemoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $specialities = Speciality::all();
        $bloodTypes = BloodType::all();

        // Configurar Faker en español localmente para asegurar nombres hispanos si el config falla
        $faker = \Faker\Factory::create('es_ES');

        // 1. Crear Doctores de Prueba (Mínimo 2 por Especialidad)
        foreach ($specialities as $speciality) {
            // Creamos 2 doctores por especialidad
            for ($i = 0; $i < 2; $i++) {
                // Crear usuario doctor con nombre hispano
                $user = User::factory()->create([
                    'name' => $faker->name,
                    'email' => $faker->unique()->safeEmail,
                ]);
                $user->assignRole('Doctor');

                // Relacionar a Doctor (tabla: doctors)
                $doctor = Doctor::create([
                    'user_id' => $user->id,
                    'speciality_id' => $speciality->id,
                ]);

                // Generar Horarios Disponibles de Lunes a Viernes para la Demo
                // Turnos en la mañana (08:00 - 12:00) para simplificar la demo
                for ($day = 1; $day <= 5; $day++) { 
                    for ($hour = 8; $hour < 12; $hour++) {
                        for ($min = 0; $min < 60; $min += 15) {
                            // ~30% de probabilidad de tener huecos libres intermitentes
                            if (rand(1, 100) > 30) {
                                $startTime = sprintf('%02d:%02d:00', $hour, $min);
                                $endMin = $min + 15;
                                $endHour = $hour;
                                if ($endMin == 60) {
                                    $endMin = 0;
                                    $endHour++;
                                }
                                $endTime = sprintf('%02d:%02d:00', $endHour, $endMin);

                                DoctorSchedule::create([
                                    'doctor_id' => $doctor->id,
                                    'day_of_week' => $day,
                                    'start_time' => $startTime,
                                    'end_time' => $endTime,
                                    'is_available' => true,
                                ]);
                            }
                        }
                    }
                }
            }
        }

        // 2. Crear Pacientes de Prueba
        $patientUsers = User::factory(20)->create()->each(function ($user) use ($bloodTypes) {
            $user->assignRole('Paciente');

            Patient::create([
                'user_id' => $user->id,
                'blood_type_id' => $bloodTypes->random()->id,
                'emergency_contact_name' => fake()->name(),
                'emergency_contact_phone' => fake()->phoneNumber(),
            ]);
        });

        // 3. Crear Citas Médicas Ficticias
        $doctors = Doctor::with('schedules')->get();
        $patients = Patient::all();
        $statuses = ['programado', 'completado', 'cancelado'];

        // Repartimos unas 30 citas aleatorias en los próximos 14 días
        $today = Carbon::today();
        
        for ($i = 0; $i < 30; $i++) {
            $doctor = $doctors->random();
            $patient = $patients->random();
            $status = $statuses[array_rand($statuses)];
            
            // Elegir una fecha aleatoria dentro de los próximos 14 días (excluyendo Fines de semana)
            $randomDate = $today->copy()->addDays(rand(1, 14));
            while ($randomDate->isWeekend()) {
                $randomDate->addDay();
            }

            // Conseguir un horario de este doctor disponible para ese día
            $dayOfWeek = $randomDate->dayOfWeek; // Carbon 0 (Domingo) - 6 (Sábado), BD 0 (Lunes) - 6 (Domingo)... 
            // Espera, según nuestro modelo/front, Lunes es 1 y Viernes 5. Carbon->dayOfWeek -> Lunes = 1. Coincide.
            
            $availableSchedules = $doctor->schedules->where('day_of_week', $dayOfWeek);
            
            if ($availableSchedules->count() > 0) {
                // Selecciona un bloque aleatorio que este doctor ofreció ese día
                $schedule = $availableSchedules->random();

                // Asegurar que NO existan conflictos en esta fecha+horario
                $conflict = Appointment::where('doctor_id', $doctor->id)
                    ->where('date', $randomDate->format('Y-m-d'))
                    ->where('start_time', $schedule->start_time)
                    ->where('status', '!=', 'cancelado')
                    ->exists();

                if (!$conflict) {
                    Appointment::create([
                        'patient_id' => $patient->id,
                        'doctor_id' => $doctor->id,
                        'date' => $randomDate->format('Y-m-d'),
                        'start_time' => $schedule->start_time,
                        'end_time' => $schedule->end_time,
                        'duration' => 15,
                        'status' => array_rand([1, 2, 3]) + 1,
                        'reason' => fake()->boolean(60) ? fake()->sentence(8) : null,
                    ]);
                }
            }
        }
    }
}
