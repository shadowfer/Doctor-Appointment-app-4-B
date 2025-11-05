<x-admin-layout :breadcrumbs="[
    [
        'name' => 'Dashboard',
        'href' => route('admin.dashboard')
    ],
    [
        'name' => 'Principal',
        'href' => route('admin.dashboard')
    ],
    [
        'name' => 'Configuracion',
    ],
]">
    Hola desde el dashboard de admin
</x-admin-layout>