<x-admin-layout 
    title="Editar Doctor | MediLink"
    :breadcrumbs="[
        ['name' => 'Dashboard', 'href' => route('admin.dashboard')],
        ['name' => 'Doctores', 'href' => route('admin.doctors.index')],
        ['name' => 'Editar'],
    ]">

    <form action="{{ route('admin.doctors.update', $doctor) }}" method="POST">
        @csrf
        @method('PUT')
        
        {{-- Encabezado con información del doctor --}}
        <x-wire-card class="mb-8">
            <div class="lg:flex lg:justify-between lg:items-center">
                <div class="flex items-center">
                    <img src="{{ $doctor->user->profile_photo_url }}" alt="{{ $doctor->user->name }}"
                        class="h-20 w-20 rounded-full object-cover object-center">
                    <div class="ml-4">
                        <p class="text-2xl font-bold text-gray-900 ml-4">{{ $doctor->user->name }}</p>
                        <p class="text-sm text-gray-500 ml-4">Licencia: {{ $doctor->medical_license_number ?? 'N/A' }}</p>
                    </div>
                </div>
                <div class="flex space-x-3 mt-6 lg:mt-0">
                    <x-wire-button outline gray href="{{ route('admin.doctors.index') }}">Volver</x-wire-button>
                    <x-wire-button type="submit">
                        <i class="fa-solid fa-check mr-2"></i> Guardar cambios
                    </x-wire-button>
                </div>
            </div>
        </x-wire-card>

        {{-- Formulario de edición sin pestañas --}}
        <x-wire-card>
            <div class="space-y-4">
                {{-- Especialidad --}}
                <x-wire-native-select label="Especialidad" name="speciality_id">
                    <option value="">Selecciona una especialidad</option>
                    @foreach ($specialities as $speciality)
                        <option value="{{ $speciality->id }}" @selected(old('speciality_id', $doctor->speciality_id) == $speciality->id)>{{ $speciality->name }}</option>
                    @endforeach
                </x-wire-native-select>

                {{-- Número de licencia médica --}}
                <x-wire-input 
                    label="Número de licencia médica" 
                    name="medical_license_number" 
                    placeholder="Ej. 23568349" 
                    value="{{ old('medical_license_number', $doctor->medical_license_number) }}">
                </x-wire-input>

                {{-- Biografía --}}
                <x-wire-textarea 
                    label="Biografía" 
                    name="biography" 
                    placeholder="Escribe aquí la biografía del doctor...">{{ old('biography', $doctor->biography) }}</x-wire-textarea>
            </div>
        </x-wire-card>
    </form>
</x-admin-layout>
