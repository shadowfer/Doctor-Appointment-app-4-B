<div class="flex items-center space-x-2">
    <x-wire-button href="{{ route('admin.doctors.edit', $doctor) }}" blue xs>
        <i class="fa-solid fa-pen-to-square"></i>
    </x-wire-button>
    <!-- Botón de Horario / Disponibilidad Académico -->
    <a href="{{ route('admin.doctors.schedules', $doctor->id) }}" class="inline-flex items-center px-3 py-2 text-sm font-medium text-center text-white bg-green-500 rounded-lg hover:bg-green-600 focus:ring-4 focus:ring-green-300 dark:focus:ring-green-900" title="Gestionar Horarios">
        <i class="fa-solid fa-clock"></i>
    </a>
</div>
