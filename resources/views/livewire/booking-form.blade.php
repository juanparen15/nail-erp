<div data-booking-step="{{ $step }}">

    @if(!$bookingComplete)

    {{-- ════════════════════════════════════════
         PASO 1 — Servicio
    ════════════════════════════════════════ --}}
    @if($step === 1)
    <div class="step-wrap">
        <span class="step-script">Reserva tu cita</span>
        <h1 class="step-heading">Elige tu servicio</h1>
        <p class="step-desc">Selecciona el tratamiento que deseas para tu cita</p>

        @error('selectedServiceId')
            <div class="alert-err">{{ $message }}</div>
        @enderror

        <div class="service-list">
            @foreach($services as $service)
            <div class="service-card {{ $selectedServiceId === $service->id ? 'is-sel' : '' }}"
                 style="{{ $selectedServiceId === $service->id ? 'border-left-color:' . $service->color : '' }}"
                 wire:click="selectService({{ $service->id }})">

                <div class="svc-dot" style="background:{{ $service->color }}"></div>

                <div class="svc-info">
                    <div class="svc-name">{{ $service->name }}</div>
                    @if($service->description)
                        <div class="svc-desc">{{ $service->description }}</div>
                    @endif
                </div>

                <div class="svc-meta">
                    <div class="svc-price">${{ number_format($service->price, 0, ',', '.') }}</div>
                    <div class="svc-dur">{{ $service->duration_formatted }}</div>
                </div>

                <div class="svc-check">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                </div>
            </div>
            @endforeach
        </div>

        <div class="nav-row end">
            <button wire:click="nextStep" class="btn-primary" {{ !$selectedServiceId ? 'disabled' : '' }}>
                Continuar
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
            </button>
        </div>
    </div>
    @endif

    {{-- ════════════════════════════════════════
         PASO 2 — Fecha
    ════════════════════════════════════════ --}}
    @if($step === 2)
    <div class="step-wrap">
        <span class="step-script">¿Cuándo te vemos?</span>
        <h1 class="step-heading">Elige la fecha</h1>
        <p class="step-desc">
            <strong>{{ $selectedService?->name }}</strong>
            &nbsp;·&nbsp; {{ $selectedService?->duration_formatted }}
        </p>

        @error('selectedDate')
            <div class="alert-err">{{ $message }}</div>
        @enderror

        @if(count($availableDates) === 0)
            <div class="empty">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
                </svg>
                <p>No hay fechas disponibles en los próximos 60 días.</p>
            </div>
        @else
            @php
                $groupedDates = collect($availableDates)->groupBy(fn($d) => \Carbon\Carbon::parse($d)->format('Y-m'));
            @endphp

            @foreach($groupedDates as $monthKey => $dates)
                <span class="month-label">
                    {{ ucfirst(\Carbon\Carbon::parse($monthKey . '-01')->locale('es')->isoFormat('MMMM [de] YYYY')) }}
                </span>
                <div class="date-grid">
                    @foreach($dates as $date)
                        @php $c = \Carbon\Carbon::parse($date); @endphp
                        <div class="date-cell {{ $selectedDate === $date ? 'is-sel' : '' }}"
                             wire:click="selectDate('{{ $date }}')">
                            <span class="d-name">{{ $c->locale('es')->isoFormat('ddd') }}</span>
                            <span class="d-num">{{ $c->day }}</span>
                        </div>
                    @endforeach
                </div>
            @endforeach
        @endif

        <div class="nav-row">
            <button wire:click="prevStep" class="btn-ghost">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
                Atrás
            </button>
            <button wire:click="nextStep" class="btn-primary" {{ !$selectedDate ? 'disabled' : '' }}>
                Continuar
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
            </button>
        </div>
    </div>
    @endif

    {{-- ════════════════════════════════════════
         PASO 3 — Hora
    ════════════════════════════════════════ --}}
    @if($step === 3)
    <div class="step-wrap">
        <span class="step-script">Elige tu horario</span>
        <h1 class="step-heading">Elige la hora</h1>
        <p class="step-desc">
            <strong>{{ \Carbon\Carbon::parse($selectedDate)->locale('es')->isoFormat('dddd, D [de] MMMM') }}</strong>
            &nbsp;·&nbsp; {{ $selectedService?->name }}
        </p>

        @error('selectedTime')
            <div class="alert-err">{{ $message }}</div>
        @enderror

        @if(count($availableSlots) === 0)
            <div class="empty">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                </svg>
                <p>No hay horarios disponibles para esta fecha.</p>
                <div style="margin-top:1rem">
                    <button wire:click="prevStep" class="btn-ghost">← Elegir otra fecha</button>
                </div>
            </div>
        @else
            <div class="time-grid">
                @foreach($availableSlots as $slot)
                    <div class="time-cell {{ $selectedTime === $slot ? 'is-sel' : '' }}"
                         wire:click="selectTime('{{ $slot }}')">
                        {{ $slot }}
                    </div>
                @endforeach
            </div>
        @endif

        <div class="nav-row">
            <button wire:click="prevStep" class="btn-ghost">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
                Atrás
            </button>
            <button wire:click="nextStep" class="btn-primary" {{ !$selectedTime ? 'disabled' : '' }}>
                Continuar
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
            </button>
        </div>
    </div>
    @endif

    {{-- ════════════════════════════════════════
         PASO 4 — Datos personales
    ════════════════════════════════════════ --}}
    @if($step === 4)
    <div class="step-wrap">
        <span class="step-script">Casi listo</span>
        <h1 class="step-heading">Tus datos</h1>
        <p class="step-desc">Necesitamos estos datos para confirmar tu cita</p>

        {{-- Summary chip --}}
        <div class="summary-chip">
            <div class="summary-dot" style="background:{{ $selectedService?->color ?? '#B4707A' }}"></div>
            <div class="summary-body">
                <div class="summary-svc">{{ $selectedService?->name }}</div>
                <div class="summary-when">
                    {{ \Carbon\Carbon::parse($selectedDate)->locale('es')->isoFormat('dddd D [de] MMMM') }}
                    &nbsp;·&nbsp; {{ $selectedTime }} hrs
                </div>
            </div>
            <div class="summary-price">${{ number_format($selectedService?->price ?? 0, 0, ',', '.') }}</div>
        </div>

        <div class="form-block">
            <label class="form-label">Nombre completo *</label>
            <input type="text" wire:model="clientName" class="form-field" placeholder="Tu nombre completo">
            @error('clientName') <span class="field-err">{{ $message }}</span> @enderror
        </div>
        <div class="form-block">
            <label class="form-label">Teléfono *</label>
            <input type="tel" wire:model="clientPhone" class="form-field" placeholder="300 000 0000">
            @error('clientPhone') <span class="field-err">{{ $message }}</span> @enderror
        </div>
        <div class="form-block">
            <label class="form-label">WhatsApp <span class="opt">(si es diferente al teléfono)</span></label>
            <input type="tel" wire:model="clientWhatsapp" class="form-field" placeholder="300 000 0000">
        </div>
        <div class="form-block">
            <label class="form-label">Email <span class="opt">(opcional)</span></label>
            <input type="email" wire:model="clientEmail" class="form-field" placeholder="tu@email.com">
            @error('clientEmail') <span class="field-err">{{ $message }}</span> @enderror
        </div>
        <div class="form-block">
            <label class="form-label">Notas <span class="opt">(diseño especial, alergias…)</span></label>
            <textarea wire:model="appointmentNotes" class="form-field" rows="3" placeholder="Cuéntanos algo especial para tu cita"></textarea>
        </div>

        <div class="nav-row">
            <button wire:click="prevStep" class="btn-ghost">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
                Atrás
            </button>
            <button wire:click="confirmBooking" class="btn-primary" wire:loading.attr="disabled">
                <span wire:loading.remove>
                    Confirmar reserva
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                </span>
                <span wire:loading>Procesando…</span>
            </button>
        </div>
    </div>
    @endif

    @endif {{-- /!$bookingComplete --}}

    {{-- ════════════════════════════════════════
         PASO 5 — ¡Confirmación!
    ════════════════════════════════════════ --}}
    @if($step === 5 && $bookingComplete)
    <div class="step-wrap confirm-wrap">

        <div class="confirm-ring">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="20 6 9 17 4 12"/>
            </svg>
        </div>

        <div class="pending-pill">
            <span class="pending-dot"></span>
            Pendiente de confirmación
        </div>

        <span class="confirm-script">¡Reserva exitosa!</span>
        <h2 class="confirm-heading">Tu cita está lista</h2>
        <p class="confirm-sub">
            Te notificaremos por WhatsApp cuando sea confirmada por Kate Nails.
        </p>

        <div class="receipt">
            <div class="receipt-row">
                <span class="receipt-key">Servicio</span>
                <span class="receipt-val">{{ $confirmedAppointment?->service?->name }}</span>
            </div>
            <div class="receipt-row">
                <span class="receipt-key">Fecha</span>
                <span class="receipt-val">
                    {{ ucfirst(\Carbon\Carbon::parse($confirmedAppointment?->appointment_date)->locale('es')->isoFormat('dddd, D [de] MMMM [de] YYYY')) }}
                </span>
            </div>
            <div class="receipt-row">
                <span class="receipt-key">Hora</span>
                <span class="receipt-val">{{ substr($confirmedAppointment?->appointment_time ?? '', 0, 5) }} hrs</span>
            </div>
            <div class="receipt-row is-total">
                <span class="receipt-key">Total</span>
                <span class="receipt-val">${{ number_format($confirmedAppointment?->total_price ?? 0, 0, ',', '.') }}</span>
            </div>
        </div>

        <button wire:click="$refresh" onclick="window.location.reload()" class="btn-primary">
            ✦&nbsp; Hacer otra reserva
        </button>

    </div>
    @endif

</div>
