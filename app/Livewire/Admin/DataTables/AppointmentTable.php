<?php

namespace App\Livewire\Admin\DataTables;

use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\Appointment;
use Illuminate\Database\Eloquent\Builder;

class AppointmentTable extends DataTableComponent
{
    public function builder(): Builder
    {
        return Appointment::query()
            ->with(['patient.user', 'doctor.user']);
    }

    public function configure(): void
    {
        $this->setPrimaryKey('id');
    }

    public function columns(): array
    {
        return [
            Column::make("ID", "id")
                ->sortable(),
            Column::make("Paciente", "patient.user.name")
                ->sortable()
                ->searchable(),
            Column::make("Doctor", "doctor.user.name")
                ->sortable()
                ->searchable(),
            Column::make("Fecha", "date")
                ->sortable(),
            Column::make("Hora", "start_time")
                ->sortable(),
            Column::make("Hora Fin", "end_time")
                ->sortable(),
            Column::make("Estado", "status")
                ->sortable()
                ->format(function ($value) {
                    $statusText = match ($value) {
                        1 => 'Programado',
                        2 => 'Completado',
                        3 => 'Cancelado',
                        default => 'Desconocido',
                    };
                    $color = match ($value) {
                        1 => 'blue',
                        2 => 'green',
                        3 => 'red',
                        default => 'gray',
                    };
                    return view('components.badge', [
                        'color' => $color,
                        'text' => $statusText
                    ]);
                }),
            Column::make("Acciones")
                ->label(function ($row, Column $column) {
                    return view('admin.appointments.actions', ['appointment' => $row]);
                }),
        ];
    }
}
