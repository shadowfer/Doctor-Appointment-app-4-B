<x-admin-layout 
    title="Editar Cita | MediLink"
    :breadcrumbs="[
        ['name' => 'Dashboard', 'href' => route('admin.dashboard')],
        ['name' => 'Citas', 'href' => route('admin.appointments.index')],
        ['name' => 'Editar'],
    ]">

    <form action="{{ route('admin.appointments.update', $appointment) }}" method="POST">
        @csrf
        @method('PUT')
        
        <x-wire-card class="mb-8">
            <div class="lg:flex lg:justify-between lg:items-center">
                <div class="flex items-center">
                    <div class="ml-4">
                        <p class="text-2xl font-bold text-gray-900 ml-4">Editar Cita #{{ $appointment->id }}</p>
                        <p class="text-sm text-gray-500 ml-4">Paciente: {{ $appointment->patient->user->name }} - Doctor: {{ $appointment->doctor->user->name }}</p>
                    </div>
                </div>
                <div class="flex space-x-3 mt-6 lg:mt-0">
                    <x-wire-button outline gray href="{{ route('admin.appointments.index') }}">Volver</x-wire-button>
                    <x-wire-button type="submit" blue>
                        <i class="fa-solid fa-check mr-2"></i> Guardar cambios
                    </x-wire-button>
                </div>
            </div>
        </x-wire-card>

        <x-wire-card>
            @if (session('error'))
                <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fa-solid fa-circle-exclamation text-red-500"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-red-700">
                                {{ session('error') }}
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            <div class="grid lg:grid-cols-2 gap-4 mb-4">
                {{-- Doctor --}}
                <x-wire-native-select label="Doctor" name="doctor_id" required>
                    <option value="">Selecciona un doctor</option>
                    @foreach ($doctors as $doctor)
                        <option value="{{ $doctor->id }}" @selected(old('doctor_id', $appointment->doctor_id) == $doctor->id)>{{ $doctor->user->name }} ({{ $doctor->speciality->name ?? 'Sin especialidad' }})</option>
                    @endforeach
                </x-wire-native-select>

                {{-- Paciente --}}
                <x-wire-native-select label="Paciente" name="patient_id" required>
                    <option value="">Selecciona un paciente</option>
                    @foreach ($patients as $patient)
                        <option value="{{ $patient->id }}" @selected(old('patient_id', $appointment->patient_id) == $patient->id)>{{ $patient->user->name }}</option>
                    @endforeach
                </x-wire-native-select>
            </div>

            <div class="grid lg:grid-cols-3 gap-4 mb-4">
                {{-- Fecha --}}
                <x-wire-input type="date" label="Fecha" name="date" required value="{{ old('date', $appointment->date) }}"></x-wire-input>

                {{-- Hora Inicio --}}
                <x-wire-input type="time" label="Hora de Inicio" name="start_time" required value="{{ old('start_time', \Carbon\Carbon::parse($appointment->start_time)->format('H:i')) }}"></x-wire-input>

                {{-- Hora Fin --}}
                <x-wire-input type="time" label="Hora de Fin" name="end_time" required value="{{ old('end_time', \Carbon\Carbon::parse($appointment->end_time)->format('H:i')) }}"></x-wire-input>
            </div>

            <div class="mb-4">
                {{-- Estado --}}
                <x-wire-native-select label="Estado de la Cita" name="status" required>
                    <option value="programado" @selected(old('status', $appointment->status) == 'programado')>Programado</option>
                    <option value="completado" @selected(old('status', $appointment->status) == 'completado')>Completado</option>
                    <option value="cancelado" @selected(old('status', $appointment->status) == 'cancelado')>Cancelado</option>
                </x-wire-native-select>
            </div>

            <div class="mb-4">
                {{-- Notas --}}
                <x-wire-textarea label="Notas Médicas" name="notes" placeholder="Agrega notas o instrucciones para esta cita...">{{ old('notes', $appointment->notes) }}</x-wire-textarea>
            </div>

        </x-wire-card>
    </form>
</x-admin-layout>
