{{-- 
  Este archivo renderiza el contenido de la columna "Acciones".
  Recibe la variable $role desde el componente RoleTable.
--}}
<div class="flex items-center space-x-2">
    
    {{-- Botón de Editar --}}
    {{-- Se quita el atributo 'icon' y se añade la etiqueta <i> --}}
    <x-wire-button
        color="blue"
        href="{{ route('admin.roles.edit', $role) }}" 
        xs
    >
        <i class="fa-solid fa-pen-to-square"></i>
    </x-wire-button>

    {{-- Botón de Eliminar (usando un formulario) --}}
    <form action="{{ route('admin.roles.destroy', $role) }}" method="POST">
        @csrf
        @method('DELETE')

        {{-- Se quita el atributo 'icon' y se añade la etiqueta <i> --}}
        <x-wire-button
            color="red"
            type="submit"
            xs
        >
            <i class="fa-solid fa-trash-can"></i>
        </x-wire-button>
    </form>

</div>

