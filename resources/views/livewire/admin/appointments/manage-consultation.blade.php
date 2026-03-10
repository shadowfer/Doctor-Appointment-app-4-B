<div>
    <!-- Encabezado del Paciente -->
    <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-6 mb-6 mt-4">
        <div class="flex justify-between items-start">
            <div class="flex items-center space-x-4">
                <div class="w-16 h-16 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 font-bold text-2xl">
                    {{ strtoupper(substr($appointment->patient->user->name, 0, 2)) }}
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">{{ $appointment->patient->user->name }}</h2>
                    <p class="text-sm text-gray-500">Fecha de Cita: {{ \Carbon\Carbon::parse($appointment->date)->format('d/m/Y') }} | Hora: {{ \Carbon\Carbon::parse($appointment->start_time)->format('H:i') }}</p>
                    <p class="text-sm text-gray-500">Motivo: {{ $appointment->reason ?? 'Ninguno registrado' }}</p>
                </div>
            </div>
            <div class="flex space-x-2">
                <button type="button" wire:click="toggleHistoryModal" class="text-indigo-600 bg-indigo-50 hover:bg-indigo-100 font-medium rounded-lg text-sm px-4 py-2 text-center transition-colors">
                    <i class="fa-solid fa-file-medical mr-1"></i> Ver Historia
                </button>
                <button type="button" class="text-indigo-600 bg-indigo-50 hover:bg-indigo-100 font-medium rounded-lg text-sm px-4 py-2 text-center transition-colors">
                    <i class="fa-solid fa-clock-rotate-left mr-1"></i> Consultas Anteriores
                </button>
            </div>
        </div>
    </div>

    <!-- Modal de Historia Médica -->
    @if($showHistoryModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black bg-opacity-50">
            <div class="bg-white rounded-lg shadow-xl w-full max-w-2xl overflow-hidden transition-all transform animate-fade-in-down">
                <!-- Header del Modal -->
                <div class="flex items-center justify-between p-4 border-b border-gray-200">
                    <h3 class="text-xl font-bold text-gray-900">Historia médica del paciente</h3>
                    <button wire:click="toggleHistoryModal" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
                
                <!-- Cuerpo del Modal -->
                <div class="p-6 space-y-6">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <span class="block text-sm font-medium text-gray-500">Tipo de sangre:</span>
                            <span class="text-md font-bold text-gray-900">{{ $appointment->patient->bloodType->name ?? '-' }}</span>
                        </div>
                        <div>
                            <span class="block text-sm font-medium text-gray-500">Alergias:</span>
                            <span class="text-md font-bold text-gray-900">{{ $appointment->patient->allergies ?? 'No registradas' }}</span>
                        </div>
                        <div>
                            <span class="block text-sm font-medium text-gray-500">Enfermedades crónicas:</span>
                            <span class="text-md font-bold text-gray-900">{{ $appointment->patient->chronic_conditions ?? 'No registradas' }}</span>
                        </div>
                        <div>
                            <span class="block text-sm font-medium text-gray-500">Antecedentes quirúrgicos:</span>
                            <span class="text-md font-bold text-gray-900">{{ $appointment->patient->surgical_history ?? 'No registradas' }}</span>
                        </div>
                    </div>
                </div>

                <!-- Footer del Modal -->
                <div class="flex items-center justify-end p-4 border-t border-gray-200 space-x-2">
                    <a href="{{ route('admin.patients.edit', $appointment->patient_id) }}" class="text-indigo-600 hover:underline text-sm font-medium">
                        Ver / Editar Historia Médica
                    </a>
                </div>
            </div>
        </div>
    @endif

    <!-- Navegación por Pestañas -->
    <div class="border-b border-gray-200 mb-6">
        <ul class="flex flex-wrap -mb-px text-sm font-medium text-center text-gray-500">
            <li class="mr-2">
                <button wire:click="setTab('consulta')" 
                        class="inline-flex items-center justify-center p-4 border-b-2 rounded-t-lg group 
                        {{ $activeTab === 'consulta' ? 'text-indigo-600 border-indigo-600 active' : 'border-transparent hover:text-gray-600 hover:border-gray-300' }}">
                    <i class="fa-solid fa-stethoscope mr-2 {{ $activeTab === 'consulta' ? 'text-indigo-600' : 'text-gray-400 group-hover:text-gray-500' }}"></i>
                    Consulta
                </button>
            </li>
            <li class="mr-2">
                <button wire:click="setTab('receta')" 
                        class="inline-flex items-center justify-center p-4 border-b-2 rounded-t-lg group 
                        {{ $activeTab === 'receta' ? 'text-indigo-600 border-indigo-600 active' : 'border-transparent hover:text-gray-600 hover:border-gray-300' }}">
                    <i class="fa-solid fa-pills mr-2 {{ $activeTab === 'receta' ? 'text-indigo-600' : 'text-gray-400 group-hover:text-gray-500' }}"></i>
                    Receta
                </button>
            </li>
        </ul>
    </div>

    <!-- Contenido de las Pestañas -->
    <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-6 mb-8">
        @if($activeTab === 'consulta')
            <!-- Formulario Consulta -->
            <form wire:submit.prevent="saveConsultation">
                <div class="space-y-6">
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900">Diagnóstico</label>
                        <textarea wire:model="diagnosis" rows="3" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block w-full p-2.5"></textarea>
                    </div>
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900">Tratamiento</label>
                        <textarea wire:model="treatment" rows="4" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block w-full p-2.5"></textarea>
                    </div>
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900">Notas Adicionales</label>
                        <textarea wire:model="notes" rows="2" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block w-full p-2.5"></textarea>
                    </div>
                    <div class="flex justify-end">
                        <button type="submit" class="text-white bg-indigo-500 hover:bg-indigo-600 focus:ring-4 focus:ring-indigo-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">
                            Guardar Consulta
                        </button>
                    </div>
                </div>
            </form>

        @elseif($activeTab === 'receta')
            <!-- Formulario Receta -->
            <div class="space-y-8">
                <!-- Lista de Medicamentos -->
                <div>
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Medicamentos Recetados</h3>
                    @if(count($prescriptions) > 0)
                        <div class="overflow-x-auto border border-gray-200 rounded-lg">
                            <table class="w-full text-sm text-left text-gray-500">
                                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3">Medicamento</th>
                                        <th scope="col" class="px-6 py-3">Dosis</th>
                                        <th scope="col" class="px-6 py-3">Frecuencia / Duración</th>
                                        <th scope="col" class="px-6 py-3"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($prescriptions as $presc)
                                        <tr class="bg-white border-b hover:bg-gray-50">
                                            <td class="px-6 py-3 font-medium text-gray-900">{{ $presc->medication }}</td>
                                            <td class="px-6 py-3">{{ $presc->dose }}</td>
                                            <td class="px-6 py-3">{{ $presc->frequency }}</td>
                                            <td class="px-6 py-3 text-right">
                                                <button wire:click="removePrescription({{ $presc->id }})" class="text-red-500 hover:text-red-700">
                                                    <i class="fa-solid fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-sm text-gray-500 italic">No se han añadido medicamentos a la receta aún.</p>
                    @endif
                </div>

                <!-- Agregar Nuevo Medicamento -->
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-5">
                    <h4 class="text-md font-bold text-gray-900 mb-4">Añadir Nuevo Medicamento</h4>
                    <form wire:submit.prevent="addPrescription">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                            <div>
                                <label class="block mb-2 text-sm font-medium text-gray-900">Medicamento</label>
                                <input type="text" wire:model="medication" required placeholder="Ej. Paracetamol 500mg"
                                    class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block w-full p-2.5">
                            </div>
                            <div>
                                <label class="block mb-2 text-sm font-medium text-gray-900">Dosis</label>
                                <input type="text" wire:model="dose" required placeholder="Ej. 1 tableta"
                                    class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block w-full p-2.5">
                            </div>
                            <div>
                                <label class="block mb-2 text-sm font-medium text-gray-900">Frecuencia / Duración</label>
                                <input type="text" wire:model="frequency" required placeholder="Ej. Cada 8 horas por 5 días"
                                    class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block w-full p-2.5">
                            </div>
                        </div>
                        <div class="mt-4 flex justify-end">
                            <button type="submit" class="text-indigo-600 bg-indigo-50 border border-indigo-200 hover:bg-indigo-100 focus:ring-4 focus:ring-indigo-100 font-medium rounded-lg text-sm px-5 py-2.5 text-center">
                                <i class="fa-solid fa-plus mr-1"></i> Añadir Medicamento
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @endif
    </div>
</div>
