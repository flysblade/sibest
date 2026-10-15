<aside id="sidebar"
    class="hidden lg:flex w-64 shrink-0 bg-blue-950 border-r border-white/10 flex-col fixed inset-y-0 left-0 transition-all duration-300 z-40">

    {{-- Logo --}}
    <div id="sidebarHeader" class="h-20 px-6 flex items-center border-b border-white/10 transition-all duration-300">
        <div class="flex items-center gap-3 min-w-0 w-full">
            <img src="{{ asset('assets/logo.jpg') }}" alt="Logo UNUJA"
                class="w-10 h-10 rounded-full object-cover shrink-0">
            <div class="sidebar-text">
                <p class="text-white font-extrabold text-lg leading-none">SIBEST</p>
                <p class="text-gold text-[10px] font-bold uppercase tracking-wide mt-1">UNUJA</p>
            </div>
        </div>
    </div>

    {{-- Toggle --}}
    <button id="sidebarToggle" type="button" aria-label="Toggle sidebar"
        class="absolute -right-3 top-6 w-7 h-7 z-50 rounded-full bg-white border border-gray-200 shadow-md flex items-center justify-center text-blue-950 hover:bg-gray-50 transition">
        <i data-lucide="chevron-left" id="sidebarToggleIcon" class="w-4 h-4"></i>
    </button>

    {{-- Menu --}}
    <nav class="flex-1 p-4 space-y-1 overflow-y-auto">
        <p class="sidebar-text px-3 pt-2 pb-3 text-[11px] font-bold uppercase tracking-wider text-slate-400">
            Menu Utama
        </p>

        @php
            $menus = [
                ['url' => route('mahasiswa.dashboard'),        'match' => 'mahasiswa.dashboard',    'icon' => 'layout-dashboard', 'label' => 'Dashboard'],
                ['url' => route('mahasiswa.beasiswa.index'),   'match' => 'mahasiswa.beasiswa.*',   'icon' => 'graduation-cap',   'label' => 'Daftar Beasiswa'],
                ['url' => route('mahasiswa.pengajuan.index'),  'match' => 'mahasiswa.pengajuan.*',  'icon' => 'file-text',        'label' => 'Pengajuan'],
                ['url' => route('mahasiswa.tracking.index'),   'match' => 'mahasiswa.tracking.*',   'icon' => 'route',            'label' => 'Tracking'],
                ['url' => route('mahasiswa.riwayat.index'),    'match' => 'mahasiswa.riwayat.*',   'icon' => 'history',         'label' => 'Riwayat'],
                ['url' => route('mahasiswa.pengumuman.index'), 'match' => 'mahasiswa.pengumuman.*', 'icon' => 'megaphone', 'label' => 'Pengumuman', 'badge' => $jumlahPengumuman ?? 0],
            ];
        @endphp

        @foreach($menus as $menu)
            @php $isActive = $menu['match'] && request()->routeIs($menu['match']); @endphp

            <a href="{{ $menu['url'] }}" title="{{ $menu['label'] }}"
                class="sidebar-link flex items-center {{ isset($menu['badge']) ? 'justify-between' : '' }} gap-3 px-3 py-2.5 rounded-lg text-sm transition-colors
                {{ $isActive
                    ? 'rounded-tr-lg rounded-br-lg border-l-[3px] border-gold bg-cyan-900 pl-[9px] text-white font-bold'
                    : 'text-slate-200 hover:bg-white/5 font-medium' }}">
                <span class="flex items-center gap-3 min-w-0">
                    <i data-lucide="{{ $menu['icon'] }}" class="w-5 h-5 shrink-0"></i>
                    <span class="sidebar-text">{{ $menu['label'] }}</span>
                </span>
                @if(isset($menu['badge']) && $menu['badge'] > 0)
                    <span class="sidebar-text min-w-5 h-5 px-1.5 rounded-full bg-red-500 text-white text-[10px] font-bold inline-flex items-center justify-center">
                        {{ $menu['badge'] }}
                    </span>
                @endif
            </a>
        @endforeach
    </nav>

    {{-- Akun --}}
    <div class="p-4 border-t border-white/10 relative">
        <button type="button" id="accountMenuButton"
            class="w-full flex items-center gap-3 px-2 py-2 rounded-lg hover:bg-white/5 text-left transition-colors">
            <div class="w-9 h-9 rounded-full bg-navy text-white flex items-center justify-center font-bold text-sm shrink-0">
                {{ strtoupper(substr(auth()->user()->nama ?? 'M', 0, 2)) }}
            </div>
            <div class="sidebar-text min-w-0 flex-1">
                <p class="text-sm font-bold text-white truncate">{{ auth()->user()->nama ?? 'Mahasiswa' }}</p>
                <p class="text-xs text-slate-300 truncate">{{ auth()->user()->username ?? '-' }}</p>
            </div>
            <i data-lucide="chevron-up" id="accountMenuChevron" class="sidebar-text w-4 h-4 text-slate-300 shrink-0"></i>
        </button>

        <div id="accountMenu"
            class="hidden absolute left-4 right-4 bottom-[72px] bg-white rounded-xl border border-border-light shadow-lg overflow-hidden z-50">
            <a href="#" class="flex items-center gap-3 px-4 py-3 text-sm font-semibold text-gray-700 hover:bg-gray-50">
                <i data-lucide="user-round" class="w-4 h-4 text-navy"></i> Profil
            </a>
            <a href="#" class="flex items-center gap-3 px-4 py-3 text-sm font-semibold text-gray-700 hover:bg-gray-50">
                <i data-lucide="settings" class="w-4 h-4 text-navy"></i> Pengaturan
            </a>
            <div class="border-t border-border-light"></div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                    class="w-full flex items-center gap-3 px-4 py-3 text-sm font-semibold text-red-600 hover:bg-red-50 text-left">
                    <i data-lucide="log-out" class="w-4 h-4"></i> Keluar
                </button>
            </form>
        </div>
    </div>
