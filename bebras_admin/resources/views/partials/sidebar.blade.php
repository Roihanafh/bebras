<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <div class="app-brand demo d-flex align-items-center">
        <a href="{{ route('admin.dashboard') }}" class="app-brand-link d-flex align-items-center text-decoration-none">
            <span class="app-brand-logo demo">
                <img src="{{ asset('assets/img/logo/logo.jpg') }}" alt="" style="height:40px;">
            </span>
            <span class="app-brand-text demo menu-text fw-bolder ms-2">
                Admin
                <p>
                    Bebras
                </p>

            </span>
        </a>
        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
            <i class="bx bx-chevron-left bx-sm align-middle"></i>
        </a>
    </div>

    <div class="menu-inner-shadow"></div>

    <ul class="menu-inner py-1">
        @if(auth()->user()->hasRole('biro'))
            {{-- Menu khusus Biro --}}
            <li class="menu-item {{ Route::is('berita.*') ? 'active' : '' }}">
                <a href="{{ route('berita.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-news"></i>
                    <div data-i18n="Berita">Berita</div>
                </a>
            </li>
        @else
            {{-- Menu Admin / User --}}
            <!-- Dashboard -->
            <li class="menu-item {{ Route::is('admin.dashboard') ? 'active' : '' }}">
                <a href="{{ route('admin.dashboard') }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-home-circle"></i>
                    <div data-i18n="Analytics">Dashboard</div>
                </a>
            </li>

            <!-- CMS Beranda -->
            <li class="menu-item {{ Route::is('beranda.*') ? 'active' : '' }}">
                <a href="{{ route('beranda.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-desktop"></i>
                    <div data-i18n="CMS Beranda">CMS Beranda</div>
                </a>
            </li>
            <li class="menu-item {{ Route::is('tentang_bebras.index') ? 'active' : '' }}">
                <a href="{{ route('tentang_bebras.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-crown"></i>
                    <div data-i18n="Analytics">Tentang Bebras</div>
                </a>
            </li>
            @php
                $isSoalActive = Route::is('soal_bebras.*') || Route::is('soal_book.*') || Route::is('form-soal-bebras');
            @endphp
            <li class="menu-item {{ $isSoalActive ? 'active open' : '' }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons bx bx-book"></i>
                    <div data-i18n="Soal Bebras">Soal Bebras</div>
                </a>
                <ul class="menu-sub">
                    <li class="menu-item {{ Route::is('soal_bebras.*') || Route::is('form-soal-bebras') ? 'active' : '' }}">
                        <a href="{{ route('soal_bebras.index') }}" class="menu-link">
                            <div data-i18n="Halaman Soal">📄 Halaman Soal</div>
                        </a>
                    </li>
                    <li class="menu-item {{ Route::is('soal_book.*') ? 'active' : '' }}">
                        <a href="{{ route('soal_book.index') }}" class="menu-link">
                            <div data-i18n="Sumber Buku">📚 Sumber Buku</div>
                        </a>
                    </li>
                </ul>
            </li>
            <li class="menu-item {{ Route::is('kontak') ? 'active' : '' }}">
                <a href="{{ route('kontak.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-box"></i>
                    <div data-i18n="Analytics">Kontak</div>
                </a>
            </li>
            <li class="menu-item {{ Route::is('latihan') ? 'active' : '' }}">
                <a href="{{ route('latihan.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-copy"></i>
                    <div data-i18n="Analytics">Latihan</div>
                </a>
            </li>

            <!-- Banner -->
            <li class="menu-item {{ Route::is('banner.*') ? 'active' : '' }}">
                <a href="{{ route('banner.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-image"></i>
                    <div data-i18n="Banner">Banner (Carousel)</div>
                </a>
            </li>

            <!-- Kegiatan -->
            <li class="menu-item {{ Route::is('kegiatan.*') ? 'active' : '' }}">
                <a href="{{ route('kegiatan.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-calendar-event"></i>
                    <div data-i18n="Kegiatan">Kegiatan (Kartu)</div>
                </a>
            </li>

            <!-- Menu Kegiatan -->
            <li class="menu-item {{ Route::is('menu_kegiatan.*') ? 'active' : '' }}">
                <a href="{{ route('menu_kegiatan.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-menu"></i>
                    <div data-i18n="Menu Kegiatan">Menu Kegiatan (Navbar)</div>
                </a>
            </li>

            <!-- Berita Admin -->
            <li class="menu-item {{ Route::is('admin.berita.*') ? 'active' : '' }}">
                <a href="{{ route('admin.berita.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-news"></i>
                    <div data-i18n="Berita">Berita</div>
                </a>
            </li>

            {{-- 
            <!-- Pengaturan Situs (Dihidden sementara) -->
            <li class="menu-item {{ Route::is('setting.*') ? 'active' : '' }}">
                <a href="{{ route('setting.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-cog"></i>
                    <div data-i18n="Pengaturan">Pengaturan Situs</div>
                </a>
            </li>
            --}}

            <!-- Pages -->
            <li class="menu-header small text-uppercase">
                <span class="menu-header-text">Pages</span>
            </li>

            <li class="menu-item {{ Route::is('register') || Request::is('account*') ? 'active' : '' }}">
                <a href="{{ route('register') }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-user"></i>
                    <div data-i18n="Manajemen Akun">Manajemen Akun</div>
                </a>
            </li>
        @endif
    </ul>
</aside>
