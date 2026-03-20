<?php

namespace App\Http\Controllers\Admin;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Speciality;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Jobs\SendWhatsAppConfirmation;

class AppointmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.appointments.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $specialities = Speciality::all();
        $patients = Patient::with('user')->get();
        return view('admin.appointments.create', compact('specialities', 'patients'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'doctor_id' => 'required|exists:doctors,id',
            'date' => 'required|date|after_or_equal:today',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'notes' => 'nullable|string|max:1000',
        ]);

        // Verificar conflictos
        $conflict = Appointment::where('doctor_id', $data['doctor_id'])
            ->where('date', $data['date'])
            ->where(function ($query) use ($data) {
                $query->whereBetween('start_time', [$data['start_time'], $data['end_time']])
                    ->orWhereBetween('end_time', [$data['start_time'], $data['end_time']])
                    ->orWhere(function ($query) use ($data) {
                        $query->where('start_time', '<=', $data['start_time'])
                              ->where('end_time', '>=', $data['end_time']);
                    });
            })
            ->where('status', '!=', 'cancelado')
            ->exists();

        if ($conflict) {
            return back()->withInput()->with('error', 'El doctor ya tiene una cita programada en ese horario.');
        }

        $appointment = Appointment::create($data);
        SendWhatsAppConfirmation::dispatch($appointment);

        session()->flash('swal', [
            'icon' => 'success',
            'title' => 'Cita creada',
            'text' => 'La cita se ha programado correctamente.',
            
        ]);

        return redirect()->route('admin.appointments.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Appointment $appointment)
    {
        return view('admin.appointments.show', compact('appointment'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Appointment $appointment)
    {
        $specialities = Speciality::all();
        $patients = Patient::with('user')->get();
        // Cargar los doctores si la especialidad está seleccionada (para el frontend o Livewire)
        $doctors = Doctor::with('user')->get(); 

        return view('admin.appointments.edit', compact('appointment', 'specialities', 'patients', 'doctors'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Appointment $appointment)
    {
        $data = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'doctor_id' => 'required|exists:doctors,id',
            'date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'status' => 'required|integer|in:1,2,3',
            'notes' => 'nullable|string|max:1000',
        ]);

        // Verificar conflictos excluyendo la propia cita
        // 1 significa 'programado' en esta base de datos
        if ((int)$data['status'] === 1) {
            $conflict = Appointment::where('doctor_id', $data['doctor_id'])
                ->where('date', $data['date'])
                ->where('id', '!=', $appointment->id)
                ->where(function ($query) use ($data) {
                    $query->whereBetween('start_time', [$data['start_time'], $data['end_time']])
                        ->orWhereBetween('end_time', [$data['start_time'], $data['end_time']])
                        ->orWhere(function ($query) use ($data) {
                            $query->where('start_time', '<=', $data['start_time'])
                                ->where('end_time', '>=', $data['end_time']);
                        });
                })
                ->where('status', '!=', 'cancelado')
                ->exists();

            if ($conflict) {
                return back()->withInput()->with('error', 'El doctor ya tiene una cita programada en ese horario.');
            }
        }

        $appointment->update($data);

        session()->flash('swal', [
            'icon' => 'success',
            'title' => 'Cita actualizada',
            'text' => 'La información de la cita ha sido actualizada.',
        ]);

        return redirect()->route('admin.appointments.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Appointment $appointment)
    {
        $appointment->delete();

        session()->flash('swal', [
            'icon' => 'success',
            'title' => 'Cita eliminada',
            'text' => 'La cita ha sido eliminada correctamente.',
        ]);

        return redirect()->route('admin.appointments.index');
    }
}
