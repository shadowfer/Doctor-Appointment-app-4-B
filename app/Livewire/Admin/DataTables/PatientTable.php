<?php

namespace App\Livewire\Admin\Datatables;

use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\Patient;
use Illuminate\Database\Eloquent\Builder;
use Symfony\Component\Mime\Email;

class PatientTable extends DataTableComponent
{
    //protected $model = Patient::class;

    public function builder(): Builder
    {
        return Patient::query()
            ->with('user');
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
            Column::make("Phone", "user.phone")
                ->sortable(),
            Column::make("Numero de id", "user.id_number")
                ->sortable(),
            Column::make("Acciones")
                ->label(function($row) {
                    return view('admin.patients.actions', ['patient' => $row]);
                }),








            //Column::make("Allergies", "allergies")
               // ->sortable(),
            //Column::make("Chronic conditions", "chronic_conditions")
              //  ->sortable(),
            //Column::make("Surgical history", "surgical_history")
              //  ->sortable(),
            //Column::make("Family history", "family_history")
              //  ->sortable(),
            //Column::make("Observations", "observations")
              //  ->sortable(),
            //Column::make("Emergency contact name", "emergency_contact_name")
              //  ->sortable(),
            //Column::make("Emergency contact phone", "emergency_contact_phone")
              //  ->sortable(),
            //Column::make("Emergency contact relationship", "emergency_contact_relationship")
            //    ->sortable(),
            //Column::make("Created at", "created_at")
              //  ->sortable(),
            //Column::make("Updated at", "updated_at")
               // ->sortable(),
        ];
    }
}
