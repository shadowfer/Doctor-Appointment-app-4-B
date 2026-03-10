<div class="flex items-center space-x-2">
    <x-wire-button href="{{ route('admin.appointments.edit', $appointment) }}" blue xs>
        <i class="fa-solid fa-pen-to-square"></i>
    </x-wire-button>
    
    <!-- Consulta Médica (Botón Verde de Acción) -->
    <a href="{{ route('admin.appointments.consultation', $appointment) }}" class="inline-flex items-center px-2 py-1 text-xs font-medium text-center text-white bg-green-500 rounded hover:bg-green-600 focus:ring-2 focus:ring-green-300 dark:focus:ring-green-900" title="Datos de la consulta">
        <i class="fa-solid fa-file-medical"></i>
    </a>
    
    <form action="{{ route('admin.appointments.destroy', $appointment) }}" method="POST" id="delete-form-{{ $appointment->id }}" class="inline-block">
        @csrf
        @method('DELETE')
        <x-wire-button color="red" xs onclick="if(confirm('¿Estás seguro de que deseas eliminar esta cita?')) document.getElementById('delete-form-{{ $appointment->id }}').submit(); return false;">
            <i class="fa-solid fa-trash"></i>
        </x-wire-button>
    </form>
</div>
