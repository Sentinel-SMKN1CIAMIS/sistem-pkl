@php
    $toastUser = auth()->user();
    $showPokjaActivityToast = $toastUser && ($toastUser->activeWorkspace() === 'pokja' || $toastUser->role === 'super_admin');
@endphp

@if($showPokjaActivityToast)
    <div id="pokja-activity-toast-container"
         class="fixed bottom-5 right-5 z-[90] flex flex-col gap-2.5 max-w-sm w-[calc(100vw-2.5rem)] pointer-events-none">
    </div>

    <script>
        (function() {
            const container = document.getElementById('pokja-activity-toast-container');
            if (!container) return;

            let afterId = parseInt(sessionStorage.getItem('pokja_activity_after_id') || '0', 10);

            function escapeHtml(str) {
                const div = document.createElement('div');
                div.textContent = str ?? '';
                return div.innerHTML;
            }

            function renderToast(ev) {
                const colorMap = {
                    CREATED: { badge: 'bg-emerald-600 text-white', label: 'Tambah', border: 'border-emerald-200 dark:border-emerald-800' },
                    UPDATED: { badge: 'bg-blue-600 text-white', label: 'Ubah', border: 'border-blue-200 dark:border-blue-800' },
                    DELETED: { badge: 'bg-red-600 text-white', label: 'Hapus', border: 'border-red-200 dark:border-red-800' },
                };
                const style = colorMap[ev.action] || colorMap.UPDATED;

                const toast = document.createElement('div');
                toast.className = `pointer-events-auto p-4 rounded-2xl bg-white dark:bg-slate-900 border ${style.border} shadow-xl flex items-start gap-3 transition-all duration-300 opacity-0 translate-y-2`;
                toast.innerHTML = `
                    <div class="shrink-0 mt-0.5">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-lg text-[10px] font-bold uppercase ${style.badge}">${style.label}</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-extrabold text-slate-900 dark:text-white truncate">${escapeHtml(ev.actor)}</p>
                        <p class="text-xs text-slate-600 dark:text-slate-300 mt-0.5 leading-snug">${escapeHtml(ev.summary)}</p>
                        <p class="text-[10px] text-slate-400 mt-1">${escapeHtml(ev.time)}</p>
                    </div>
                    <button type="button" class="shrink-0 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 cursor-pointer p-0.5" aria-label="Tutup">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                    </button>
                `;

                const closeBtn = toast.querySelector('button');
                const removeToast = () => {
                    toast.classList.add('opacity-0', 'translate-y-2');
                    setTimeout(() => toast.remove(), 250);
                };
                closeBtn.addEventListener('click', removeToast);

                container.appendChild(toast);
                requestAnimationFrame(() => {
                    toast.classList.remove('opacity-0', 'translate-y-2');
                });

                setTimeout(removeToast, 7000);
            }

            async function pollPokjaActivity() {
                if (document.hidden) return;
                try {
                    const url = "{{ route('pokja.activity-feed') }}?after_id=" + encodeURIComponent(afterId);
                    const res = await fetch(url, {
                        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                    });
                    if (!res.ok) return;
                    const data = await res.json();

                    if (typeof data.latest_id === 'number' && data.latest_id > afterId) {
                        afterId = data.latest_id;
                        sessionStorage.setItem('pokja_activity_after_id', String(afterId));
                    }

                    if (Array.isArray(data.events) && data.events.length > 0) {
                        data.events.forEach(renderToast);
                    }
                } catch (err) {
                    // Fail silently on transient network issues
                }
            }

            document.addEventListener('DOMContentLoaded', () => {
                pollPokjaActivity();
                setInterval(pollPokjaActivity, 10000);
            });
        })();
    </script>
@endif
