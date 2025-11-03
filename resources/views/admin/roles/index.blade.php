<x-admin-layout
    title="Roles | MediLink"
    :breadcrumbs="[
        ['name' => 'Dashboard', 
         'route' => route('admin.dashboard')
    ],
        [
            'name' => 'Roles',
        ],
    ]">

    {{-- --- INICIO DE LA MODIFICACIÓN (ADA 1) --- --}}
    {{-- Slot para el botón "Nuevo" --}}
    @slot('action')
        <x-wire-button
            color="blue"
            href="{{ route('admin.roles.create') }}"
        >
            <i class="fa-solid fa-plus mr-1"></i>
            Nuevo
        </x-wire-button>
    @endslot
    {{-- --- FIN DE LA MODIFICACIÓN --- --}}


    {{-- Esta línea ya la tenías correcta --}}
    @livewire('admin.data-tables.role-table')

</x-admin-layout>

