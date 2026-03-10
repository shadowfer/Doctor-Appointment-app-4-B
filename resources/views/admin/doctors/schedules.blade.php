<x-admin-layout 
    title="Gestor de Horarios | MediLink"
    :breadcrumbs="[
        ['name' => 'Dashboard', 'href' => route('admin.dashboard')],
        ['name' => 'Doctores', 'href' => route('admin.doctors.index')],
        ['name' => 'Horarios'],
    ]">

    <form action="{{ route('admin.doctors.schedules.update', $doctor) }}" method="POST">
        @csrf
        @method('PUT')
        
        <h1 class="text-2xl font-bold mb-6 text-gray-800">Gestionar Horarios de {{ $doctor->user->name }}</h1>
        <div class="mb-6">
            <x-wire-card title="Disponibilidad Semanal">
                <p class="text-sm text-gray-500 mb-4">Selecciona los bloques de 15 minutos en los que el doctor estará disponible para consultas médicas.</p>
            </x-wire-card>
        </div>

        <x-wire-card class="mb-8">
            <div class="lg:flex lg:justify-between lg:items-center">
                <div class="flex items-center">
                    <img src="{{ $doctor->user->profile_photo_url }}" alt="{{ $doctor->user->name }}"
                        class="h-16 w-16 rounded-full object-cover object-center">
                    <div class="ml-4">
                        <p class="text-xl font-bold text-gray-900 ml-4">Gestor de horarios: {{ $doctor->user->name }}</p>
                        <p class="text-sm text-gray-500 ml-4">Especialidad: {{ $doctor->speciality->name ?? 'N/A' }}</p>
                    </div>
                </div>
                <div class="flex space-x-3 mt-6 lg:mt-0">
                    <x-wire-button outline gray href="{{ route('admin.doctors.index') }}">Volver</x-wire-button>
                    <x-wire-button type="submit" blue>
                        <i class="fa-solid fa-save mr-2"></i> Guardar horario
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

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-500">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="px-6 py-3">DÍA/HORA</th>
                            <th scope="col" class="px-6 py-3">LUNES (1)</th>
                            <th scope="col" class="px-6 py-3">MARTES (2)</th>
                            <th scope="col" class="px-6 py-3">MIÉRCOLES (3)</th>
                            <th scope="col" class="px-6 py-3">JUEVES (4)</th>
                            <th scope="col" class="px-6 py-3">VIERNES (5)</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- Generar bloques de horas desde las 08:00 hasta las 18:00 --}}
                        @for ($hour = 8; $hour <= 17; $hour++)
                            @php
                                $mainHour = str_pad($hour, 2, '0', STR_PAD_LEFT) . ':00:00';
                            @endphp
                            <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                <td class="px-6 py-4 font-bold text-gray-900 whitespace-nowrap dark:text-white align-top">
                                    {{ str_pad($hour, 2, '0', STR_PAD_LEFT) }}:00
                                </td>
                                
                                {{-- Columnas para Lunes(1) a Viernes(5) --}}
                                @for ($day = 1; $day <= 5; $day++)
                                    <td class="px-6 py-4 align-top">
                                        <div class="space-y-2">
                                            {{-- Generar 4 bloques de 15 minutos en esta hora --}}
                                            @for ($min = 0; $min < 60; $min += 15)
                                                @php
                                                    $startTimeStr = str_pad($hour, 2, '0', STR_PAD_LEFT) . ':' . str_pad($min, 2, '0', STR_PAD_LEFT) . ':00';
                                                    
                                                    // Calcular end time
                                                    $endHour = $hour;
                                                    $endMin = $min + 15;
                                                    if ($endMin >= 60) {
                                                        $endMin -= 60;
                                                        $endHour++;
                                                    }
                                                    $endTimeStr = str_pad($endHour, 2, '0', STR_PAD_LEFT) . ':' . str_pad($endMin, 2, '0', STR_PAD_LEFT) . ':00';
                                                    
                                                    // Verificar si este bloque existe en la base de datos para este doctor
                                                    $isChecked = current($schedules->filter(function($s) use ($day, $startTimeStr) {
                                                        return $s->day_of_week == $day && $s->start_time == $startTimeStr;
                                                    })->toArray());
                                                    
                                                    // Solo se marca por defecto si existía o si mandó un error pero estaba chequeado (old)
                                                    $idx = "{$day}_{$startTimeStr}";
                                                @endphp
                                                
                                                <div class="flex items-center">
                                                    <input type="checkbox" 
                                                        aria-label="Bloque {{ $startTimeStr }} - {{ $endTimeStr }}"
                                                        name="schedules[{{ $idx }}][day_of_week]" 
                                                        value="{{ $day }}" 
                                                        id="chk_{{ $idx }}"
                                                        class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500"
                                                        @if($isChecked) checked @endif>
                                                    
                                                    <input type="hidden" name="schedules[{{ $idx }}][start_time]" value="{{ $startTimeStr }}" @if(!$isChecked) disabled @endif id="st_{{ $idx }}">
                                                    <input type="hidden" name="schedules[{{ $idx }}][end_time]" value="{{ $endTimeStr }}" @if(!$isChecked) disabled @endif id="et_{{ $idx }}">
                                                    
                                                    <label for="chk_{{ $idx }}" class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-300">
                                                        {{ substr($startTimeStr, 0, 5) }} - {{ substr($endTimeStr, 0, 5) }}
                                                    </label>

                                                    {{-- JavaScript inline simple para que no lleguen campos required vacios --}}
                                                    <script>
                                                        document.getElementById('chk_{{ $idx }}').addEventListener('change', function() {
                                                            var startInput = document.getElementById('st_{{ $idx }}');
                                                            var endInput = document.getElementById('et_{{ $idx }}');
                                                            if (this.checked) {
                                                                startInput.disabled = false;
                                                                endInput.disabled = false;
                                                            } else {
                                                                startInput.disabled = true;
                                                                endInput.disabled = true;
                                                            }
                                                        });
                                                    </script>
                                                </div>
                                            @endfor
                                        </div>
                                    </td>
                                @endfor
                            </tr>
                        @endfor
                    </tbody>
                </table>
            </div>

        </x-wire-card>
    </form>
</x-admin-layout>
