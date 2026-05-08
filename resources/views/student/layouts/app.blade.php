<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') - BK SMKN 2 Mojokerto</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
</head>
<body class="bg-gray-100 min-h-screen">

    {{-- ═══════════════════════════════
         OVERLAY (mobile)
    ════════════════════════════════ --}}
    <div id="sidebar-overlay"
         class="fixed inset-0 bg-black/40 z-20 hidden lg:hidden"
         onclick="closeSidebar()"></div>

    {{-- ═══════════════════════════════
         SIDEBAR
    ════════════════════════════════ --}}
    <aside id="sidebar"
           class="fixed top-0 left-0 h-full w-64 bg-blue-700 z-30
                  transform -translate-x-full lg:translate-x-0
                  transition-transform duration-200 ease-in-out
                  flex flex-col">

        {{-- Brand --}}
        <div class="flex items-center gap-3 px-5 py-5 border-b border-blue-600">
            <div class="w-9 h-9 rounded-lg bg-white/20 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
            </div>
            <div>
                <p class="text-white font-semibold text-sm leading-tight">BK SMKN 2</p>
                <p class="text-blue-200 text-xs">Mojokerto</p>
            </div>
        </div>

        {{-- Navigation --}}
        <nav class="flex-1 px-3 py-4 space-y-0.5 overflow-y-auto">

            {{-- Dashboard --}}
            <a href="{{ route('student.dashboard') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors
                      {{ request()->routeIs('student.dashboard') ? 'bg-white/20 text-white' : 'text-blue-100 hover:bg-white/10 hover:text-white' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                Dashboard
            </a>

            {{-- Pelanggaran — dropdown --}}
            @php
                $violationsActive = request()->routeIs('student.violations') || request()->routeIs('student.violations.show') || request()->routeIs('student.report.*');
            @endphp

            <div x-data="{ open: {{ $violationsActive ? 'true' : 'false' }} }">

                {{-- Trigger --}}
                <button @click="open = !open"
                        class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors
                               {{ $violationsActive ? 'bg-white/20 text-white' : 'text-blue-100 hover:bg-white/10 hover:text-white' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <span class="flex-1 text-left">Pelanggaran</span>
                    <svg class="w-4 h-4 flex-shrink-0 transition-transform duration-200"
                         :class="open ? 'rotate-180' : ''"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>

                {{-- Sub-menu --}}
                <div x-show="open"
                     x-transition:enter="transition ease-out duration-150"
                     x-transition:enter-start="opacity-0 -translate-y-1"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-100"
                     x-transition:leave-start="opacity-100 translate-y-0"
                     x-transition:leave-end="opacity-0 -translate-y-1"
                     class="mt-0.5 ml-3 pl-5 border-l border-blue-500 space-y-0.5">

                    <a href="{{ route('student.violations') }}"
                       class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm font-medium transition-colors
                              {{ request()->routeIs('student.violations') || request()->routeIs('student.violations.show') ? 'bg-white/20 text-white' : 'text-blue-200 hover:bg-white/10 hover:text-white' }}">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M4 6h16M4 10h16M4 14h8"/>
                        </svg>
                        Riwayat Pelanggaran
                    </a>

                    <a href="{{ route('student.report.form') }}"
                       class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm font-medium transition-colors
                              {{ request()->routeIs('student.report.*') ? 'bg-white/20 text-white' : 'text-blue-200 hover:bg-white/10 hover:text-white' }}">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Lapor Pelanggaran
                    </a>

                </div>
            </div>

            {{-- Profil --}}
            <a href="{{ route('student.profile') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors
                      {{ request()->routeIs('student.profile') ? 'bg-white/20 text-white' : 'text-blue-100 hover:bg-white/10 hover:text-white' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                Profil Saya
            </a>

        </nav>

        {{-- Logout --}}
        <div class="px-3 pb-4 border-t border-blue-600 pt-3">
            <form method="POST" action="{{ route('student.logout') }}">
                @csrf
                <button type="submit"
                        class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium
                               text-blue-100 hover:bg-white/10 hover:text-white transition-colors">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    Keluar
                </button>
            </form>
        </div>
    </aside>

    {{-- ═══════════════════════════════
         MAIN CONTENT
    ════════════════════════════════ --}}
    <div class="lg:pl-64 min-h-screen flex flex-col">

        {{-- Top bar --}}
        <header class="sticky top-0 z-10 bg-white border-b border-gray-200 px-4 sm:px-6 lg:px-8 h-14 flex items-center justify-between">

            {{-- Kiri: hamburger (mobile) --}}
            <button onclick="openSidebar()"
                    class="lg:hidden text-gray-500 p-1 -ml-1 rounded-lg hover:bg-gray-100 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>

            {{-- Page title (desktop) --}}
            <span class="hidden lg:block text-sm font-medium text-gray-500">@yield('heading', 'Dashboard')</span>

            {{-- Kanan: profile dropdown --}}
            <div class="relative ml-auto" id="profile-menu-wrapper">
                <button id="profile-btn"
                        onclick="toggleProfileMenu()"
                        class="flex items-center gap-2.5 px-3 py-1.5 rounded-lg hover:bg-gray-100 transition-colors">
                    <div class="w-7 h-7 rounded-full bg-blue-600 flex items-center justify-center flex-shrink-0">
                        <span class="text-white text-xs font-semibold">
                            {{ strtoupper(substr(Auth::guard('student')->user()->name, 0, 1)) }}
                        </span>
                    </div>
                    <div class="hidden sm:block text-left">
                        <p class="text-sm font-medium text-gray-800 leading-tight">
                            {{ Auth::guard('student')->user()->name }}
                        </p>
                        <p class="text-xs text-gray-400 leading-tight">
                            {{ Auth::guard('student')->user()->class }}
                        </p>
                    </div>
                    <svg class="w-4 h-4 text-gray-400 hidden sm:block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>

                {{-- Dropdown --}}
                <div id="profile-dropdown"
                     class="hidden absolute right-0 mt-1.5 w-52 bg-white rounded-xl border border-gray-200 shadow-lg py-1 z-50">

                    <div class="px-4 py-3 border-b border-gray-100">
                        <p class="text-sm font-medium text-gray-900 truncate">{{ Auth::guard('student')->user()->name }}</p>
                        <p class="text-xs text-gray-400 mt-0.5">NISN: {{ Auth::guard('student')->user()->nisn }}</p>
                    </div>

                    <a href="{{ route('student.profile') }}"
                       class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        Profil Saya
                    </a>

                    <div class="border-t border-gray-100 mt-1 pt-1">
                        <form method="POST" action="{{ route('student.logout') }}">
                            @csrf
                            <button type="submit"
                                    class="w-full flex items-center gap-3 px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                </svg>
                                Keluar
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        {{-- Page content --}}
        <main class="flex-1 px-4 sm:px-6 lg:px-8 py-6 max-w-5xl w-full mx-auto">

            @hasSection('heading')
            <div class="mb-6">
                <h1 class="text-xl font-semibold text-gray-900">@yield('heading')</h1>
                @hasSection('subheading')
                <p class="text-sm text-gray-500 mt-0.5">@yield('subheading')</p>
                @endif
            </div>
            @endif

            @yield('content')
        </main>
    </div>

    {{-- ═══════════════════════════════
         SWEETALERT2 SCRIPT
    ════════════════════════════════ --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
    <script>
        /* ── SweetAlert: session flash ── */
        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: @json(session('success')),
                confirmButtonText: 'OK',
                confirmButtonColor: '#1d4ed8',
                timer: 5000,
                timerProgressBar: true,
            });
        @endif

        @if(session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: @json(session('error')),
                confirmButtonText: 'Tutup',
                confirmButtonColor: '#dc2626',
            });
        @endif

        @if(session('warning'))
            Swal.fire({
                icon: 'warning',
                title: 'Perhatian!',
                text: @json(session('warning')),
                confirmButtonText: 'OK',
                confirmButtonColor: '#d97706',
            });
        @endif

        /* ── Sidebar & dropdown ── */
        function openSidebar() {
            document.getElementById('sidebar').classList.remove('-translate-x-full');
            document.getElementById('sidebar-overlay').classList.remove('hidden');
        }
        function closeSidebar() {
            document.getElementById('sidebar').classList.add('-translate-x-full');
            document.getElementById('sidebar-overlay').classList.add('hidden');
        }
        function toggleProfileMenu() {
            document.getElementById('profile-dropdown').classList.toggle('hidden');
        }
        document.addEventListener('click', function(e) {
            const wrapper = document.getElementById('profile-menu-wrapper');
            if (wrapper && !wrapper.contains(e.target)) {
                document.getElementById('profile-dropdown').classList.add('hidden');
            }
        });
    </script>

    @stack('scripts')
</body>
</html>
