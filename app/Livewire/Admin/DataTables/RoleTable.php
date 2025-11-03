<?php

namespace App\Livewire\Admin\DataTables;

use Rappasoft\LaravelLivewireTables\Views\Column;
use Spatie\Permission\Models\Role;
use Rappasoft\LaravelLivewireTables\DataTableComponent;

class RoleTable extends DataTableComponent
{
    protected $model = Role::class;

    public function configure(): void
    {
        $this->setPrimaryKey('id');
    }

    public function columns(): array
    {
        return [
            Column::make("Id", "id")
                ->sortable(),
            Column::make("Name", "name")
                ->sortable()
                ->searchable(), // Añadido para buscar

            // --- INICIO DE LA MODIFICACIÓN (ADA 1) ---
            
            // 1. Nueva columna "Acciones"
            Column::make("Acciones")
                ->label(
                    // 2. Carga la vista 'actions.blade.php' por cada fila ($row)
                    fn($row) => view('admin.roles.actions', ['role' => $row])
                ),
            
            // --- FIN DE LA MODIFICACIÓN ---

            Column::make("Created at", "created_at")
                ->sortable(),
        ];
    }
}
