<x-app-layout>
    <x-slot name="header">Buku Panduan PKL</x-slot>

    <div class="mb-6">
        <p class="text-slate-600 dark:text-slate-400">Baca buku panduan dan unduh dokumen teknis yang relevan dengan PKL Anda.</p>
    </div>

    <div class="glass-card mb-8 p-4 md:p-6 flex flex-col" style="height: 80vh; min-height: 600px;">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-bold text-slate-900 dark:text-slate-100 flex items-center gap-2">
                <i data-lucide="book-open" class="w-5 h-5 text-blue-500"></i>
                Buku Pedoman PKL 2025-2026
            </h3>
            <a href="{{ asset('BUKU%20PEDOMAN%20PKL%202025-2026.pdf') }}" download
               class="flex items-center gap-2 px-4 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-lg text-sm font-semibold transition-all shadow-sm">
                <i data-lucide="download" class="w-4 h-4"></i>
                Unduh PDF
            </a>
        </div>
        <div class="w-full flex-grow rounded-xl overflow-y-auto border border-slate-200 dark:border-slate-700 bg-slate-100 dark:bg-slate-900 p-4" style="height: 100%; min-height: 500px;" id="pdf-container">
            <div id="pdf-loader" class="flex flex-col items-center justify-center h-full text-slate-500">
                <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-500 mb-4"></div>
                <p>Memuat dokumen PDF...</p>
            </div>
            <!-- Canvas elements will be appended here -->
        </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';

            const url = '{{ asset("BUKU PEDOMAN PKL 2025-2026.pdf") }}';
            const pdfContainer = document.getElementById('pdf-container');
            const pdfLoader = document.getElementById('pdf-loader');

            let pdfDoc = null;

            // Render the page
            const renderPage = num => {
                return pdfDoc.getPage(num).then(page => {
                    let viewport = page.getViewport({ scale: 1 });
                    
                    // Responsive scale based on container width
                    // leave some padding (32px)
                    let containerWidth = pdfContainer.clientWidth - 32; 
                    let autoScale = containerWidth / viewport.width;
                    
                    // Limit max scale to 2.0 to avoid huge blurry rendering on large screens
                    let finalScale = Math.min(autoScale, 2.0);
                    
                    viewport = page.getViewport({ scale: finalScale });

                    const wrapper = document.createElement('div');
                    wrapper.className = 'flex justify-center mb-6';

                    const canvas = document.createElement('canvas');
                    const ctx = canvas.getContext('2d');
                    canvas.height = viewport.height;
                    canvas.width = viewport.width;
                    canvas.className = 'shadow-md rounded bg-white max-w-full';

                    wrapper.appendChild(canvas);
                    pdfContainer.appendChild(wrapper);

                    const renderCtx = {
                        canvasContext: ctx,
                        viewport: viewport
                    };

                    return page.render(renderCtx).promise;
                });
            };

            // Load document
            pdfjsLib.getDocument(url).promise.then(pdfDoc_ => {
                pdfDoc = pdfDoc_;
                pdfLoader.style.display = 'none';

                // Render all pages sequentially
                let promise = Promise.resolve();
                for (let i = 1; i <= pdfDoc.numPages; i++) {
                    promise = promise.then(() => renderPage(i));
                }
            }).catch(err => {
                console.error('Error loading PDF: ', err);
                pdfLoader.innerHTML = `
                    <div class="text-red-500 text-center p-4">
                        <i data-lucide="alert-circle" class="w-12 h-12 mx-auto mb-2 text-red-400"></i>
                        <p class="font-semibold">Gagal memuat PDF.</p>
                        <p class="text-sm mt-1">${err.message}</p>
                        <a href="${url}" download class="inline-block mt-4 px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg transition-colors">Unduh Manual PDF</a>
                    </div>
                `;
                if(window.lucide) {
                    window.lucide.createIcons();
                }
            });
            
            // Re-render on resize with debounce
            let resizeTimer;
            window.addEventListener('resize', () => {
                clearTimeout(resizeTimer);
                resizeTimer = setTimeout(() => {
                    if (pdfDoc) {
                        // Clear container
                        pdfContainer.innerHTML = '';
                        // Re-render pages
                        let promise = Promise.resolve();
                        for (let i = 1; i <= pdfDoc.numPages; i++) {
                            promise = promise.then(() => renderPage(i));
                        }
                    }
                }, 500);
            });
        });
    </script>

    @if($panduans->count() > 0)
    <div class="mb-4">
        <h3 class="text-lg font-bold text-slate-900 dark:text-slate-100">Dokumen Panduan Tambahan</h3>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($panduans as $item)
            <div class="glass-card p-6 flex flex-col justify-between">
                <div>
                    <div class="flex items-center gap-3 mb-4 text-red-400">
                        <div class="p-3 bg-red-400/10 rounded-2xl">
                            <i data-lucide="file-text" class="w-6 h-6"></i>
                        </div>
                        <span class="text-xs font-bold uppercase tracking-widest text-slate-500 dark:text-slate-400">Document</span>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 dark:text-slate-100 mb-2 leading-tight">{{ $item->judul }}</h3>
                    <p class="text-slate-600 dark:text-slate-400 text-sm mb-6 line-clamp-3 italic">{{ $item->deskripsi ?? 'Tidak ada deskripsi.' }}</p>
                </div>
                
                <a href="{{ asset('storage/' . $item->file_path) }}" target="_blank" 
                   class="w-full flex items-center justify-center gap-2 px-5 py-3 bg-blue-600 hover:bg-blue-500 text-white font-bold rounded-xl shadow-lg shadow-blue-500/20 transition-all active:scale-95">
                    <i data-lucide="download" class="w-5 h-5"></i>
                    UNDUH PDF
                </a>
            </div>
        @endforeach
    </div>
    @endif
</x-app-layout>
