<?php

namespace App\Livewire\Admin\Appointments;

use Livewire\Component;
use App\Models\Appointment;
use App\Models\Consultation;
use App\Models\Prescription;

class ManageConsultation extends Component
{
    public Appointment $appointment;
    public Consultation $consultation;
    public $activeTab = 'consulta';
    public bool $showHistoryModal = false;
    
    // Consulta
    public $diagnosis = '';
    public $treatment = '';
    public $notes = '';

    // Nueva Receta
    public $medication = '';
    public $dose = '';
    public $frequency = '';

    public function mount(Appointment $appointment)
    {
        $this->appointment = $appointment->load('patient.user', 'doctor.user', 'patient.bloodType');
        
        // Primera vez o cargar existente
        $this->consultation = Consultation::firstOrCreate(
            ['appointment_id' => $appointment->id]
        );

        $this->diagnosis = $this->consultation->diagnosis;
        $this->treatment = $this->consultation->treatment;
        $this->notes = $this->consultation->notes;
    }

    public function toggleHistoryModal()
    {
        $this->showHistoryModal = !$this->showHistoryModal;
    }

    public function setTab($tab)
    {
        $this->activeTab = $tab;
    }

    public function saveConsultation()
    {
        $this->validate([
            'diagnosis' => 'nullable|string',
            'treatment' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $this->consultation->update([
            'diagnosis' => $this->diagnosis,
            'treatment' => $this->treatment,
            'notes' => $this->notes,
        ]);

        // Cambiar el estado de la cita a Completado si guardan la consulta final
        if ($this->diagnosis) {
            $this->appointment->update(['status' => 2]); // 2 = completado
        }

        $this->dispatch('swal:success', [
            'icon' => 'success',
            'title' => 'Guardado',
            'text' => 'Los datos de la consulta han sido guardados.',
        ]);
    }

    public function addPrescription()
    {
        $this->validate([
            'medication' => 'required|string|max:255',
            'dose' => 'required|string|max:255',
            'frequency' => 'required|string|max:255',
        ]);

        $this->consultation->prescriptions()->create([
            'medication' => $this->medication,
            'dose' => $this->dose,
            'frequency' => $this->frequency,
        ]);

        $this->medication = '';
        $this->dose = '';
        $this->frequency = '';

        $this->dispatch('swal:success', [
            'icon' => 'success',
            'title' => 'Medicamento Añadido',
            'text' => 'Se ha agregado exitosamente a la receta.',
        ]);
    }

    public function removePrescription($id)
    {
        Prescription::find($id)->delete();
        $this->dispatch('swal:success', [
            'icon' => 'success',
            'title' => 'Eliminado',
            'text' => 'El medicamento fue retirado.',
        ]);
    }

    public function render()
    {
        return view('livewire.admin.appointments.manage-consultation', [
            'prescriptions' => $this->consultation->fresh()->prescriptions
        ])->layout('layouts.admin');
    }
}