</aside>


<script>
(function () {
    function refreshIcons() {
        if (window.lucide && typeof window.lucide.createIcons === 'function') {
            window.lucide.createIcons();
        }
    }

    function init() {
        // ---- Dropdown Akun ----
        var btn  = document.getElementById('accountMenuButton');
        var menu = document.getElementById('accountMenu');
        var chev = document.getElementById('accountMenuChevron');

        if (btn && menu && chev) {
            btn.onclick = function (e) {
                e.preventDefault();
                e.stopPropagation();
                var isHidden = menu.classList.contains('hidden');
                if (isHidden) {
                    menu.classList.remove('hidden');
                    chev.setAttribute('data-lucide', 'chevron-down');
                } else {
                    menu.classList.add('hidden');
                    chev.setAttribute('data-lucide', 'chevron-up');
                }
                refreshIcons();
            };

            document.addEventListener('click', function (e) {
                if (!btn.contains(e.target) && !menu.contains(e.target)) {
                    menu.classList.add('hidden');
                    chev.setAttribute('data-lucide', 'chevron-up');
                    refreshIcons();
                }
            });

            console.log('[SIBEST] Dropdown akun siap ✅');
        } else {
            console.warn('[SIBEST] Dropdown GAK DITEMUKAN', { btn: !!btn, menu: !!menu, chev: !!chev });
        }

        // ---- Toggle Sidebar ----
        var sidebar     = document.getElementById('sidebar');
        var mainContent = document.getElementById('mainContent');
        var toggle      = document.getElementById('sidebarToggle');
        var toggleIcon  = document.getElementById('sidebarToggleIcon');
        var sidebarHead = document.getElementById('sidebarHeader');

        console.log('[SIBEST] Cek elemen toggle:', {
            sidebar: !!sidebar,
            mainContent: !!mainContent,
            toggle: !!toggle
        });

        if (sidebar && mainContent && toggle) {
            function setSidebar(collapsed) {
                var texts = document.querySelectorAll('.sidebar-text');
                if (collapsed) {
                    sidebar.classList.remove('w-64');         sidebar.classList.add('w-20');
                    mainContent.classList.remove('lg:ml-64'); mainContent.classList.add('lg:ml-20');
                    texts.forEach(function (el) { el.classList.add('hidden'); });
                    if (sidebarHead) {
                        sidebarHead.classList.remove('px-6');
                        sidebarHead.classList.add('px-0', 'justify-center');
                        var inner = sidebarHead.querySelector('div');
                        if (inner) { inner.classList.remove('gap-3'); inner.classList.add('justify-center'); }
                    }
                    if (menu) {
                        menu.classList.remove('left-4', 'right-4', 'bottom-[72px]');
                        menu.classList.add('left-full', 'ml-2', 'bottom-4', 'w-56', 'z-[100]');
                    }
                    if (toggleIcon) toggleIcon.setAttribute('data-lucide', 'chevron-right');
                } else {
                    sidebar.classList.remove('w-20');         sidebar.classList.add('w-64');
                    mainContent.classList.remove('lg:ml-20'); mainContent.classList.add('lg:ml-64');
                    texts.forEach(function (el) { el.classList.remove('hidden'); });
                    if (sidebarHead) {
                        sidebarHead.classList.remove('px-0', 'justify-center');
                        sidebarHead.classList.add('px-6');
                        var inner = sidebarHead.querySelector('div');
                        if (inner) { inner.classList.remove('justify-center'); inner.classList.add('gap-3'); }
                    }
                    if (menu) {
                        menu.classList.remove('left-full', 'ml-2', 'bottom-4', 'w-56', 'z-[100]');
                        menu.classList.add('left-4', 'right-4', 'bottom-[72px]');
                    }
                    if (toggleIcon) toggleIcon.setAttribute('data-lucide', 'chevron-left');
                }
                try { localStorage.setItem('sibest-sidebar-collapsed', collapsed ? '1' : '0'); } catch (e) {}
                refreshIcons();
            }

            toggle.onclick = function () {
                setSidebar(!sidebar.classList.contains('w-20'));
            };

            var saved = '0';
            try { saved = localStorage.getItem('sibest-sidebar-collapsed') || '0'; } catch (e) {}
            setSidebar(saved === '1');

            console.log('[SIBEST] Toggle sidebar siap ✅');
        } else {
            console.warn('[SIBEST] Toggle GAK BISA di-set, ada elemen null');
        }
    }

    // Tunggu DOM ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
</script>