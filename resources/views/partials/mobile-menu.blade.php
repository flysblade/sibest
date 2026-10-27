{{-- Tombol Hamburger (Mobile Only) --}}
<button id="mobileMenuButton" type="button" aria-label="Buka menu"
    class="lg:hidden w-10 h-10 rounded-lg border border-border-light flex items-center justify-center text-navy hover:bg-gray-50 shrink-0 mr-2">
    <i data-lucide="menu" class="w-5 h-5"></i>
</button>

{{-- Backdrop (Layar Gelap) --}}
<div id="mobileMenuBackdrop" class="hidden lg:hidden fixed inset-0 bg-black/50 z-[45]"></div>

{{-- Script Toggle --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    const btn = document.getElementById('mobileMenuButton');
    const sidebar = document.getElementById('sidebar');
    const backdrop = document.getElementById('mobileMenuBackdrop');

    // Kalau elemen gak ada (misal di halaman login), skip
    if (!btn || !sidebar || !backdrop) return;

    function openMenu() {
        sidebar.classList.remove('hidden');
        sidebar.classList.add('flex');
        backdrop.classList.remove('hidden');
        document.body.style.overflow = 'hidden'; // Kunci scroll body
    }

    function closeMenu() {
        // Cuma sembunyiin kalau di mobile (layar < 1024px)
        if (window.innerWidth < 1024) {
            sidebar.classList.add('hidden');
            sidebar.classList.remove('flex');
        }
        backdrop.classList.add('hidden');
        document.body.style.overflow = ''; // Balikin scroll
    }

    // Klik tombol -> Buka
    btn.addEventListener('click', function (e) {
        e.stopPropagation();
        openMenu();
    });

    // Klik backdrop -> Tutup
    backdrop.addEventListener('click', closeMenu);

    // Klik link menu -> Tutup (biar pas pindah halaman, sidebar ketutup)
    sidebar.querySelectorAll('a').forEach(function (link) {
        link.addEventListener('click', closeMenu);
    });
});
</script>