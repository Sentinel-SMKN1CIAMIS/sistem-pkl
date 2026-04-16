@props([
    'type' => 'submit',
    'variant' => 'primary',
    'class' => '',
    'icon' => null,
    'errorText' => 'Lengkapi data',
])

@php
    $variants = [
        'primary' => 'bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white shadow-blue-500/25',
        'secondary' => 'bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700',
        'danger' => 'bg-gradient-to-r from-red-600 to-rose-600 hover:from-red-500 hover:to-rose-500 text-white shadow-red-500/25',
        'emerald' => 'bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white shadow-emerald-500/25',
        'orange' => 'bg-gradient-to-r from-orange-600 to-amber-600 hover:from-orange-500 hover:to-amber-500 text-white shadow-orange-500/25',
    ];

    $variantClass = $variants[$variant] ?? $variants['primary'];
@endphp

<button
    type="{{ $type }}"
    x-data="{
        loading: false,
        form: null,
        isValid: true,
        init() {
            this.form = $el.closest('form');
            if (this.form) {
                // Check initial validity
                this.isValid = this.form.checkValidity();

                // Listen for input changes to toggle disabled state
                this.form.addEventListener('input', () => {
                    this.isValid = this.form.checkValidity();
                });

                // Listen for submit to show processing state
                this.form.addEventListener('submit', () => {
                    if (this.isValid) {
                        this.loading = true;
                    }
                });
            }
        }
    }"
    :disabled="loading || !isValid"
    {{ $attributes->merge(['class' => "py-2.5 px-4 rounded-xl font-medium transition-all transform hover:-translate-y-0.5 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:ring-offset-slate-900 flex justify-center items-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none $variantClass $class"]) }}
>
    <!-- Normal State -->
    <template x-if="!loading">
        <div class="flex items-center gap-2">
            <!-- Default State -->
            <template x-if="!form || isValid">
                <div class="flex items-center gap-2">
                    {{ $slot }}
                    @if($icon)
                        <i data-lucide="{{ $icon }}" class="w-5 h-5"></i>
                    @endif
                </div>
            </template>

            <!-- Error State -->
            <template x-if="form && !isValid">
                <div class="flex items-center gap-2">
                    <i data-lucide="alert-circle" class="w-5 h-5"></i>
                    <span>
                        @if($attributes->has('error-text'))
                            <span x-text="{{ $attributes->get('error-text') }}"></span>
                        @else
                            {{ $errorText }}
                        @endif
                    </span>
                </div>
            </template>
        </div>
    </template>

    <!-- Loading State -->
    <template x-if="loading">
        <div class="flex items-center gap-2">
            <svg class="animate-spin h-5 w-5 text-current" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span>Memproses...</span>
        </div>
    </template>
</button>
