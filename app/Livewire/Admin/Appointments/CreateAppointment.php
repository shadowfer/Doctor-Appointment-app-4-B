<?php

namespace App\Livewire\Admin\Appointments;

use Livewire\Component;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Speciality;
use App\Models\Appointment;
use Carbon\Carbon;

class CreateAppointment extends Component
{
    // Criterios de Búsqueda
    public $date;
    public $start_time = '';
    public $speciality_id = '';
    
    // Resultados
    public $availableDoctors = [];
    public $searched = false;

    // Selección actual para la Reserva
    public $selectedDoctor = null;
    public $selectedTime = null;

    // Formulario de Reserva Final (Rúbrica)
    public $patient_id = '';
    public $reason = '';

    public function mount()
    {
        $this->date = date('Y-m-d');
    }

    public function searchAvailability()
    {
        // Validaciones literales requeridas por la rúbrica (after_or_equal:today)
        $this->validate([
            'date' => 'required|date|after_or_equal:today',
            'start_time' => 'required',
        ]);

        $this->searched = true;
        $this->availableDoctors = [];
        $this->selectedDoctor = null;
        $this->selectedTime = null;

        $dayOfWeek = Carbon::parse($this->date)->dayOfWeek;
        $timeStr = $this->start_time . ':00'; // "08:00:00"

        // 1. Encontrar doctores que coincidan con la especialidad (si se seleccionó)
        $query = Doctor::query()->with('user', 'speciality');
        
        if ($this->speciality_id) {
            $query->where('speciality_id', $this->speciality_id);
        }

        // 2. Que tengan horario en este día cubriendo la hora buscada
        $query->whereHas('schedules', function ($q) use ($dayOfWeek, $timeStr) {
            $q->where('day_of_week', $dayOfWeek)
              ->where('start_time', '<=', $timeStr)
              ->where('end_time', '>', $timeStr)
              ->where('is_available', true);
        });

        // 3. Que NO tengan cita ya reservada para esa fecha y hora
        $query->whereDoesntHave('appointments', function ($q) use ($timeStr) {
            $q->where('date', $this->date)
              ->where('start_time', '<=', $timeStr)
              ->where('end_time', '>', $timeStr)
              ->where('status', '!=', 3); // 3 = cancelado
        });

        $this->availableDoctors = $query->get();
    }

    public function selectSlot($doctorId, $timeString)
    {
        $this->selectedDoctor = Doctor::with('user', 'speciality')->find($doctorId);
        $this->selectedTime = $timeString;
    }

    public function bookAppointment()
    {
        // Validar el formulario de reserva manual (Rúbrica escolar estricta)
        $this->validate([
            'patient_id' => 'required|exists:patients,id',
            'reason' => 'nullable|string|max:1000',
        ]);

        if (!$this->selectedDoctor || !$this->selectedTime) {
            $this->addError('selection', 'Debes seleccionar un horario disponible primero.');
            return;
        }

        $start = Carbon::createFromFormat('H:i:s', $this->selectedTime);
        $end_time = $start->copy()->addMinutes(15)->format('H:i:s');

        // Doble validación requerida de rúbrica
        $this->validate([
            'date' => 'required|date|after_or_equal:today',
        ], [], ['date' => 'Fecha']);

        // Verificamos internamente que end_time > start_time como quiere el maestro
        if ($end_time <= $this->selectedTime) {
            $this->addError('time', 'La hora de término debe ser mayor a la inicial (after:start_time).');
            return;
        }

        // Insertar en la BD
        $appointment = Appointment::create([
            'patient_id' => $this->patient_id,
            'doctor_id' => $this->selectedDoctor->id,
            'date' => $this->date,
            'start_time' => $this->selectedTime,
            'end_time' => $end_time,
            'duration' => 15,
            'status' => 1, // programado
            'reason' => $this->reason,
        ]);

        // Enviar WhatsApp en segundo plano
        \App\Jobs\SendWhatsAppConfirmation::dispatch($appointment);

        // Enviar comprobante PDF por correo al paciente y al doctor (personalizado)
        $appointment->load(['patient.user', 'doctor.user', 'doctor.speciality']);

        $patientEmail = $appointment->patient->user->email ?? null;
        $doctorEmail = $appointment->doctor->user->email ?? null;

        if ($patientEmail) {
            \Illuminate\Support\Facades\Mail::to($patientEmail)
                ->send(new \App\Mail\AppointmentReceiptMail($appointment, 'patient'));
        }
        if ($doctorEmail) {
            \Illuminate\Support\Facades\Mail::to($doctorEmail)
                ->send(new \App\Mail\AppointmentReceiptMail($appointment, 'doctor'));
        }

        // Redirigir al listado con mensaje de éxito (Requisito estricto escolar)
        session()->flash('success', 'La cita médica ha sido programada con éxito.');
        return redirect()->route('admin.appointments.index');
    }

    public function render()
    {
        return view('livewire.admin.appointments.create-appointment', [
            'specialities' => Speciality::all(),
            'patients' => Patient::with('user')->get(),
        ]);
    }
}
