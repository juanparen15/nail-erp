<x-filament-panels::page>

    {{ $this->form }}

    <x-filament::section
        heading="Fechas Bloqueadas"
        description="Días en que el salón no estará disponible para reservas (festivos, vacaciones, etc.)"
        icon="heroicon-o-no-symbol"
    >
        @forelse($this->blockedDates as $blocked)
            <div class="flex items-center justify-between py-3 {{ !$loop->last ? 'border-b border-gray-100 dark:border-white/5' : '' }}">
                <div class="flex items-center gap-3">
                    <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-danger-50 dark:bg-danger-400/10">
                        <x-filament::icon
                            icon="heroicon-m-calendar-x-mark"
                            class="h-5 w-5 text-danger-500"
                        />
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">
                            {{ \Carbon\Carbon::parse($blocked->date)->locale('es')->isoFormat('dddd, D [de] MMMM [de] YYYY') }}
                        </p>
                        @if($blocked->reason)
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $blocked->reason }}</p>
                        @endif
                    </div>
                </div>

                <x-filament::icon-button
                    icon="heroicon-m-trash"
                    wire:click="removeBlockedDate({{ $blocked->id }})"
                    wire:confirm="¿Desbloquear esta fecha?"
                    color="danger"
                    tooltip="Desbloquear"
                    size="sm"
                />
            </div>
        @empty
            <div class="flex flex-col items-center justify-center py-8 text-center">
                <x-filament::icon
                    icon="heroicon-o-calendar-days"
                    class="h-10 w-10 text-gray-300 dark:text-gray-600 mb-3"
                />
                <p class="text-sm text-gray-400 dark:text-gray-500">No hay fechas bloqueadas</p>
            </div>
        @endforelse
    </x-filament::section>

</x-filament-panels::page>
