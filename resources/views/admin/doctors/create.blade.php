<x-admin-layout 
    title="Doctores | MediLink"
    :breadcrumbs="[
        [
            'name' => 'Dashboard',
            'href' => route('admin.dashboard'),
        ],
        [
            'name' => 'Doctores',
            'href' => route('admin.doctors.index'),
        ],
        [
            'name' => 'Nuevo Doctor',
        ],
    ]">

</x-admin-layout>
