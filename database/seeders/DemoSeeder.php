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

        // 3. SEGURO: Cada paciente DEBE tener al menos una cita completada en el pasado con su consulta y receta
        $doctors = Doctor::with('schedules')->get();
        $patients = Patient::all();
        $today = Carbon::today();

        $diagnosticos = [
            'Hipertensión arterial esencial controlada.',
            'Infección respiratoria aguda de vías superiores.',
            'Diabetes Mellitus tipo 2 en tratamiento.',
            'Gastroenteritis viral aguda.',
            'Lumbalgia mecánica inespecífica.',
            'Control de niño sano sin hallazgos patológicos.',
            'Anemia ferropénica leve.',
            'Dermatitis atópica en fase eccematosa.',
            'Rinitis alérgica estacional.',
            'Migraña con aura controlada.'
        ];

        $tratamientos = [
            'Reposo absoluto por 3 días y abundante hidratación.',
            'Continuar esquema actual de medicación y dieta hiposódica.',
            'Iniciar tratamiento antibiótico vía oral por 7 días.',
            'Terapia física 2 veces por semana y analgésicos.',
            'Seguimiento estricto de niveles de glucosa capilar.',
            'Aplicar crema hidratante 3 veces al día en zona afectada.',
            'Uso de antihistamínicos según necesidad y evitar alérgenos.',
            'Dieta rica en fibra y aumento en el consumo de líquidos.',
            'Ejercicio aeróbico moderado 30 minutos al día.',
            'Evitar cambios bruscos de temperatura y ambientes polvorientos.'
        ];

        $notas = [
            'Paciente se muestra colaborador.',
            'Se recomienda cita de seguimiento en 15 días.',
            'Se solicita perfil lipídico completo.',
            'Evolución favorable del cuadro inicial.',
            'Pendiente de resultados de laboratorio.',
            'Se requiere interconsulta con nutrición.',
            'Mantener monitoreo de presión arterial en casa.',
            'Paciente refiere mejoría en la sintomatología.'
        ];

        foreach ($patients as $patient) {
            $doctor = $doctors->random();
            $pastDate = Carbon::today()->subDays(rand(1, 10)); // Hace 1 a 10 días
            
            // Buscar un horario cualquiera del doctor para ese día
            $dayOfWeek = $pastDate->dayOfWeek;
            $schedule = $doctor->schedules->where('day_of_week', $dayOfWeek)->first() 
                        ?? $doctor->schedules->first(); // fallback si no trabaja ese día
            
            if ($schedule) {
                $appointment = Appointment::create([
                    'patient_id' => $patient->id,
                    'doctor_id' => $doctor->id,
                    'date' => $pastDate->format('Y-m-d'),
                    'start_time' => $schedule->start_time,
                    'end_time' => $schedule->end_time,
                    'duration' => 15,
                    'status' => 2, // COMPLETADO
                    'reason' => 'Consulta de control rutinario',
                ]);

                $consultation = \App\Models\Consultation::create([
                    'appointment_id' => $appointment->id,
                    'diagnosis' => $diagnosticos[array_rand($diagnosticos)],
                    'treatment' => $tratamientos[array_rand($tratamientos)],
                    'notes' => $notas[array_rand($notas)],
                ]);

                for ($j = 0; $j < 2; $j++) {
                    \App\Models\Prescription::create([
                        'consultation_id' => $consultation->id,
                        'medication' => $faker->randomElement(['Amoxicilina', 'Paracetamol', 'Vitamina C', 'Ibuprofeno', 'Naproxeno', 'Loratadina', 'Omeprazol']) . ' ' . rand(100, 500) . 'mg',
                        'dose' => '1 ' . $faker->randomElement(['tableta', 'cápsula', 'cucharada']),
                        'frequency' => 'Cada ' . rand(4, 12) . ' horas por ' . rand(3, 10) . ' días',
                    ]);
                }
            }
        }

        // 4. Crear Citas Médicas Aleatorias adicionales para el calendario (próximos días)
        for ($i = 0; $i < 50; $i++) {
            $doctor = $doctors->random();
            $patient = $patients->random();
            $status = array_rand([1, 2, 3]) + 1; // 1: Programado, 2: Completado, 3: Cancelado
            
            // Elegir una fecha aleatoria dentro de los próximos 14 días (excluyendo Fines de semana)
            $randomDate = $today->copy()->addDays(rand(1, 14));
            while ($randomDate->isWeekend()) {
                $randomDate->addDay();
            }

            $dayOfWeek = $randomDate->dayOfWeek;
            $availableSchedules = $doctor->schedules->where('day_of_week', $dayOfWeek);
            
            if ($availableSchedules->count() > 0) {
                $schedule = $availableSchedules->random();

                // Evitar conflictos
                $conflict = Appointment::where('doctor_id', $doctor->id)
                    ->where('date', $randomDate->format('Y-m-d'))
                    ->where('start_time', $schedule->start_time)
                    ->where('status', '!=', 3)
                    ->exists();

                if (!$conflict) {
                    $appointment = Appointment::create([
                        'patient_id' => $patient->id,
                        'doctor_id' => $doctor->id,
                        'date' => $randomDate->format('Y-m-d'),
                        'start_time' => $schedule->start_time,
                        'end_time' => $schedule->end_time,
                        'duration' => 15,
                        'status' => $status,
                        'reason' => $faker->boolean(60) ? 'Paciente refiere molestias generales persistentes' : 'Seguimiento post-operatorio',
                    ]);

                    if ($appointment->status === 2) {
                        $consultation = \App\Models\Consultation::create([
                            'appointment_id' => $appointment->id,
                            'diagnosis' => $diagnosticos[array_rand($diagnosticos)],
                            'treatment' => $tratamientos[array_rand($tratamientos)],
                            'notes' => $notas[array_rand($notas)],
                        ]);

                        for ($j = 0; $j < rand(1, 3); $j++) {
                            \App\Models\Prescription::create([
                                'consultation_id' => $consultation->id,
                                'medication' => $faker->randomElement(['Omeprazol', 'Loratadina', 'Ibuprofeno', 'Metformina', 'Enalapril', 'Atorvastatina']) . ' ' . rand(10, 500) . 'mg',
                                'dose' => '1 ' . $faker->randomElement(['tableta', 'cápsula', 'cucharada']),
                                'frequency' => 'Cada ' . rand(4, 24) . ' horas según indicación',
                            ]);
                        }
                    }
                }
            }
        }
    }
}
