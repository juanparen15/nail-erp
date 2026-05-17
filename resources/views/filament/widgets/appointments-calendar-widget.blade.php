<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            Calendario de citas
        </x-slot>
        <x-slot name="headerEnd">
            <span class="text-xs text-gray-400 dark:text-gray-500">Clic en una cita para ver detalles</span>
        </x-slot>

        @once
            @push('styles')
            <style>
                /* ── FullCalendar overrides ───────────────────────────── */
                .fc { font-family: inherit; font-size: 0.82rem; }
                .fc .fc-toolbar-title { font-size: 0.95rem; font-weight: 600; }
                .fc .fc-button {
                    background-color: rgb(var(--color-primary-500)) !important;
                    border-color: rgb(var(--color-primary-600)) !important;
                    font-size: 0.72rem !important;
                    padding: 0.2rem 0.55rem !important;
                    border-radius: 0.35rem !important;
                    text-transform: capitalize !important;
                    box-shadow: none !important;
                }
                .fc .fc-button:hover { background-color: rgb(var(--color-primary-600)) !important; }
                .fc .fc-button-active,
                .fc .fc-button:not(:disabled):active {
                    background-color: rgb(var(--color-primary-700)) !important;
                    border-color: rgb(var(--color-primary-700)) !important;
                }
                .fc .fc-daygrid-day.fc-day-today,
                .fc .fc-timegrid-col.fc-day-today {
                    background-color: rgba(236,72,153,.06) !important;
                }
                /* Dark mode */
                .dark .fc-theme-standard td,
                .dark .fc-theme-standard th,
                .dark .fc-theme-standard .fc-scrollgrid { border-color: #374151 !important; }
                .dark .fc .fc-col-header-cell-cushion,
                .dark .fc .fc-daygrid-day-number,
                .dark .fc .fc-timegrid-slot-label,
                .dark .fc .fc-list-event-title,
                .dark .fc .fc-toolbar-title { color: #d1d5db !important; }
                .dark .fc .fc-list-day-cushion { background-color: #1f2937 !important; }
                .dark .fc .fc-list-day-side-text { color: #9ca3af !important; }
                .dark .fc-theme-standard .fc-list { border-color: #374151 !important; }
                .fc .fc-event { cursor: pointer; border-radius: 4px; font-size: 0.75rem; }
            </style>
            @endpush
        @endonce

        <div
            wire:ignore
            x-data="{
                calendar: null,
                detail: null,
                init() {
                    const el = this.$refs.calendarEl;
                    const url = @js(route('admin.calendar.appointments'));
                    const self = this;

                    /* FullCalendar loads from CDN before Alpine (panels::scripts.before hook),
                       but guard with a retry just in case. */
                    const tryInit = () => {
                        if (typeof window.FullCalendar === 'undefined') {
                            setTimeout(tryInit, 100);
                            return;
                        }

                        self.calendar = new window.FullCalendar.Calendar(el, {
                            initialView: 'timeGridWeek',
                            locale: 'es',
                            firstDay: 1,
                            height: 'auto',
                            headerToolbar: {
                                left:   'prev,next today',
                                center: 'title',
                                right:  'dayGridMonth,timeGridWeek,timeGridDay,listWeek',
                            },
                            buttonText: {
                                today: 'Hoy',
                                month: 'Mes',
                                week:  'Semana',
                                day:   'Día',
                                list:  'Lista',
                            },
                            slotMinTime: '07:00:00',
                            slotMaxTime: '21:00:00',
                            allDaySlot: false,
                            nowIndicator: true,
                            events: {
                                url: url,
                                failure() { console.error('Error al cargar citas del calendario'); },
                            },
                            eventClick(info) {
                                const p = info.event.extendedProps;
                                self.detail = {
                                    title:   info.event.title,
                                    client:  p.client,
                                    service: p.service,
                                    status:  p.status,
                                    price:   p.price,
                                    phone:   p.phone,
                                    color:   info.event.backgroundColor,
                                };
                            },
                            eventDidMount(info) {
                                info.el.title = info.event.extendedProps.service
                                    + ' · ' + info.event.extendedProps.status;
                            },
                        });

                        self.calendar.render();
                    };

                    tryInit();
                },
            }"
            x-on:destroy="if (calendar) { calendar.destroy(); }"
        >
            <div x-ref="calendarEl" class="mt-1"></div>

            {{-- Appointment detail modal --}}
            <div
                x-show="detail !== null"
                x-transition.opacity
                @click.self="detail = null"
                class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm"
                style="display:none"
            >
                <div
                    class="w-full max-w-sm rounded-xl bg-white dark:bg-gray-800 shadow-2xl p-6 mx-4"
                    @click.stop
                    x-transition.scale
                >
                    <div class="flex items-center gap-3 mb-4">
                        <span class="inline-block w-3 h-3 rounded-full flex-shrink-0"
                              :style="'background:' + (detail && detail.color)"></span>
                        <h3 class="font-semibold text-gray-900 dark:text-white text-sm leading-snug"
                            x-text="detail && detail.title"></h3>
                    </div>
                    <dl class="space-y-2.5 text-sm">
                        <div class="flex justify-between">
                            <dt class="font-medium text-gray-500 dark:text-gray-400">Servicio</dt>
                            <dd class="text-gray-800 dark:text-gray-200" x-text="detail && detail.service"></dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="font-medium text-gray-500 dark:text-gray-400">Estado</dt>
                            <dd class="text-gray-800 dark:text-gray-200" x-text="detail && detail.status"></dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="font-medium text-gray-500 dark:text-gray-400">Total</dt>
                            <dd class="text-gray-800 dark:text-gray-200" x-text="detail && detail.price"></dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="font-medium text-gray-500 dark:text-gray-400">Teléfono</dt>
                            <dd class="text-gray-800 dark:text-gray-200" x-text="detail && detail.phone"></dd>
                        </div>
                    </dl>
                    <button
                        @click="detail = null"
                        class="mt-5 w-full rounded-lg bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 text-sm font-medium py-2 transition"
                    >
                        Cerrar
                    </button>
                </div>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
