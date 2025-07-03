<!-- Desktop sidebar -->
<aside class="z-20 hidden w-64 overflow-y-auto bg-slate-800 dark:bg-gray-800 md:block flex-shrink-0">
    <div class="text-white flex-col justify-center align-middle  dark:text-gray-400">
        <a href="{{ route('dashboard') }}" class="flex flex-col items-center justify-center my-4">
            <h1 class="text-2xl font-bold text-white dark:text-white">Dashboard Admin</h1>
        </a>
        <ul class="mt-6">
            <li class="relative px-6 py-3">
                @if (Request::is('dashboard'))
                    <span class="absolute inset-y-0 left-0 w-1 bg-white rounded-tr-lg rounded-br-lg"
                        aria-hidden="true"></span>
                @endif
                <a class="{{ Request::is('dashboard') ? 'font-semibold text-teal-400 dark:text-teal-300' : 'text-white dark:text-gray-100' }} inline-flex items-center w-full text-sm transition-colors duration-150 hover:text-teal-600 dark:hover:text-teal-300"
                    href="{{ route('dashboard') }}">
                    <i class="ri-home-4-line text-lg"></i>
                    <span class="ml-4">Dashboard</span>
                </a>
            </li>
        </ul>
        @if (auth()->user()->role !== 'user')
            <ul>
                {{-- Data Master --}}
                <li class="w-full mt-6">
                    <h6 class="pl-6 font-bold leading-tight uppercase text-xs opacity-60">Master</h6>
                </li>
                <li class="relative px-6 pt-3">
                    @if (Request::is('dashboard/kriteria'))
                        <span class="absolute inset-y-0 left-0 w-1 bg-white rounded-tr-lg rounded-br-lg"
                            aria-hidden="true"></span>
                    @endif
                    <a class="{{ Request::is('dashboard/kriteria') ? 'font-semibold text-teal-400 dark:text-teal-300' : 'text-white dark:text-gray-100' }} inline-flex items-center w-full text-sm transition-colors duration-150 hover:text-teal-600 dark:hover:text-teal-300"
                        href="{{ route('kriteria') }}">
                        <i class="ri-table-line text-lg"></i>
                        <span class="ml-4">Kriteria</span>
                    </a>
                </li>
          

                {{-- Data WP --}}
                <li class="w-full mt-6">
                    <h6 class="pl-6 font-bold leading-tight uppercase text-xs opacity-60">WP</h6>
                </li>
                <li class="relative px-6 pt-3">
                    @if (Request::is('dashboard/alternatif*'))
                        <span class="absolute inset-y-0 left-0 w-1 bg-white rounded-tr-lg rounded-br-lg"
                            aria-hidden="true"></span>
                    @endif
                    <a class="{{ Request::is('dashboard/alternatif*') ? 'font-semibold text-teal-400 dark:text-teal-300' : 'text-white dark:text-gray-100' }} inline-flex items-center w-full text-sm transition-colors duration-150 hover:text-teal-600 dark:hover:text-teal-300"
                        href="{{ route('alternatif') }}">
                        <i class="ri-braces-line text-lg"></i>
                        <span class="ml-4">Alternatif</span>
                    </a>
                </li>
                <li class="relative px-6 pt-3">
                    @if (Request::is('dashboard/hasil_akhir*'))
                        <span class="absolute inset-y-0 left-0 w-1 bg-white rounded-tr-lg rounded-br-lg"
                            aria-hidden="true"></span>
                    @endif
                    <a class="{{ Request::is('dashboard/hasil_akhir*') ? 'font-semibold text-teal-400 dark:text-teal-300' : 'text-white dark:text-gray-100' }} inline-flex items-center w-full text-sm transition-colors duration-150 hover:text-teal-600 dark:hover:text-teal-300"
                        href="{{ route('penilaian.hasil_akhir') }}">
                        <i class="ri-bar-chart-2-line text-lg"></i>
                        <span class="ml-4">Hasil Akhir</span>
                    </a>
                </li>

                <li class="w-full mt-6">
                    <h6 class="pl-6 font-bold leading-tight uppercase text-xs opacity-60">Master Data</h6>
                </li>
                <li class="relative px-6 pt-3">
                    @if (Request::is('dashboard/user*'))
                        <span class="absolute inset-y-0 left-0 w-1 bg-white rounded-tr-lg rounded-br-lg"
                            aria-hidden="true"></span>
                    @endif
                    <a class="{{ Request::is('dashboard/user*') ? 'font-semibold text-teal-400 dark:text-teal-300' : 'text-white dark:text-gray-100' }} inline-flex items-center w-full text-sm transition-colors duration-150 hover:text-teal-600 dark:hover:text-teal-300"
                        href="{{ route('user') }}">
                        <i class="ri-user-3-line text-lg"></i>
                        <span class="ml-4">Pengguna</span>
                    </a>
                </li>
            </ul>
        @else
            <ul>
                <li class="relative px-6 pt-3">
                    @if (Request::is('dashboard/hasil_akhir*'))
                        <span class="absolute inset-y-0 left-0 w-1 bg-white rounded-tr-lg rounded-br-lg"
                            aria-hidden="true"></span>
                    @endif
                    <a class="{{ Request::is('dashboard/hasil_akhir*') ? 'font-semibold text-teal-400 dark:text-teal-300' : 'text-white dark:text-gray-100' }} inline-flex items-center w-full text-sm transition-colors duration-150 hover:text-teal-600 dark:hover:text-teal-300"
                        href="{{ route('penilaian.hasil_akhir') }}">
                        <i class="ri-bar-chart-2-line text-lg"></i>
                        <span class="ml-4">Hasil Akhir</span>
                    </a>
                </li>
            </ul>
        @endif
    </div>
