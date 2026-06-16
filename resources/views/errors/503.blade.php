@extends('errors.layout')

@section('title', __('Layanan Sedang Pemeliharaan'))
@section('code', '503')
@section('icon')
    <i data-lucide="wrench" class="w-12 h-12"></i>
@endsection
@section('message', __('Kami sedang melakukan pemeliharaan rutin untuk meningkatkan kualitas layanan. Kami akan segera kembali online. Terima kasih atas kesabaran Anda.'))

@section('game')
<div class="mt-8 pt-6 border-t border-slate-200 dark:border-slate-700/50">
    <div class="flex items-center justify-between mb-4">
        <h3 class="flex items-center gap-1.5 text-sm font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
            <i data-lucide="gamepad-2" class="w-4 h-4"></i>
            Mini Game
        </h3>
        <span class="text-xs text-slate-400 dark:text-slate-500">
            Langkah: <span id="move-count" class="font-bold text-slate-700 dark:text-slate-300">0</span>
        </span>
    </div>

    <div id="memory-grid" class="grid grid-cols-4 gap-2 max-w-[280px] mx-auto mb-3" style="perspective: 800px;"></div>

    <button onclick="resetGame()"
            class="flex items-center justify-center gap-1.5 text-xs px-4 py-2 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-700 transition-all hover:scale-105 active:scale-95 duration-200 font-medium mx-auto cursor-pointer">
        <i data-lucide="refresh-cw" class="w-3.5 h-3.5"></i>
        Mulai Ulang
    </button>
</div>

<style>
    .memory-card {
        aspect-ratio: 1;
        cursor: pointer;
        position: relative;
        transform-style: preserve-3d;
        transition: transform 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        border-radius: 0.75rem;
    }
    .memory-card:hover:not(.flipped):not(.matched) {
        transform: scale(1.05);
    }
    .memory-card.flipped, .memory-card.matched {
        transform: rotateY(180deg);
    }
    .memory-card .face {
        position: absolute;
        inset: 0;
        border-radius: 0.75rem;
        backface-visibility: hidden;
        -webkit-backface-visibility: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .memory-card .front {
        background: linear-gradient(135deg, #3b82f6, #4f46e5);
        box-shadow: 0 2px 8px rgba(59,130,246,0.3);
    }
    .dark .memory-card .front {
        box-shadow: 0 2px 8px rgba(59,130,246,0.15);
    }
    .memory-card .back {
        background: white;
        border: 1px solid #e2e8f0;
        transform: rotateY(180deg);
        font-size: 1.5rem;
    }
    .dark .memory-card .back {
        background: #1e293b;
        border-color: #334155;
    }
    .memory-card.matched .back {
        border-color: #4ade80;
        box-shadow: 0 0 0 2px rgba(74,222,128,0.3);
    }
</style>

<script>
    const emojis = ['🐶','🐱','🐼','🐸','🦊','🐯'],
          grid = document.getElementById('memory-grid'),
          moveEl = document.getElementById('move-count');
    let flipped = [], matched = 0, moves = 0, lock = false;

    function shuffle(a) {
        for (let i = a.length - 1; i > 0; i--) {
            const j = Math.floor(Math.random() * (i + 1));
            [a[i], a[j]] = [a[j], a[i]];
        }
        return a;
    }

    function initGame() {
        grid.innerHTML = '';
        flipped = []; matched = 0; moves = 0; lock = false;
        moveEl.textContent = '0';
        let values = shuffle([...emojis, ...emojis]);
        values.forEach((v) => {
            const el = document.createElement('div');
            el.className = 'memory-card';
            el.dataset.val = v;
            el.dataset.matched = 'false';
            el.innerHTML = '<div class="face front"></div><div class="face back">' + v + '</div>';
            el.addEventListener('click', () => flip(el));
            grid.appendChild(el);
        });
    }

    function flip(el) {
        if (lock || el.classList.contains('flipped') || el.dataset.matched === 'true') return;
        if (flipped.length >= 2) return;
        el.classList.add('flipped');
        flipped.push(el);
        if (flipped.length === 2) {
            moves++;
            moveEl.textContent = moves;
            checkMatch();
        }
    }

    function checkMatch() {
        lock = true;
        const [a, b] = flipped;
        if (a.dataset.val === b.dataset.val) {
            a.dataset.matched = 'true';
            b.dataset.matched = 'true';
            a.classList.add('matched');
            b.classList.add('matched');
            matched++;
            flipped = [];
            lock = false;
            if (matched === emojis.length) setTimeout(() => alert('🎉 Selamat! Semua berhasil dipasangkan!'), 400);
        } else {
            setTimeout(() => {
                a.classList.remove('flipped');
                b.classList.remove('flipped');
                flipped = [];
                lock = false;
            }, 700);
        }
    }

    function resetGame() { initGame(); }
    document.addEventListener('DOMContentLoaded', initGame);
</script>
@endsection
