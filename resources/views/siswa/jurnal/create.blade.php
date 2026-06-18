<x-app-layout>
    <x-slot name="header">Tambah Jurnal Harian</x-slot>

    <div class="mb-6">
        <a href="{{ route('siswa.jurnal.index') }}" class="inline-flex items-center gap-2 text-sm text-slate-600 dark:text-slate-400 hover:text-slate-800 dark:hover:text-slate-200 transition-colors">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            Kembali ke Daftar
        </a>
    </div>

    <div class="max-w-4xl text-slate-700 dark:text-slate-300">
        <div class="glass-card p-8">
            <form action="{{ route('siswa.jurnal.store') }}" method="POST" id="jurnal-form" class="space-y-6">
                @csrf
                <input type="hidden" name="foto_cropped" id="foto-cropped-input">
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="tanggal" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Tanggal Kegiatan</label>
                        <div class="relative">
                            <input type="date" name="tanggal" id="tanggal" 
                                   value="{{ old('tanggal', date('Y-m-d')) }}"
                                   min="{{ $minDate }}"
                                   max="{{ $maxDate }}"
                                   required
                                   class="w-full px-4 py-2.5 bg-slate-100 dark:bg-slate-900/50 border border-slate-200/50 dark:border-slate-700/50 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-slate-800 dark:text-slate-200 transition-all">
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                                Anda dapat mengisi jurnal untuk maksimal 7 hari sebelumnya
                            </p>
                        </div>
                    </div>

                    <div>
                        <label for="kompetensi_id" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Elemen Kompetensi</label>
                        <select name="kompetensi_id" id="kompetensi_id" required
                                class="w-full px-4 py-2.5 bg-slate-100 dark:bg-slate-900/50 border border-slate-200/50 dark:border-slate-700/50 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-slate-800 dark:text-slate-200 transition-all">
                            <option value="" disabled selected>Pilih Kompetensi</option>
                            @foreach($kompetensis as $item)
                                <option value="{{ $item->id }}">{{ $item->nama }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="cp_id" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Tujuan Pembelajaran (TP)</label>
                        <select name="cp_id" id="cp_id" 
                                class="w-full px-4 py-2.5 bg-slate-100 dark:bg-slate-900/50 border border-slate-200/50 dark:border-slate-700/50 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-slate-800 dark:text-slate-200 transition-all">
                            <option value="">-- Pilih TP (Opsional) --</option>
                            @foreach($tujuanPembelajaran as $tp)
                                <option value="{{ $tp->id }}" title="{{ $tp->deskripsi ?? '' }}">
                                    {{ $tp->tp ?? $tp->nama }} @if($tp->cp) - {{ $tp->cp }} @endif
                                </option>
                            @endforeach
                        </select>
                        <p class="text-xs text-slate-400 mt-1">Pilih TP yang sesuai dengan kegiatan hari ini.</p>
                    </div>

                    <div>
                        <label for="cp" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Capaian Pembelajaran (CP)</label>
                        <input type="text" name="cp" id="cp" value="{{ old('cp') }}"
                               class="w-full px-4 py-2.5 bg-slate-100 dark:bg-slate-900/50 border border-slate-200/50 dark:border-slate-700/50 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-slate-800 dark:text-slate-200 transition-all"
                               placeholder="Contoh: Menyajikan makanan Indonesia">
                        <p class="text-xs text-slate-400 mt-1">Isi CP yang sesuai dengan kegiatan hari ini.</p>
                    </div>

                    <div class="md:col-span-2">
                        <label for="kegiatan" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Unit Kerja / Pekerjaan</label>
                        <input type="text" name="kegiatan" id="kegiatan" required
                               class="w-full px-4 py-2.5 bg-slate-100 dark:bg-slate-900/50 border border-slate-200/50 dark:border-slate-700/50 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-slate-800 dark:text-slate-200 transition-all font-mono"
                               placeholder="Sebutkan unit kerja dan apa yang Anda kerjakan hari ini...">
                    </div>

                    {{-- Grid: Foto (left) | Catatan Detail/Kendala (right) --}}
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Foto Bukti Kegiatan</label>
                        
                        {{-- Upload Area (shown when no photo is cropped yet) --}}
                        <div id="foto-upload-area">
                            <label for="foto-raw" class="flex flex-col items-center justify-center w-full aspect-square border-2 border-slate-200 dark:border-slate-700 border-dashed rounded-xl cursor-pointer bg-slate-100 dark:bg-slate-900/20 hover:bg-slate-200 dark:hover:bg-slate-900/40 transition-all group">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="w-14 h-14 rounded-full bg-blue-500/10 flex items-center justify-center mb-3 group-hover:bg-blue-500/20 transition-colors">
                                        <i data-lucide="camera" class="w-7 h-7 text-blue-500"></i>
                                    </div>
                                    <p class="text-sm font-medium text-slate-600 dark:text-slate-400">Klik untuk unggah foto</p>
                                    <p class="text-[10px] text-slate-500 dark:text-slate-500 mt-1">JPG, PNG — max 2MB</p>
                                </div>
                                <input id="foto-raw" type="file" class="hidden" accept="image/*" />
                            </label>
                        </div>

                        {{-- Preview Area (shown after crop is saved) --}}
                        <div id="foto-preview-area" class="hidden">
                            <div class="relative w-full aspect-square rounded-xl overflow-hidden border-2 border-blue-500/30 shadow-lg shadow-blue-500/10">
                                <img id="foto-preview-img" src="" alt="Preview" class="w-full h-full object-cover">
                                <button type="button" onclick="removeCroppedPhoto()" 
                                        class="absolute top-2 right-2 w-8 h-8 rounded-full bg-red-600/90 hover:bg-red-500 text-white flex items-center justify-center shadow-lg transition-all transform hover:scale-110">
                                    <i data-lucide="x" class="w-4 h-4"></i>
                                </button>
                                <div class="absolute bottom-0 inset-x-0 bg-linear-to-t from-black/60 to-transparent p-3">
                                    <p class="text-xs text-white font-medium flex items-center gap-1.5">
                                        <i data-lucide="check-circle" class="w-3.5 h-3.5 text-emerald-400"></i>
                                        Foto siap diunggah
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label for="catatan" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Catatan Detail / Kendala</label>
                        <textarea name="catatan" id="catatan" rows="4"
                                  class="w-full h-[calc(100%-2rem)] min-h-[200px] px-4 py-2.5 bg-slate-100 dark:bg-slate-900/50 border border-slate-200/50 dark:border-slate-700/50 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-slate-800 dark:text-slate-200 transition-all resize-none"
                                  placeholder="Ceritakan proses pengerjaan atau kendala yang dihadapi..."></textarea>
                    </div>
                </div>

                <div class="pt-4 flex justify-end">
                    <button type="submit" class="px-8 py-3 bg-blue-600 hover:bg-blue-500 text-white font-bold rounded-xl shadow-lg shadow-blue-500/25 transition-all transform hover:-translate-y-1 flex items-center gap-2">
                        <i data-lucide="save" class="w-5 h-5"></i>
                        Simpan Jurnal
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Crop Modal --}}
    <div id="crop-modal" class="fixed inset-0 z-100 hidden items-center justify-center p-4 bg-slate-900/70 backdrop-blur-sm">
        <div class="glass-card max-w-lg w-full p-0 relative rounded-2xl overflow-hidden shadow-2xl animate-in" onclick="event.stopPropagation()">
            {{-- Header --}}
            <div class="px-6 py-4 border-b border-slate-200/50 dark:border-slate-700/50 flex items-center justify-between">
                <h3 class="font-bold text-slate-900 dark:text-slate-100 flex items-center gap-2">
                    <i data-lucide="crop" class="w-5 h-5 text-blue-500"></i>
                    Potong Foto (1:1)
                </h3>
                <button type="button" onclick="closeCropModal()" class="w-8 h-8 rounded-full hover:bg-slate-200 dark:hover:bg-slate-700 flex items-center justify-center transition-colors">
                    <i data-lucide="x" class="w-4 h-4 text-slate-600 dark:text-slate-400"></i>
                </button>
            </div>
            
            {{-- Crop Area --}}
            <div class="p-4 bg-slate-950">
                <div class="max-h-[60vh] overflow-hidden flex items-center justify-center">
                    <img id="crop-source" src="" alt="Source" class="max-w-full">
                </div>
            </div>
            
            {{-- Footer Actions --}}
            <div class="px-6 py-4 border-t border-slate-200/50 dark:border-slate-700/50 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <button type="button" onclick="rotateImage()" class="px-3 py-2 rounded-lg bg-slate-200 dark:bg-slate-700 hover:bg-slate-300 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-300 text-sm font-medium flex items-center gap-1.5 transition-colors">
                        <i data-lucide="rotate-cw" class="w-4 h-4"></i>
                        Putar
                    </button>
                </div>
                <div class="flex items-center gap-3">
                    <button type="button" onclick="closeCropModal()" class="px-4 py-2 rounded-lg text-slate-600 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-700 text-sm font-medium transition-colors">
                        Batal
                    </button>
                    <button type="button" onclick="saveCrop()" class="px-5 py-2 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-bold text-sm shadow-lg shadow-blue-500/25 flex items-center gap-1.5 transition-all">
                        <i data-lucide="check" class="w-4 h-4"></i>
                        Simpan
                    </button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/cropperjs@1.6.2/dist/cropper.min.css">
    <script src="https://cdn.jsdelivr.net/npm/cropperjs@1.6.2/dist/cropper.min.js"></script>
    <script>
        let cropper = null;

        document.addEventListener('DOMContentLoaded', function() {
            const fileInput = document.getElementById('foto-raw');
            
            fileInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (!file) return;

                // Validate file
                if (!file.type.match('image.*')) {
                    alert('File harus berupa gambar (JPG, PNG).');
                    fileInput.value = '';
                    return;
                }
                if (file.size > 2 * 1024 * 1024) {
                    alert('Ukuran file maksimal 2MB.');
                    fileInput.value = '';
                    return;
                }

                // Read file and open crop modal
                const reader = new FileReader();
                reader.onload = function(event) {
                    openCropModal(event.target.result);
                };
                reader.readAsDataURL(file);
            });

            // Re-initialize lucide icons for dynamically shown elements
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        });

        function openCropModal(imageSrc) {
            const modal = document.getElementById('crop-modal');
            const cropSource = document.getElementById('crop-source');
            
            // Destroy previous cropper
            if (cropper) {
                cropper.destroy();
                cropper = null;
            }

            cropSource.src = imageSrc;
            modal.classList.remove('hidden');
            modal.classList.add('flex');

            // Initialize Cropper after image loads
            cropSource.onload = function() {
                cropper = new Cropper(cropSource, {
                    aspectRatio: 1 / 1,
                    viewMode: 1,
                    dragMode: 'move',
                    autoCropArea: 1,
                    responsive: true,
                    restore: false,
                    guides: true,
                    center: true,
                    highlight: false,
                    cropBoxMovable: true,
                    cropBoxResizable: true,
                    toggleDragModeOnDblclick: false,
                    background: true,
                });
            };
        }

        function closeCropModal() {
            const modal = document.getElementById('crop-modal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            
            if (cropper) {
                cropper.destroy();
                cropper = null;
            }

            // Reset file input
            document.getElementById('foto-raw').value = '';
        }

        function rotateImage() {
            if (cropper) {
                cropper.rotate(90);
            }
        }

        function saveCrop() {
            if (!cropper) return;

            const canvas = cropper.getCroppedCanvas({
                width: 800,
                height: 800,
                imageSmoothingEnabled: true,
                imageSmoothingQuality: 'high',
            });

            const croppedDataURL = canvas.toDataURL('image/jpeg', 0.85);

            // Set hidden input value
            document.getElementById('foto-cropped-input').value = croppedDataURL;

            // Show preview
            document.getElementById('foto-preview-img').src = croppedDataURL;
            document.getElementById('foto-upload-area').classList.add('hidden');
            document.getElementById('foto-preview-area').classList.remove('hidden');

            // Re-initialize lucide icons for preview area
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }

            closeCropModal();
        }

        function removeCroppedPhoto() {
            document.getElementById('foto-cropped-input').value = '';
            document.getElementById('foto-preview-img').src = '';
            document.getElementById('foto-preview-area').classList.add('hidden');
            document.getElementById('foto-upload-area').classList.remove('hidden');
            document.getElementById('foto-raw').value = '';
        }
    </script>
    @endpush
</x-app-layout>