</aside>

<!-- Mobile sidebar -->
<div x-show="isSideMenuOpen" x-transition:enter="transition ease-in-out duration-150"
    x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in-out duration-150" x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    class="fixed inset-0 z-10 flex items-end bg-black bg-opacity-50 sm:items-center sm:justify-center">
</div>
<aside class="fixed inset-y-0 z-20 flex-shrink-0 w-64 mt-16 overflow-y-auto bg-slate-800 dark:bg-gray-800 md:hidden"
    x-show="isSideMenuOpen" x-transition:enter="transition ease-in-out duration-150"
    x-transition:enter-start="opacity-0 transform -translate-x-20" x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in-out duration-150" x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0 transform -translate-x-20" @click.away="closeSideMenu"
    @keydown.escape="closeSideMenu">
    <div class="py-4 text-white dark:text-gray-400">
        <a href="{{ route('dashboard') }}" class="flex flex-col items-center justify-center my-4">
            <img src="{{ asset('favicon/favicon.svg') }}" alt="Logo" class="w-20 h-20 mb-4">
            <h1 class="text-2xl font-bold text-gray-700 dark:text-white">Dashboard Admin</h1>
        </a>
        <ul class="mt-6">
            <li class="relative px-6 py-3">
                @if (Request::is('dashboard'))
                    <span class="absolute inset-y-0 left-0 w-1 bg-white rounded-tr-lg rounded-br-lg"
                        aria-hidden="true"></span>
                @endif
                <a class="{{ Request::is('dashboard') ? 'font-semibold text-teal-400 dark:text-teal-300' : 'text-white dark:text-gray-100' }} inline-flex items-center w-full text-sm transition-colors duration-150 hover:text-teal-600 dark:hover:text-teal-300"
                    href="{{ route('dashboard') }}">
                    <i class="ri-home-4-line text-lg"></i>
                    <span class="ml-4">Dashboard</span>
                </a>
            </li>
        </ul>

        @if (auth()->user()->role !== 'User')
            <ul>
                {{-- Data Master --}}
                <li class="w-full mt-6">
                    <h6 class="pl-6 font-bold leading-tight uppercase text-xs opacity-60">Master</h6>
                </li>
                <li class="relative px-6 pt-3">
                    @if (Request::is('dashboard/kriteria'))
                        <span class="absolute inset-y-0 left-0 w-1 bg-white rounded-tr-lg rounded-br-lg"
                            aria-hidden="true"></span>
                    @endif
                    <a class="{{ Request::is('dashboard/kriteria') ? 'font-semibold text-teal-400 dark:text-teal-300' : 'text-white dark:text-gray-100' }} inline-flex items-center w-full text-sm transition-colors duration-150 hover:text-teal-600 dark:hover:text-teal-300"
                        href="{{ route('kriteria') }}">
                        <i class="ri-table-line text-lg"></i>
                        <span class="ml-4">Kriteria</span>
                    </a>
                </li>
              
                {{-- Data WP --}}
                <li class="w-full mt-6">
                    <h6 class="pl-6 font-bold leading-tight uppercase text-xs opacity-60">WP</h6>
                </li>
                <li class="relative px-6 pt-3">
                    @if (Request::is('dashboard/alternatif*'))
                        <span class="absolute inset-y-0 left-0 w-1 bg-white rounded-tr-lg rounded-br-lg"
                            aria-hidden="true"></span>
                    @endif
                    <a class="{{ Request::is('dashboard/alternatif*') ? 'font-semibold text-teal-400 dark:text-teal-300' : 'text-white dark:text-gray-100' }} inline-flex items-center w-full text-sm transition-colors duration-150 hover:text-teal-600 dark:hover:text-teal-300"
                        href="{{ route('alternatif') }}">
                        <i class="ri-braces-line text-lg"></i>
                        <span class="ml-4">Alternatif</span>
                    </a>
                </li>
                <li class="relative px-6 pt-3">
                    @if (Request::is('dashboard/hasil_akhir*'))
                        <span class="absolute inset-y-0 left-0 w-1 bg-white rounded-tr-lg rounded-br-lg"
                            aria-hidden="true"></span>
                    @endif
                    <a class="{{ Request::is('dashboard/hasil_akhir*') ? 'font-semibold text-teal-400 dark:text-teal-300' : 'text-white dark:text-gray-100' }} inline-flex items-center w-full text-sm transition-colors duration-150 hover:text-teal-600 dark:hover:text-teal-300"
                        href="{{ route('penilaian.hasil_akhir') }}">
                        <i class="ri-bar-chart-2-line text-lg"></i>
                        <span class="ml-4">Hasil Akhir</span>
                    </a>
                </li>

                <li class="w-full mt-6">
                    <h6 class="pl-6 font-bold leading-tight uppercase text-xs opacity-60">Master Data</h6>
                </li>
                <li class="relative px-6 pt-3">
                    @if (Request::is('dashboard/user*'))
                        <span class="absolute inset-y-0 left-0 w-1 bg-white rounded-tr-lg rounded-br-lg"
                            aria-hidden="true"></span>
                    @endif
                    <a class="{{ Request::is('dashboard/user*') ? 'font-semibold text-teal-400 dark:text-teal-300' : 'text-white dark:text-gray-100' }} inline-flex items-center w-full text-sm transition-colors duration-150 hover:text-teal-600 dark:hover:text-teal-300"
                        href="{{ route('user') }}">
                        <i class="ri-user-3-line text-lg"></i>
                        <span class="ml-4">Pengguna</span>
                    </a>
                </li>
            </ul>
        @else
            <ul>
                <li class="relative px-6 pt-3">
                    @if (Request::is('dashboard/hasil_akhir*'))
                        <span class="absolute inset-y-0 left-0 w-1 bg-white rounded-tr-lg rounded-br-lg"
                            aria-hidden="true"></span>
                    @endif
                    <a class="{{ Request::is('dashboard/hasil_akhir*') ? 'font-semibold text-teal-400 dark:text-teal-300' : 'text-white dark:text-gray-100' }} inline-flex items-center w-full text-sm transition-colors duration-150 hover:text-teal-600 dark:hover:text-teal-300"
                        href="{{ route('penilaian.hasil_akhir') }}">
                        <i class="ri-bar-chart-2-line text-lg"></i>
                        <span class="ml-4">Hasil Akhir</span>
                    </a>
                </li>
            </ul>
        @endif
    </div>
</aside>
