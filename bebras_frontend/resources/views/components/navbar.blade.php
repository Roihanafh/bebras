<nav class="fixed top-0 left-0 w-full z-50 bg-bebrasBlue shadow-md rounded-md">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
            {{-- Logo --}}
            <div class="flex-shrink-0 flex items-center">
                <a href="{{ route('home') }}" class="flex items-center" title="Beranda">
                    <img src="{{ asset('img/bebras.png') }}" alt="Logo Bebras" class="h-10 hover:opacity-85 transition-opacity duration-200">
                    {{-- <span class="ml-2 text-white font-semibold text-xl hidden sm:block">Bebras Indonesia</span> --}}
                </a>
            </div>

            {{-- Desktop Menu --}}
            <div class="hidden md:block">
                <div class="ml-10 flex items-baseline space-x-6">
                    <a href="{{ route('home') }}"
                        class="nav-link text-white px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('home') ? 'active' : '' }}">
                        Home
                    </a>

                    <div class="relative inline-block dropdown">
                        <!-- Parent -->
                        <button
                            class="dropdown-btn nav-link text-white px-3 py-2 rounded-md text-sm font-medium flex items-center cursor-pointer hover:bg-bebrasDarkBlue transition">
                            Tentang Bebras
                            <svg class="w-4 h-4 ml-1 transform transition duration-200" fill="none"
                                stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <!-- Dropdown -->
                        <div
                            class="dropdown-menu absolute left-0 hidden bg-white text-black mt-2 rounded-md shadow-lg w-48 z-50">
                            <ul class="py-2 text-sm">
                                @foreach (\App\Models\TentangBebras::orderBy('urutan', 'asc')->get() as $tbPage)
                                <li>
                                    @php
                                        $isHardcoded = in_array($tbPage->slug, ['dd_1', 'dd_2', 'dd_3', 'dd_4', 'dd_5', 'dd_6', 'dd_7']);
                                        $routeUrl = $isHardcoded ? route('tentangBebras.' . $tbPage->slug) : route('tentangBebras.show', $tbPage->slug);
                                        $isActive = request()->is('tentangBebras/' . $tbPage->slug);
                                    @endphp
                                    <a href="{{ $routeUrl }}"
                                        class="nav-link block px-4 py-2 text-gray-800 hover:bg-gray-100 {{ $isActive ? 'active' : '' }}">
                                        {{ $tbPage->judul }}
                                    </a>
                                </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>

                    <div class="relative inline-block dropdown">
                        <!-- Parent -->
                        <button
                            class="dropdown-btn nav-link text-white px-3 py-2 rounded-md text-sm font-medium flex items-center cursor-pointer hover:bg-bebrasDarkBlue transition">
                            Soal
                            <svg class="w-4 h-4 ml-1 transform transition duration-200" fill="none"
                                stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <!-- Dropdown -->
                        <div
                            class="dropdown-menu absolute left-0 hidden bg-white text-black mt-2 rounded-md shadow-lg w-48 z-50">
                            <ul class="py-2 text-sm">
                                @foreach($menuSoals->whereNull('parent_id') as $menuItem)
                                    @php
                                        $children = $menuSoals->where('parent_id', $menuItem->id);
                                    @endphp
                                    @if($children->isNotEmpty())
                                        <li class="relative group">
                                            <!-- Parent Dropdown Header -->
                                            <div
                                                class="nav-link px-4 py-2 text-gray-800 hover:bg-gray-100 flex items-center cursor-pointer">
                                                {{ $menuItem->nama_menu }}
                                                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" stroke-width="2"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                                </svg>
                                            </div>

                                            <!-- Sub-Dropdown -->
                                            <ul
                                                class="absolute left-[98%] top-0 hidden group-hover:block bg-white shadow-lg rounded-md w-48 ms-0">
                                                @foreach($children as $child)
                                                    @php
                                                        $routeName = 'soal.show';
                                                        if ($child->slug === 'siaga-sd') $routeName = 'soal.siaga-sd';
                                                        elseif ($child->slug === 'penggalang-smp') $routeName = 'soal.penggalang-smp';
                                                        elseif ($child->slug === 'penegak-sma') $routeName = 'soal.penegak-sma';
                                                        elseif ($child->slug === 'index-soal') $routeName = 'soal.index-soal';
                                                        elseif ($child->slug === 'pembahasan-soal') $routeName = 'soal.pembahasan-soal';
                                                        
                                                        $isChildActive = request()->routeIs('soal.' . $child->slug) || (request()->routeIs('soal.show') && request()->route('slug') === $child->slug);
                                                    @endphp
                                                    <li>
                                                        <a href="{{ $routeName === 'soal.show' ? route('soal.show', $child->slug) : route($routeName) }}"
                                                            class="block px-4 py-2 text-gray-700 hover:bg-gray-100 {{ $isChildActive ? 'active' : '' }}">
                                                            {{ $child->nama_menu }}
                                                        </a>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </li>
                                    @else
                                        @php
                                            $routeName = 'soal.show';
                                            if ($menuItem->slug === 'siaga-sd') $routeName = 'soal.siaga-sd';
                                            elseif ($menuItem->slug === 'penggalang-smp') $routeName = 'soal.penggalang-smp';
                                            elseif ($menuItem->slug === 'penegak-sma') $routeName = 'soal.penegak-sma';
                                            elseif ($menuItem->slug === 'index-soal') $routeName = 'soal.index-soal';
                                            elseif ($menuItem->slug === 'pembahasan-soal') $routeName = 'soal.pembahasan-soal';
                                            
                                            $isItemActive = request()->routeIs('soal.' . $menuItem->slug) || (request()->routeIs('soal.show') && request()->route('slug') === $menuItem->slug);
                                        @endphp
                                        <li>
                                            <a href="{{ $routeName === 'soal.show' ? route('soal.show', $menuItem->slug) : route($routeName) }}"
                                                class="nav-link block px-4 py-2 text-gray-800 hover:bg-gray-100 {{ $isItemActive ? 'active' : '' }}">
                                                {{ $menuItem->nama_menu }}
                                            </a>
                                        </li>
                                    @endif
                                @endforeach
                            </ul>
                        </div>
                    </div>

                    <div class="relative inline-block dropdown ">
                        <!-- Parent -->
                        <button
                            class="dropdown-btn nav-link text-white px-3 py-2 rounded-md text-sm font-medium flex items-center cursor-pointer hover:bg-bebrasDarkBlue transition">
                            Kegiatan
                            <svg class="w-4 h-4 ml-1 transform transition duration-200" fill="none"
                                stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <!-- Dropdown -->
                        <div
                            class="dropdown-menu absolute left-0 hidden bg-white text-black mt-2 rounded-md shadow-lg w-60 z-50">
                            <ul class="py-2 text-sm">
                                @foreach($menuKegiatans as $menuItem)
                                    @php
                                        $children = $menuItem->children;
                                        $isActive = request()->is('kegiatan/' . $menuItem->slug)
                                            || ($children->isNotEmpty() && $children->contains(fn($c) => request()->is('kegiatan/' . $c->slug)));
                                    @endphp

                                    @if($children->isNotEmpty())
                                        <li class="relative group">
                                            <!-- Has children: show arrow -->
                                            <div class="nav-link px-4 py-2 text-gray-800 hover:bg-gray-100 flex items-center cursor-pointer {{ $isActive ? 'active' : '' }}">
                                                {{ $menuItem->nama_menu }}
                                                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" stroke-width="2"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                                                </svg>
                                            </div>

                                            <!-- Sub-Dropdown -->
                                            <ul class="absolute left-[98%] top-0 hidden group-hover:block bg-white shadow-lg rounded-md w-48 ms-0">
                                                @foreach($children as $child)
                                                    @php
                                                        $isChildActive = request()->is('kegiatan/' . $child->slug);
                                                    @endphp
                                                    <li>
                                                        @if($child->url)
                                                            <a href="{{ $child->url }}" target="_blank"
                                                               class="block px-4 py-2 text-gray-700 hover:bg-gray-100">
                                                                {{ $child->nama_menu }}
                                                            </a>
                                                        @else
                                                            <a href="{{ route('kegiatan.show', $child->slug) }}"
                                                               class="block px-4 py-2 text-gray-700 hover:bg-gray-100 {{ $isChildActive ? 'active' : '' }}">
                                                                {{ $child->nama_menu }}
                                                            </a>
                                                        @endif
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </li>
                                    @else
                                        <li>
                                            @if($menuItem->url)
                                                <a href="{{ $menuItem->url }}" target="_blank"
                                                   class="nav-link block px-4 py-2 text-gray-800 hover:bg-gray-100">
                                                    {{ $menuItem->nama_menu }}
                                                </a>
                                            @else
                                                <a href="{{ route('kegiatan.show', $menuItem->slug) }}"
                                                   class="nav-link block px-4 py-2 text-gray-800 hover:bg-gray-100 {{ $isActive ? 'active' : '' }}">
                                                    {{ $menuItem->nama_menu }}
                                                </a>
                                            @endif
                                        </li>
                                    @endif
                                @endforeach
                            </ul>
                        </div>
                    </div>



                    <a href="{{ route('latihan') }}"
                        class="nav-link text-white px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('latihan') ? 'active' : '' }}">
                        Latihan
                    </a>
                    <a href="{{ route('kontak') }}"
                        class="nav-link text-white px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('kontak') ? 'active' : '' }}">
                        Kontak
                    </a>
                </div>
            </div>

            {{-- Search + Mobile --}}
            <div class="flex items-center">
                <div class="hidden md:block relative mr-4">
                    <form action="{{ route('search') }}" method="GET" class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none z-10">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                        <input type="text" id="searchInput" name="search"
                            class="block w-52 md:w-60 pl-10 pr-3 py-2 border border-transparent rounded-lg leading-5 bg-white text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-bebrasDarkBlue shadow-sm transition-all duration-200 text-sm"
                            autocomplete="off" placeholder="Cari...">
                    </form>
                </div>

                <div class="md:hidden">
                    <button type="button"
                        class="inline-flex items-center justify-center p-2 rounded-md text-white hover:bg-bebrasDarkBlue focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white"
                        id="mobile-menu-button">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
    {{-- Mobile Menu --}}
    @include('components.navbar-mobile')

</nav>

