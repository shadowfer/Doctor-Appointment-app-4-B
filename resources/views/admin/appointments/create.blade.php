<x-admin-layout 
    title="Nueva Cita | MediLink"
    :breadcrumbs="[
        ['name' => 'Dashboard', 'href' => route('admin.dashboard')],
        ['name' => 'Citas', 'href' => route('admin.appointments.index')],
        ['name' => 'Nuevo'],
    ]">

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Nuevo</h1>
    </div>

    @livewire('admin.appointments.create-appointment')

</x-admin-layout>
