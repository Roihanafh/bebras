<div class="md:hidden hidden bg-bebrasDarkBlue shadow-lg rounded-md" id="mobile-menu">
    <div class="px-2 pt-2 pb-3 space-y-1 sm:px-3">
        <a href="{{ route('home') }}"
            class="text-white block px-3 py-2 rounded-md text-base font-medium nav-link">Home</a>

        <!-- Dropdown Utama: Tentang Bebras -->
        <div class="relative dropdown">
            <button
                class="dropdown-btn nav-link text-white px-3 py-2 rounded-md text-sm font-medium flex items-center hover:bg-bebrasDarkBlue transition"
                aria-expanded="false">
                Tentang Bebras
                <svg class="w-4 h-4 ml-1 transform transition duration-200 group-hover:rotate-180" fill="none"
                    stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                </svg>
            </button>

            <div class="dropdown-menu absolute left-0 hidden bg-white text-black mt-2 rounded-md shadow-lg w-48 z-50">
                <ul class="py-2 text-sm">
                    @foreach (\App\Models\TentangBebras::orderBy('urutan', 'asc')->get() as $tbPage)
                    <li>
                        @php
                            $isHardcoded = in_array($tbPage->slug, ['dd_1', 'dd_2', 'dd_3', 'dd_4', 'dd_5', 'dd_6', 'dd_7']);
                            $routeUrl = $isHardcoded ? route('tentangBebras.' . $tbPage->slug) : route('tentangBebras.show', $tbPage->slug);
                            $isActive = request()->is('tentangBebras/' . $tbPage->slug);
                        @endphp
                        <a href="{{ $routeUrl }}"
                            class="block px-4 py-2 text-gray-800 hover:bg-gray-100 {{ $isActive ? 'active' : '' }}">
                            {{ $tbPage->judul }}
                        </a>
                    </li>
                    @endforeach
                </ul>
            </div>
        </div>

        <!-- Dropdown Utama: Soal (dengan nested) -->
        <div class="relative dropdown">
            <button
                class="dropdown-btn nav-link text-white px-3 py-2 rounded-md text-sm font-medium flex items-center hover:bg-bebrasDarkBlue transition"
                aria-expanded="false">
                Soal
                <svg class="w-4 h-4 ml-1 transform transition duration-200 group-hover:rotate-180" fill="none"
                    stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                </svg>
            </button>

            <div class="dropdown-menu absolute left-0 hidden bg-white text-black mt-2 rounded-md shadow-lg w-56 z-50">
                <ul class="py-2 text-sm">
                    @foreach($menuSoals->whereNull('parent_id') as $menuItem)
                        @php
                            $children = $menuSoals->where('parent_id', $menuItem->id);
                        @endphp
                        @if($children->isNotEmpty())
                            <!-- Nested: cukup jadikan LI ini juga .dropdown -->
                            <li class="relative dropdown">
                                <button
                                    class="dropdown-btn w-full flex items-center justify-between px-4 py-2 hover:bg-gray-100"
                                    aria-expanded="false">
                                    {{ $menuItem->nama_menu }}
                                    <svg class="w-4 h-4 ml-1 transform transition duration-200 group-hover:rotate-180"
                                        fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>

                                <ul
                                    class="dropdown-menu absolute top-0 left-[98%] ml-0 hidden bg-white shadow-lg rounded-md w-48 z-50">
                                    @foreach($children as $child)
                                        @php
                                            $routeName = 'soal.show';
                                            if ($child->slug === 'siaga-sd') $routeName = 'soal.siaga-sd';
                                            elseif ($child->slug === 'penggalang-smp') $routeName = 'soal.penggalang-smp';
                                            elseif ($child->slug === 'penegak-sma') $routeName = 'soal.penegak-sma';
                                            elseif ($child->slug === 'index-soal') $routeName = 'soal.index-soal';
                                            elseif ($child->slug === 'pembahasan-soal') $routeName = 'soal.pembahasan-soal';
                                        @endphp
                                        <li>
                                            <a href="{{ $routeName === 'soal.show' ? route('soal.show', $child->slug) : route($routeName) }}"
                                               class="block px-4 py-2 hover:bg-gray-100">
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
                            @endphp
                            <li>
                                <a href="{{ $routeName === 'soal.show' ? route('soal.show', $menuItem->slug) : route($routeName) }}"
                                   class="block px-4 py-2 hover:bg-gray-100">
                                    {{ $menuItem->nama_menu }}
                                </a>
                            </li>
                        @endif
                    @endforeach
                </ul>
            </div>





        </div>

        <!-- Dropdown Utama: Kegiatan (dengan nested) -->
        <div class="relative dropdown">
            <button
                class="dropdown-btn nav-link text-white px-3 py-2 rounded-md text-sm font-medium flex items-center hover:bg-bebrasDarkBlue transition"
                aria-expanded="false">
                Kegiatan
                <svg class="w-4 h-4 ml-1 transform transition duration-200 group-hover:rotate-180" fill="none"
                    stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                </svg>
            </button>

            <div class="dropdown-menu absolute left-0 hidden bg-white text-black mt-2 rounded-md shadow-lg w-56 z-50">
                <ul class="py-2 text-sm">
                    @foreach($menuKegiatans as $menuItem)
                        @php $children = $menuItem->children; @endphp
                        @if($children->isNotEmpty())
                            <!-- Nested -->
                            <li class="relative dropdown">
                                <button
                                    class="dropdown-btn w-full flex items-center justify-between px-4 py-2 hover:bg-gray-100"
                                    aria-expanded="false">
                                    {{ $menuItem->nama_menu }}
                                    <svg class="w-4 h-4 ml-1 transform transition duration-200 group-hover:rotate-180"
                                        fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>

                                <ul class="dropdown-menu absolute top-0 left-[98%] ml-0 hidden bg-white shadow-lg rounded-md w-48 z-50">
                                    @foreach($children as $child)
                                        <li>
                                            @if($child->url)
                                                <a href="{{ $child->url }}" target="_blank"
                                                   class="block px-4 py-2 hover:bg-gray-100">
                                                    {{ $child->nama_menu }}
                                                </a>
                                            @else
                                                <a href="{{ route('kegiatan.show', $child->slug) }}"
                                                   class="block px-4 py-2 hover:bg-gray-100">
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
                                       class="block px-4 py-2 hover:bg-gray-100">
                                        {{ $menuItem->nama_menu }}
                                    </a>
                                @else
                                    <a href="{{ route('kegiatan.show', $menuItem->slug) }}"
                                       class="block px-4 py-2 hover:bg-gray-100">
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
            class="text-white block px-3 py-2 rounded-md text-base font-medium nav-link">Latihan</a>
        <a href="{{ route('kontak') }}"
            class="text-white block px-3 py-2 rounded-md text-base font-medium nav-link">Kontak</a>
        <div class="relative mt-4 px-3 pb-3">
            <form action="{{ route('search') }}" method="GET" class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none z-10">
                    <i class="fas fa-search text-gray-400 ms-2"></i>
                </div>
                <input type="text" id="searchInputMobile" name="search"
                    class="block w-full pl-10 pr-3 py-2 border border-transparent rounded-lg leading-5 bg-white text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-bebrasBlue text-sm"
                    autocomplete="off" placeholder="Cari...">
            </form>
        </div>

    </div>
