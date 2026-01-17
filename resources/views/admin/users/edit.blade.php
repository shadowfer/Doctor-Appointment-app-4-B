<x-admin-layout title="Editar Usuario | MediLink" :breadcrumbs="[
    [
        'name' => 'Dashboard',
        'href' => route('admin.dashboard')
    ],
    [
        'name' => 'Usuarios',
        'href' => route('admin.users.index')
    ],
    [
        'name' => 'Editar'
    ],
]">

<x-wire-card>
    <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
    @csrf
    @method('put')
    <div class="space-y-4">
    <div class="grid lg:grid-cols-2 gap-4">
        <x-wire-input name="name" label="Nombre" required value="{{ old('name', $user->name) }}" placeholder="Nombre" autocomplete="name"></x-wire-input>
        <x-wire-input name="email" label="Correo Electrónico" type="email" required value="{{ old('email', $user->email) }}" placeholder="Usuario@gmail.com" autocomplete="email" inputmode="email"></x-wire-input>
        <x-wire-input name="password" label="Contraseña" type="password" value="{{ old('password') }}" placeholder="Minimo 8 caracteres" autocomplete="new-password" inputmode="password"></x-wire-input>
        <x-wire-input name="password_confirmation" label="Confirmar Contraseña" type="password" value="{{ old('password_confirmation') }}" placeholder="Confirmar Contraseña" autocomplete="new-password" inputmode="password"></x-wire-input>
        <x-wire-input name="id_number" label="Número de Identificación" required value="{{ old('id_number', $user->id_number) }}" placeholder="Ej. 12937129047" autocomplete="off" inputmode="numeric"></x-wire-input>
        <x-wire-input name="phone" label="Teléfono" required value="{{ old('phone', $user->phone) }}" placeholder="Ej. 3001234567" autocomplete="off" inputmode="tel"></x-wire-input>
        </div>
        <x-wire-input name="address" label="Dirección" required value="{{ old('address', $user->address) }}" placeholder="Ej. Calle 123" autocomplete="Street-address"></x-wire-input>
    </div>
    <div class="space-y-1">
        <x-wire-native-select name="role_id" label="Rol" required>
        <option value="" disabled selected>Seleccione un rol</option>
        @foreach ($roles as $role)
            <option value="{{ $role->id }}" @selected(old('role_id', $user->roles->first()?->id) == $role->id)>{{ $role->name }}</option>
        @endforeach
        </x-wire-native-select>

        <p class="text-sm text-gray-500">
        Define los permisos y accesos del usuario.
        </p>

        <div class="flex justify-end">
        <x-wire-button type="submit">
            Actualizar
        </x-wire-button>  

    </div>
    </form>

</x-wire-card>

</x-admin-layout>