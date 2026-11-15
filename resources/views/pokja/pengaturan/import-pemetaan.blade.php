<x-app-layout>
    <x-slot name="header">Impor Pemetaan Pembimbing & DUDI PKL</x-slot>

    <div class="max-w-4xl text-slate-700 dark:text-slate-300">
        <div class="mb-6">
            <p class="text-slate-600 dark:text-slate-400">
                Impor dan sinkronisasi otomatis data pemetaan siswa ke tempat PKL dan penugasan guru pembimbing sekolah untuk Tim Pokja PKL.
            </p>
        </div>

        @if(session('success'))
            <div class="mb-6 p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-sm flex items-center gap-3">
                <i data-lucide="check-circle" class="w-5 h-5"></i>
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 p-4 rounded-xl bg-red-500/10 border border-red-500/20 text-red-500 dark:text-red-400 text-sm flex items-center gap-3">
                <i data-lucide="alert-circle" class="w-5 h-5"></i>
                {{ session('error') }}
            </div>
        @endif

        <div class="space-y-6">
            @include('partials.import-pemetaan-card', ['postUrl' => route('pokja.pengaturan.import_pemetaan.process')])
        </div>
    </div>
</x-app-layout>

