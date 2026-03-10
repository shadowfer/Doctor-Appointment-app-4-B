<?php

namespace App\Http\Controllers\Admin;

use App\Models\Doctor;
use App\Http\Controllers\Controller;
use App\Models\Speciality;
use Illuminate\Http\Request;

class DoctorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.doctors.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.doctors.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Doctor $doctor)
    {
        return view('admin.doctors.show', compact('doctor'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Doctor $doctor)
    {
        $specialities = Speciality::all();
        return view('admin.doctors.edit', compact('doctor', 'specialities'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Doctor $doctor)
    {
        $data = $request->validate([
            'speciality_id' => 'nullable|exists:specialities,id',
            'medical_license_number' => 'nullable|string|min:3|max:255',
            'biography' => 'nullable|string|min:3|max:1000',
        ]);

        $doctor->update($data);

        session()->flash('swal', [
            'icon' => 'success',
            'title' => 'Doctor actualizado',
            'text' => 'La información del doctor ha sido actualizada correctamente.',
        ]);

        return redirect()->route('admin.doctors.edit', $doctor)->with('success', 'Doctor actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Doctor $doctor)
    {
        //
    }

    /**
     * Gestión de horarios desde dentro del controlador de Doctor requerido académicamente
     */
    public function schedules(Doctor $doctor)
    {
        $days = [
            1 => 'Lunes',
            2 => 'Martes',
            3 => 'Miércoles',
            4 => 'Jueves',
            5 => 'Viernes',
            6 => 'Sábado',
            0 => 'Domingo',
        ];

        $schedules = $doctor->schedules()->get();

        return view('admin.doctors.schedules', compact('doctor', 'days', 'schedules'));
    }

    public function updateSchedules(Request $request, Doctor $doctor)
    {
        $request->validate([
            'schedules' => 'nullable|array',
        ]);

        \DB::beginTransaction();
        try {
            // Eliminar horarios anteriores
            $doctor->schedules()->delete();

            $schedulesInput = $request->input('schedules', []);

            foreach ($schedulesInput as $idx => $data) {
                // Solo procesamos si el checkbox envió el day_of_week (los hidden están sincronizados)
                if (isset($data['day_of_week'], $data['start_time'], $data['end_time'])) {
                    $doctor->schedules()->create([
                        'day_of_week' => $data['day_of_week'],
                        'start_time' => $data['start_time'],
                        'end_time' => $data['end_time'],
                        'is_available' => true,
                    ]);
                }
            }

            \DB::commit();

            session()->flash('swal', [
                'icon' => 'success',
                'title' => 'Horarios actualizados',
                'text' => 'La disponibilidad del doctor ha sido guardada correctamente.',
            ]);

            return redirect()->route('admin.doctors.index');
        } catch (\Exception $e) {
            \DB::rollBack();
            return back()->with('error', 'Ocurrió un error guardando los horarios: ' . $e->getMessage());
        }
    }
}
