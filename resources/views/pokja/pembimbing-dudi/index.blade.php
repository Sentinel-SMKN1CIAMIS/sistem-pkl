<x-app-layout>
    <x-slot name="header">Kelola Pembimbing DUDI</x-slot>

    <style>
        .pokja-header-container {
            display: flex !important;
            flex-direction: column !important;
            gap: 1rem !important;
        }
        .pokja-btn {
            width: 100% !important;
            display: inline-flex !important;
            justify-content: center !important;
            align-items: center !important;
        }
        @media (min-width: 768px) {
            .pokja-header-container {
                flex-direction: row !important;
                justify-content: space-between !important;
                align-items: center !important;
            }
            .pokja-btn {
                width: auto !important;
            }
        }
    </style>

    <div class="mb-6 pokja-header-container" x-data="{ importModalOpen: false }">
        <p class="text-slate-600 dark:text-slate-400">Daftar mentor / pembimbing dari pihak industri (DUDI).</p>
        <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
            <button @click="importModalOpen = true" class="pokja-btn px-5 py-2.5 bg-slate-800 hover:bg-slate-700 text-white font-medium rounded-xl transition-all gap-2 cursor-pointer border border-slate-700">
                <i data-lucide="upload-cloud" class="w-5 h-5"></i>
                Impor Pembimbing
            </button>
            <a href="{{ route('pokja.pembimbing_dudi.create') }}" class="pokja-btn px-5 py-2.5 bg-blue-600 hover:bg-blue-500 text-white font-medium rounded-xl shadow-lg shadow-blue-500/25 transition-all gap-2">
                <i data-lucide="user-plus" class="w-5 h-5"></i>
                Tambah Pembimbing
            </a>
        </div>

        <!-- Elegant Import Modal -->
        <template x-teleport="body">
            <div x-show="importModalOpen" 
                 class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 bg-slate-950/60 backdrop-blur-sm"
                 x-transition.opacity.duration.300ms x-cloak>
                
                <div @click.away="importModalOpen = false" 
                     class="glass-card w-full max-w-lg rounded-2xl overflow-hidden shadow-2xl border border-slate-200/50 dark:border-slate-700/50 bg-white dark:bg-slate-900 animate-fade-in-up text-left">
                    
                    <!-- Modal Header -->
                    <div class="px-6 py-4 border-b border-slate-200/50 dark:border-slate-700/50 flex justify-between items-center bg-slate-50/50 dark:bg-slate-800/30">
                        <h3 class="text-lg font-bold text-slate-900 dark:text-slate-100 flex items-center gap-2">
                            <i data-lucide="upload-cloud" class="text-blue-500"></i>
                            Impor Data Pembimbing DUDI
                        </h3>
                        <button @click="importModalOpen = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-colors">
                            <i data-lucide="x" class="w-5 h-5"></i>
                        </button>
                    </div>
                    
                    <!-- Modal Body -->
                    <form action="{{ route('pokja.pembimbing_dudi.import') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-4">
                        @csrf
                        
                        <!-- Download Template Section -->
                        <div class="p-4 bg-blue-500/10 border border-blue-500/20 rounded-xl flex items-start gap-3">
                            <i data-lucide="file-spreadsheet" class="w-6 h-6 text-blue-400 flex-shrink-0 mt-0.5"></i>
                            <div>
                                <h4 class="text-sm font-semibold text-slate-900 dark:text-slate-100">Gunakan Template Resmi</h4>
                                <p class="text-xs text-slate-600 dark:text-slate-400 mt-1 leading-relaxed">
                                    Pastikan struktur kolom data Anda sesuai dengan template standar kami agar sistem dapat memprosesnya dengan lancar.
                                </p>
                                <a href="{{ route('pokja.import.template', 'pembimbing_dudi') }}" class="inline-flex items-center gap-2 mt-3 text-xs font-bold text-blue-500 hover:text-blue-400 transition-colors bg-blue-500/10 px-3 py-1.5 rounded-lg border border-blue-500/30">
                                    <i data-lucide="download" class="w-3.5 h-3.5"></i>
                                    Unduh Template Excel (.xlsx)
                                </a>
                                <a href="{{ route('pokja.import.panduan') }}" target="_blank" class="inline-flex items-center gap-2 mt-3 text-xs font-bold text-emerald-500 hover:text-emerald-400 transition-colors bg-emerald-500/10 px-3 py-1.5 rounded-lg border border-emerald-500/30 ml-2">
                                    <i data-lucide="book-open" class="w-3.5 h-3.5"></i>
                                    Buka Panduan Lengkap
                                </a>
                            </div>
                        </div>
                        
                        <!-- File Upload Input -->
                        <div class="space-y-2" x-data="{ localFileName: '' }">
                            <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 uppercase tracking-wider">Pilih File Excel</label>
                            <div class="border-2 border-dashed rounded-xl p-4 text-center cursor-pointer transition-all relative group duration-300"
                                 :class="localFileName ? 'border-emerald-500/50 bg-emerald-500/5 dark:bg-emerald-500/10' : 'border-slate-300 dark:border-slate-700 hover:border-blue-500 dark:hover:border-blue-500'">
                                <input type="file" name="file" accept=".xlsx, .xls" required 
                                       @change="localFileName = $event.target.files[0] ? $event.target.files[0].name : ''"
                                       class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                                
                                <!-- State: Empty -->
                                <div class="flex flex-col items-center gap-2 py-4" x-show="!localFileName">
                                    <i data-lucide="file-spreadsheet" class="w-8 h-8 text-slate-400 group-hover:text-blue-500 transition-colors"></i>
                                    <span class="text-xs font-medium text-slate-700 dark:text-slate-300 group-hover:text-slate-900 dark:text-white transition-colors">Klik atau Seret file Excel ke sini</span>
                                    <span class="text-[10px] text-slate-500 dark:text-slate-500">Maksimum ukuran file: 4MB (Format .xlsx, .xls saja)</span>
                                </div>

                                <!-- State: File Chosen -->
                                <div class="flex flex-col items-center gap-2 py-4" x-show="localFileName" x-cloak>
                                    <i data-lucide="check-circle-2" class="w-8 h-8 text-emerald-500 animate-bounce"></i>
                                    <span class="text-xs font-bold text-emerald-600 dark:text-emerald-400 block truncate max-w-xs" x-text="localFileName"></span>
                                    <span class="text-[10px] text-slate-500 dark:text-slate-500">Klik atau seret file lain untuk mengganti berkas</span>
                                </div>
                            </div>
                        </div>

                        <!-- Important Information List -->
                        <div class="p-3.5 bg-slate-50 dark:bg-slate-800/20 border border-slate-200/50 dark:border-slate-700/50 rounded-xl">
                            <h5 class="text-xs font-bold text-slate-900 dark:text-slate-100 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                                <i data-lucide="info" class="w-4 h-4 text-amber-500"></i>
                                Penting Sebelum Mengunggah:
                            </h5>
                            <ul class="list-disc pl-5 space-y-1 text-[11px] text-slate-600 dark:text-slate-400 leading-normal">
                                <li>Isi kolom <strong>nama_perusahaan</strong> persis seperti nama DUDI yang sudah terdaftar di database.</li>
                                <li><strong>Username</strong> dan <strong>Email</strong> akun mentor wajib unik dan belum terdaftar di sistem.</li>
                            </ul>
                        </div>
                        
                        <!-- Action Buttons -->
                        <div class="pt-4 border-t border-slate-200/50 dark:border-slate-700/50 flex justify-end gap-3">
                            <button type="button" @click="importModalOpen = false" class="px-4 py-2 text-xs font-medium text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800/50 rounded-xl transition-colors border border-slate-200/50 dark:border-slate-700/50">
                                Batal
                            </button>
                            <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-500 text-white text-xs font-bold rounded-xl shadow-lg shadow-blue-500/25 transition-all flex items-center gap-2">
                                <i data-lucide="upload" class="w-4 h-4"></i>
                                Mulai Impor Data
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </template>
    </div>

    @if(session('import_errors'))
        <div class="mb-6 p-4 rounded-xl bg-red-500/10 border border-red-500/20 text-red-500 dark:text-red-400 text-sm">
            <h4 class="font-bold mb-2 flex items-center gap-2">
                <i data-lucide="alert-circle" class="w-5 h-5 text-red-500 flex-shrink-0"></i>
                Gagal Mengimpor Data Pembimbing DUDI. Silakan periksa beberapa kesalahan berikut:
            </h4>
            <ul class="list-disc pl-5 space-y-1 text-xs">
                @foreach(session('import_errors') as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if(session('success'))
        <div class="mb-6 p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-sm flex items-center gap-3">
            <i data-lucide="check-circle" class="w-5 h-5"></i>
            {{ session('success') }}
        </div>
    @endif

    <div class="glass-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-200/50 dark:border-slate-700/50 bg-white dark:bg-slate-800/30">
                        <th class="px-6 py-4 text-xs font-semibold text-slate-600 dark:text-slate-400 uppercase tracking-wider whitespace-nowrap">Nama Mentor</th>
                        <th class="px-6 py-4 text-xs font-semibold text-slate-600 dark:text-slate-400 uppercase tracking-wider whitespace-nowrap">Perusahaan</th>
                        <th class="px-6 py-4 text-xs font-semibold text-slate-600 dark:text-slate-400 uppercase tracking-wider whitespace-nowrap">Jabatan</th>
                        <th class="px-6 py-4 text-xs font-semibold text-slate-600 dark:text-slate-400 uppercase tracking-wider whitespace-nowrap">No. HP</th>
                        <th class="px-6 py-4 text-xs font-semibold text-slate-600 dark:text-slate-400 uppercase tracking-wider text-right whitespace-nowrap">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-700/50">
                    @forelse($mentors as $item)
                        <tr class="hover:bg-white dark:bg-slate-800/20 transition-colors group">
                            <td class="px-6 py-4 text-slate-900 dark:text-slate-100 whitespace-nowrap">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 flex items-center justify-center text-purple-400 font-bold">
                                        {{ substr($item->nama_lengkap, 0, 1) }}
                                    </div>
                                    <div class="min-w-0">
                                        <span class="text-sm font-semibold text-slate-900 dark:text-slate-100 block truncate">{{ $item->nama_lengkap }}</span>
                                        <span class="text-xs text-slate-500 dark:text-slate-400 truncate">{{ $item->user->email }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2.5 py-1 rounded-md bg-amber-500/10 border border-amber-500/20 text-xs text-amber-400">
                                    {{ $item->dudi->nama }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-700 dark:text-slate-300 whitespace-nowrap">
                                {{ $item->jabatan }}
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-400 whitespace-nowrap">
                                {{ $item->no_hp ?? '-' }}
                            </td>
                            <td class="px-6 py-4 text-right whitespace-nowrap">
                                <div x-data="{ open: false }" class="relative flex justify-end" x-on:click.away="open = false">
                                    <button x-on:click="open = !open" class="p-1.5 text-slate-500 hover:text-slate-700 dark:hover:text-slate-300 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors focus:outline-none">
                                        <i data-lucide="more-vertical" class="w-4 h-4"></i>
                                    </button>
                                    <div x-show="open" 
                                         x-transition:enter="transition ease-out duration-100"
                                         x-transition:enter-start="transform opacity-0 scale-95"
                                         x-transition:enter-end="transform opacity-100 scale-100"
                                         x-transition:leave="transition ease-in duration-75"
                                         x-transition:leave-start="transform opacity-100 scale-100"
                                         x-transition:leave-end="transform opacity-0 scale-95"
                                         class="absolute right-0 mt-8 w-32 rounded-xl bg-white dark:bg-slate-800 border border-slate-200/50 dark:border-slate-700/50 shadow-lg py-1 z-50 text-left" 
                                         style="display: none;">
                                        <a href="{{ route('pokja.pembimbing_dudi.edit', $item) }}" class="flex items-center gap-2 px-3 py-2 text-xs text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors">
                                            <i data-lucide="edit-3" class="w-3.5 h-3.5 text-blue-500"></i>
                                            Edit
                                        </a>
                                        <form action="{{ route('pokja.pembimbing_dudi.destroy', $item) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data mentor ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="w-full flex items-center gap-2 px-3 py-2 text-xs text-red-600 dark:text-red-400 hover:bg-red-50/50 dark:hover:bg-red-950/20 transition-colors text-left">
                                                <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-slate-500 dark:text-slate-400 italic">
                                Belum ada data pembimbing DUDI.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($mentors->hasPages())
            <div class="px-6 py-4 border-t border-slate-200/50 dark:border-slate-700/50">
                {{ $mentors->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
