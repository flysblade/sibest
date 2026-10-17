<aside id="sidebar"
    class="hidden lg:flex w-64 shrink-0 bg-navy border-r border-white/10 flex-col fixed inset-y-0 left-0 transition-all duration-300 z-40">

    {{-- Logo --}}
    <div id="sidebarHeader" class="h-20 px-6 flex items-center border-b border-white/10 transition-all duration-300">
        <div class="flex items-center gap-3 min-w-0 w-full">
            <img src="{{ asset('assets/logo.jpg') }}" alt="Logo UNUJA"
                class="w-10 h-10 rounded-full object-cover shrink-0">
            <div class="sidebar-text">
                <p class="text-white font-extrabold text-lg leading-none">SIBEST</p>
                <p class="text-gold text-[10px] font-bold uppercase tracking-wide mt-1">Admin</p>
            </div>
        </div>
    </div>

    {{-- Toggle --}}
    <button id="sidebarToggle" type="button" aria-label="Toggle sidebar"
        class="absolute -right-3 top-6 w-7 h-7 z-50 rounded-full bg-white border border-gray-200 shadow-md flex items-center justify-center text-navy hover:bg-gray-50 transition">
        <i data-lucide="chevron-left" id="sidebarToggleIcon" class="w-4 h-4"></i>
    </button>

    {{-- Menu --}}
    <nav class="flex-1 p-4 space-y-1 overflow-y-auto">
        <p class="sidebar-text px-3 pt-2 pb-3 text-[11px] font-bold uppercase tracking-wider text-slate-400">
            Menu Utama
        </p>

        @php
            $menus = [
                ['url' => route('admin.dashboard'),         'match' => 'admin.dashboard',        'icon' => 'layout-dashboard', 'label' => 'Dashboard'],
                ['url' => '#',                              'match' => null,                     'icon' => 'tags',             'label' => 'Kategori'],
                ['url' => '#',                              'match' => null,                     'icon' => 'graduation-cap',   'label' => 'Program Beasiswa'],
                ['url' => '#',                              'match' => null,                     'icon' => 'calendar-days',    'label' => 'Offering & Periode'],
                ['url' => route('admin.pengajuan.index'),   'match' => 'admin.pengajuan.*',      'icon' => 'clipboard-list',   'label' => 'Pengajuan'],
                ['url' => route('admin.user.index'),        'match' => 'admin.user.*',           'icon' => 'users',             'label' => 'Kelola User'],
                ['url' => '#',                              'match' => null,                     'icon' => 'megaphone',        'label' => 'Pengumuman'],
            ];
        @endphp

        @foreach($menus as $menu)
            @php $isActive = $menu['match'] && request()->routeIs($menu['match']); @endphp

            <a href="{{ $menu['url'] }}" title="{{ $menu['label'] }}"
                class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-colors
                {{ $isActive
                    ? 'rounded-tr-lg rounded-br-lg border-l-[3px] border-gold bg-[#2c416b] pl-[9px] text-white font-bold'
                    : 'text-slate-200 hover:bg-white/5 font-medium' }}">

                <i data-lucide="{{ $menu['icon'] }}" class="w-5 h-5 shrink-0"></i>
                <span class="sidebar-text">{{ $menu['label'] }}</span>

                {{-- Badge "Soon" buat menu yang belum ada --}}
                @if($menu['url'] === '#')
                    <span class="sidebar-text ml-auto px-1.5 py-0.5 rounded-full bg-gold/20 text-gold text-[9px] font-bold uppercase">
                        Soon
                    </span>
                @endif
            </a>
        @endforeach
    </nav>

    {{-- Akun --}}
    <div class="p-4 border-t border-white/10 relative">
        <button type="button" id="accountMenuButton"
            class="w-full flex items-center gap-3 px-2 py-2 rounded-lg hover:bg-white/5 text-left transition-colors">

            <div class="w-9 h-9 rounded-full bg-gold text-navy flex items-center justify-center font-bold text-sm shrink-0">
                {{ strtoupper(substr(auth()->user()->nama ?? 'A', 0, 2)) }}
            </div>

            <div class="sidebar-text min-w-0 flex-1">
                <p class="text-sm font-bold text-white truncate">{{ auth()->user()->nama ?? 'Administrator' }}</p>
                <p class="text-xs text-slate-300 truncate">{{ auth()->user()->username ?? '-' }}</p>
            </div>

            <i data-lucide="chevron-up" id="accountMenuChevron" class="sidebar-text w-4 h-4 text-slate-300 shrink-0"></i>
        </button>

        <div id="accountMenu"
            class="hidden absolute left-4 right-4 bottom-[72px] bg-white rounded-xl border border-border-light shadow-lg overflow-hidden z-50">

            <a href="#" class="flex items-center gap-3 px-4 py-3 text-sm font-semibold text-gray-700 hover:bg-gray-50">
                <i data-lucide="user-round" class="w-4 h-4 text-navy"></i> Profil
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

{{-- Script sidebar (copy dari sidebar-mahasiswa, samain) --}}
<script>
(function () {
    function refreshIcons() {
        if (window.lucide && typeof window.lucide.createIcons === 'function') {
            window.lucide.createIcons();
        }
    }

    function init() {
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
        }

        var sidebar     = document.getElementById('sidebar');
        var mainContent = document.getElementById('mainContent');
        var toggle      = document.getElementById('sidebarToggle');
        var toggleIcon  = document.getElementById('sidebarToggleIcon');
        var sidebarHead = document.getElementById('sidebarHeader');

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
                try { localStorage.setItem('sibest-sidebar-collapsed-admin', collapsed ? '1' : '0'); } catch (e) {}
                refreshIcons();
            }

            toggle.onclick = function () {
                setSidebar(!sidebar.classList.contains('w-20'));
            };

            var saved = '0';
            try { saved = localStorage.getItem('sibest-sidebar-collapsed-admin') || '0'; } catch (e) {}
            setSidebar(saved === '1');
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
</script>