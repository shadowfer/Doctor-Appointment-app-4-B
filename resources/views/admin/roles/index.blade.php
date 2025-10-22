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

    @livewire('admin.data-tables.role-table')

</x-admin-layout>