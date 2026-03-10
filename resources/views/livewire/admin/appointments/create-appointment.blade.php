<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
    <!-- Columna Izquierda: Búsqueda -->
    <div class="lg:col-span-2 space-y-6">
        <!-- Tarjeta de Búsqueda -->
        <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-6">
            <h3 class="text-xl font-bold text-gray-900 mb-1">Buscar disponibilidad</h3>
            <p class="text-sm text-gray-600 mb-6">Encuentra el horario perfecto para tu cita.</p>

            <form wire:submit.prevent="searchAvailability">
                <!-- Validaciones de la rúbrica -->
                @error('date') <span class="text-red-500 text-xs block mb-1 font-bold">{{ $message }}</span> @enderror
                @error('start_time') <span class="text-red-500 text-xs block mb-1 font-bold">{{ $message }}</span> @enderror
                
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-700">Fecha</label>
                        <input type="date" wire:model="date" required 
                            class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block w-full p-2.5">
                    </div>
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-700">Hora</label>
                        <select wire:model="start_time" required
                            class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block w-full p-2.5">
                            <option value="" disabled selected>Selecciona una hora</option>
                            @for ($h=8; $h<=17; $h++)
                                @for ($m=0; $m<60; $m+=15)
                                    @php $time = sprintf('%02d:%02d', $h, $m); @endphp
                                    <option value="{{ $time }}">{{ $time }}</option>
                                @endfor
                            @endfor
                        </select>
                    </div>
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-700">Especialidad (opcional)</label>
                        <select wire:model="speciality_id"
                            class="bg-white border border-gray-300 text-gray-500 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block w-full p-2.5">
                            <option value="">Selecciona una espec...</option>
                            @foreach ($specialities as $spec)
                                <option value="{{ $spec->id }}">{{ $spec->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <button type="submit" wire:loading.attr="disabled" class="w-full text-white bg-indigo-500 hover:bg-indigo-600 focus:ring-4 focus:ring-indigo-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center transition-colors disabled:opacity-50">
                            <span wire:loading.remove wire:target="searchAvailability">Buscar disponibilidad</span>
                            <span wire:loading wire:target="searchAvailability"><i class="fa-solid fa-spinner fa-spin mr-2"></i> Buscando...</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Resultados de Doctores -->
        @if($searched)
            @if(count($availableDoctors) > 0)
                <div class="space-y-4">
                    @foreach($availableDoctors as $doc)
                        <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-6">
                            <div class="flex items-center space-x-4 mb-4">
                                <!-- Avatar Falso o Iniciales -->
                                <div class="w-12 h-12 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 font-bold text-lg">
                                    {{ strtoupper(substr($doc->user->name, 0, 2)) }}
                                </div>
                                <div>
                                    <h4 class="text-lg font-bold text-gray-900">{{ $doc->user->name }}</h4>
                                    <p class="text-sm text-indigo-600">{{ $doc->speciality->name ?? 'Médico General' }}</p>
                                </div>
                            </div>
                            
                            <p class="text-sm font-medium text-gray-700 mb-3">Horarios disponibles:</p>
                            <div class="flex flex-wrap gap-2">
                                <!-- Simulando horarios libres de este doctor -->
                                @php
                                    $timeObj = Carbon\Carbon::parse($start_time);
                                @endphp
                                <button type="button" wire:click="selectSlot({{ $doc->id }}, '{{ $timeObj->format('H:i:s') }}')"
                                        class="px-4 py-2 text-sm font-medium rounded-lg transition-colors border 
                                        @if($selectedDoctor && $selectedDoctor->id === $doc->id && $selectedTime === $timeObj->format('H:i:s'))
                                            bg-indigo-600 border-indigo-600 text-white
                                        @else
                                            bg-indigo-50 border-indigo-200 text-indigo-700 hover:bg-indigo-100
                                        @endif
                                        ">
                                    {{ $timeObj->format('H:i:s') }}
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="bg-white border border-gray-200 rounded-lg p-6 text-center text-gray-500">
                    No hay doctores disponibles para esta fecha y horario.
                </div>
            @endif
        @endif
    </div>

    <!-- Columna Derecha: Resumen de la cita y Formulario Rúbrica -->
    <div class="lg:col-span-1">
        <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-6 sticky top-6">
            <h3 class="text-xl font-bold text-gray-900 mb-6">Resumen de la cita</h3>
            
            <div class="space-y-4 mb-6">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Doctor:</span>
                    <span class="font-medium text-gray-900">{{ $selectedDoctor ? $selectedDoctor->user->name : '-' }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Fecha:</span>
                    <span class="font-medium text-gray-900">{{ $date ? $date : '-' }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Horario:</span>
                    <span class="font-medium text-gray-900">
                        @if($selectedTime)
                            {{ $selectedTime }} - {{ Carbon\Carbon::parse($selectedTime)->addMinutes(15)->format('H:i:s') }}
                        @else
                            -
                        @endif
                    </span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Duración:</span>
                    <span class="font-medium text-gray-900">15 minutos</span>
                </div>
            </div>

            <hr class="border-gray-200 mb-6">

            <div class="space-y-4">
                @error('selection') <span class="text-red-500 text-xs block mb-1 font-bold">{{ $message }}</span> @enderror
                @error('time') <span class="text-red-500 text-xs block mb-1 font-bold">{{ $message }}</span> @enderror

                <!-- Select de Paciente como pide la Rúbrica -->
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900">Paciente</label>
                    <div class="relative">
                        <select wire:model="patient_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block w-full p-2.5">
                            <option value="">Seleccionar paciente</option>
                            @foreach($patients as $patient)
                                <option value="{{ $patient->id }}">{{ $patient->user->name ?? 'Paciente Desconocido' }}</option>
                            @endforeach
                        </select>
                    </div>
                    @error('patient_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <!-- Textarea de Motivo como pide la Rúbrica -->
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900">Motivo de la cita</label>
                    <textarea wire:model="reason" rows="3" placeholder="Ej. Chequeo de medicamentos"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block w-full p-2.5"></textarea>
                    @error('reason') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <div class="pt-2">
                    <button wire:click="bookAppointment" wire:loading.attr="disabled" class="w-full text-white bg-indigo-500 hover:bg-indigo-600 focus:ring-4 focus:ring-indigo-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center transition-colors disabled:opacity-50">
                        Confirmar cita
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
