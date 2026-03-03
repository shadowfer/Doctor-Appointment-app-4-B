<?php

namespace App\Livewire\Admin\Datatables;

use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\Doctor;
use Illuminate\Database\Eloquent\Builder;

class DoctorTable extends DataTableComponent
{
    public function builder(): Builder
    {
        return Doctor::query()
            ->with('user', 'speciality');
    }

    public function configure(): void
    {
        $this->setPrimaryKey('id');
    }

    public function columns(): array
    {
        return [
            Column::make("Id", "id")
                ->sortable(),

            Column::make("Nombre", "user.name")
                ->sortable(),

            Column::make("Email", "user.email")
                ->sortable(),

            Column::make("Especialidad", "speciality.name")
                ->sortable(),

            Column::make("Cédula Profesional", "medical_license_number")
                ->sortable()
                ->format(function ($value) {
                    return $value ?? 'N/A';
                }),

            Column::make("Biografía", "biography")
                ->sortable()
                ->format(function ($value) {
                    if ($value) {
                        return \Illuminate\Support\Str::limit($value, 50);
                    }
                    return 'N/A';
                }),

            Column::make("Acciones")
                ->label(function($row) {
                    return view('admin.doctors.actions', ['doctor' => $row]);
                }),
        ];
    }
}
