<x-admin-layout 
    title="Pacientes | MediLink"
    :breadcrumbs="[
        [
            'name' => 'Dashboard',
            'href' => route('admin.dashboard'),
        ],
        [
            'name' => 'Pacientes',
            'href' => route('admin.patients.index'),
        ],
        [
            'name' => 'Nuevo Paciente',
        ],
    ]">

</x-admin-layout>