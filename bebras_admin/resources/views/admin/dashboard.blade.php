@extends('app')

@section('content')
<!-- Welcome & Status Row -->
<div class="col-lg-8 mb-4">
    <div class="card h-100">
        <div class="d-flex align-items-end row">
            <div class="col-sm-7">
                <div class="card-body">
                    <h5 class="card-title text-primary">Selamat Datang, {{ Auth::user()->name }}! 🎉</h5>
                    <p class="mb-4">
                        Selamat datang di Panel CMS Bebras Indonesia. Gunakan kartu status di bawah dan navigasi menu untuk mengelola dan mempublikasikan konten pada website landing page utama secara dinamis.
                    </p>
                    <a href="{{ route('soal_bebras.index') }}" class="btn btn-sm btn-outline-primary">Kelola Soal Tantangan</a>
                </div>
            </div>
            <div class="col-sm-5 text-center text-sm-left">
                <div class="card-body pb-0 px-0 px-md-4">
                    <img src="{{ asset('assets/img/illustrations/man-with-laptop-light.png') }}" height="140" alt="View Badge User" data-app-dark-img="illustrations/man-with-laptop-dark.png" data-app-light-img="illustrations/man-with-laptop-light.png">
                </div>
            </div>
        </div>
    </div>
</div>

<div class="col-lg-4 mb-4">
    <div class="card h-100">
        <div class="card-body d-flex flex-column justify-content-between">
            <div>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="card-title mb-0">Sinkronisasi Database</h5>
                    <span class="badge bg-label-success">Terhubung</span>
                </div>
                <p class="text-muted mb-3 small">
                    CMS Admin & Frontend beroperasi pada database bersama (<code class="text-primary fw-bold">kodaposc_bebras_db</code>). Perubahan data di panel ini langsung tercermin di web publik tanpa sinkronisasi HTTP tambahan.
                </p>
            </div>
            @if(Route::has('setting.index'))
            <div>
                <a href="{{ route('setting.index') }}" class="btn btn-sm btn-outline-secondary w-100"><i class="bx bx-cog me-1"></i>Pengaturan Situs</a>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- CMS Content Status Grid -->
<div class="col-12 mb-4">
    <div class="card bg-transparent shadow-none border-0 mb-0">
        <h5 class="pb-1 mb-3"><i class="bx bx-grid-alt me-2 text-primary"></i>Kondisi Konten Portal Saat Ini</h5>
        <div class="row">
            <!-- Banner -->
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="card h-100 border-start border-danger border-3">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-2 pb-1">
                            <div class="avatar me-3">
                                <span class="avatar-initial rounded bg-label-danger"><i class="bx bx-image"></i></span>
                            </div>
                            <h4 class="ms-1 mb-0">{{ $stats['total_banner'] }}</h4>
                        </div>
                        <h6 class="mb-1">Banner Promosi</h6>
                        <small class="text-muted">Slide gambar aktif di beranda utama</small>
                    </div>
                </div>
            </div>
            <!-- Halaman Dinamis -->
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="card h-100 border-start border-primary border-3">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-2 pb-1">
                            <div class="avatar me-3">
                                <span class="avatar-initial rounded bg-label-primary"><i class="bx bx-crown"></i></span>
                            </div>
                            <h4 class="ms-1 mb-0">{{ $stats['total_tentang'] }}</h4>
                        </div>
                        <h6 class="mb-1">Halaman Tentang Bebras</h6>
                        <small class="text-muted">Sub-halaman aktif di menu navbar</small>
                    </div>
                </div>
            </div>
            <!-- Kegiatan -->
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="card h-100 border-start border-info border-3">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-2 pb-1">
                            <div class="avatar me-3">
                                <span class="avatar-initial rounded bg-label-info"><i class="bx bx-calendar-event"></i></span>
                            </div>
                            <h4 class="ms-1 mb-0">{{ $stats['total_kegiatan'] }}</h4>
                        </div>
                        <h6 class="mb-1">Kegiatan Bebras</h6>
                        <small class="text-muted">Kartu kegiatan yang dipublikasikan</small>
                    </div>
                </div>
            </div>
            <!-- Latihan -->
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="card h-100 border-start border-secondary border-3">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-2 pb-1">
                            <div class="avatar me-3">
                                <span class="avatar-initial rounded bg-label-secondary"><i class="bx bx-link-external"></i></span>
                            </div>
                            <h4 class="ms-1 mb-0">{{ $stats['total_latihan'] }}</h4>
                        </div>
                        <h6 class="mb-1">Tautan Latihan</h6>
                        <small class="text-muted">Link portal kompetisi luar/eksternal</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <!-- Soal Challenge -->
            <div class="col-lg-3 col-md-6 mb-4 mb-lg-0">
                <div class="card h-100 border-start border-success border-3">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-2 pb-1">
                            <div class="avatar me-3">
                                <span class="avatar-initial rounded bg-label-success"><i class="bx bx-book-open"></i></span>
                            </div>
                            <h4 class="ms-1 mb-0">{{ $stats['total_soal'] }}</h4>
                        </div>
                        <h6 class="mb-1">Soal Tantangan</h6>
                        <small class="text-muted">Kumpulan soal interaktif aktif</small>
                    </div>
                </div>
            </div>
            <!-- Buku Pembahasan -->
            <div class="col-lg-3 col-md-6 mb-4 mb-lg-0">
                <div class="card h-100 border-start border-warning border-3">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-2 pb-1">
                            <div class="avatar me-3">
                                <span class="avatar-initial rounded bg-label-warning"><i class="bx bx-book"></i></span>
                            </div>
                            <h4 class="ms-1 mb-0">{{ $stats['total_buku'] }}</h4>
                        </div>
                        <h6 class="mb-1">Buku Soal PDF</h6>
                        <small class="text-muted">Buku pembahasan yang dapat diunduh</small>
                    </div>
                </div>
            </div>
            <!-- Biro / Kontak -->
            <div class="col-lg-3 col-md-6 mb-4 mb-md-0">
                <div class="card h-100 border-start border-info border-3">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-2 pb-1">
                            <div class="avatar me-3">
                                <span class="avatar-initial rounded bg-label-info"><i class="bx bx-map-pin"></i></span>
                            </div>
                            <h4 class="ms-1 mb-0">{{ $stats['total_kontak'] }}</h4>
                        </div>
                        <h6 class="mb-1">Biro Bebras (Kontak)</h6>
                        <small class="text-muted">Titik kontak universitas terdaftar</small>
                    </div>
                </div>
            </div>
            <!-- Admin -->
            <div class="col-lg-3 col-md-6">
                <div class="card h-100 border-start border-primary border-3">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-2 pb-1">
                            <div class="avatar me-3">
                                <span class="avatar-initial rounded bg-label-primary"><i class="bx bx-user"></i></span>
                            </div>
                            <h4 class="ms-1 mb-0">{{ $stats['total_admin'] }}</h4>
                        </div>
                        <h6 class="mb-1">Administrator</h6>
                        <small class="text-muted">Pengguna dengan hak akses CMS</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="col-md-6 mb-4">
    <div class="card h-100">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Distribusi Soal per Tingkat</h5>
            <small class="text-muted">SD, SMP, SMA</small>
        </div>
        <div class="card-body">
            <div id="soalTingkatChart" class="mx-auto"></div>
        </div>
    </div>
</div>

<div class="col-md-6 mb-4">
    <div class="card h-100">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Tingkat Kesulitan Soal</h5>
            <small class="text-muted">Mudah, Menengah, Sulit</small>
        </div>
        <div class="card-body">
            <div id="soalKesulitanChart" class="mx-auto"></div>
        </div>
    </div>
</div>

<!-- Kontak Biro Section -->
<div class="col-12">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center border-bottom pb-3">
            <h5 class="mb-0"><i class="bx bx-map-pin me-2 text-primary"></i>Daftar Kontak Biro / Universitas (Landing Page)</h5>
            <a href="{{ route('kontak.index') }}" class="btn btn-sm btn-outline-primary">Kelola Kontak</a>
        </div>
        <div class="card-body pt-3 pb-0">
            <p class="text-muted small mb-0">
                Berikut adalah daftar kontak biro lokal resmi Bebras Indonesia di berbagai universitas. Informasi ini ditampilkan pada menu <strong>Kontak</strong> di landing page publik agar dapat dihubungi oleh sekolah atau guru setempat.
            </p>
        </div>
        <div class="table-responsive text-nowrap mt-3">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Nama Perwakilan / Biro</th>
                        <th>Institusi / Universitas</th>
                        <th>Alamat Kantor</th>
                        <th>Metode Kontak</th>
                        <th>Terakhir Diperbarui</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse($latestKontak as $kontak)
                    <tr>
                        <td><strong>{{ $kontak->nama }}</strong></td>
                        <td>{{ $kontak->institusi ?: '-' }}</td>
                        <td>{{ $kontak->alamat ? \Illuminate\Support\Str::limit($kontak->alamat, 45) : '-' }}</td>
                        <td>
                            @foreach($kontak->details as $detail)
                                @if($detail->tipe == 'email')
                                    <span class="badge bg-label-primary me-1" title="Email"><i class="bx bx-envelope me-1"></i>{{ $detail->nilai }}</span>
                                @elseif($detail->tipe == 'telepon')
                                    <span class="badge bg-label-success me-1" title="Telepon"><i class="bx bx-phone me-1"></i>{{ $detail->nilai }}</span>
                                @else
                                    <span class="badge bg-label-secondary me-1" title="{{ ucfirst($detail->tipe) }}">{{ $detail->nilai }}</span>
                                @endif
                            @endforeach
                            @if($kontak->details->isEmpty())
                                -
                            @endif
                        </td>
                        <td>{{ $kontak->updated_at ? $kontak->updated_at->diffForHumans() : '-' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">Belum ada kontak biro terdaftar.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
document.addEventListener("DOMContentLoaded", function() {
    // 1. Chart Distribusi Soal per Tingkat (Donut Chart)
    var tingkatOptions = {
        series: [
            {{ $soalPerTingkat['SD'] }},
            {{ $soalPerTingkat['SMP'] }},
            {{ $soalPerTingkat['SMA'] }}
        ],
        labels: ['SD', 'SMP', 'SMA'],
        chart: {
            type: 'donut',
            height: 300
        },
        colors: ['#696cff', '#03c3ec', '#ff3e1d'], // Sneat palette colors
        stroke: {
            width: 5,
            colors: ['#fff']
        },
        legend: {
            show: true,
            position: 'bottom',
            horizontalAlign: 'center',
            labels: {
                colors: '#566a7f',
                useSeriesColors: false
            }
        },
        dataLabels: {
            enabled: true,
            formatter: function (val, opts) {
                return opts.w.config.series[opts.seriesIndex];
            }
        },
        plotOptions: {
            pie: {
                donut: {
                    size: '70%',
                    labels: {
                        show: true,
                        value: {
                            show: true,
                            fontSize: '18px',
                            fontWeight: '600',
                            color: '#566a7f',
                            offsetY: -5,
                            formatter: function (val) {
                                return val;
                            }
                        },
                        total: {
                            show: true,
                            label: 'Total Soal',
                            fontSize: '13px',
                            color: '#a1acb8',
                            formatter: function (w) {
                                return w.globals.seriesTotals.reduce(function (a, b) {
                                    return a + b;
                                }, 0);
                            }
                        }
                    }
                }
            }
        }
    };
    var tingkatChart = new ApexCharts(document.querySelector("#soalTingkatChart"), tingkatOptions);
    tingkatChart.render();

    // 2. Chart Tingkat Kesulitan Soal (Bar/Column Chart)
    var kesulitanOptions = {
        series: [{
            name: 'Jumlah Soal',
            data: [
                {{ $soalPerKesulitan['Mudah'] }},
                {{ $soalPerKesulitan['Menengah'] }},
                {{ $soalPerKesulitan['Sulit'] }}
            ]
        }],
        chart: {
            type: 'bar',
            height: 300,
            toolbar: {
                show: false
            }
        },
        plotOptions: {
            bar: {
                borderRadius: 5,
                horizontal: false,
                columnWidth: '45%',
                distributed: true
            }
        },
        colors: ['#71dd37', '#ffab00', '#ff3e1d'], // Green, Orange, Red
        dataLabels: {
            enabled: true,
            style: {
                fontSize: '12px',
                colors: ['#fff']
            }
        },
        legend: {
            show: false
        },
        xaxis: {
            categories: ['Mudah', 'Menengah', 'Sulit'],
            labels: {
                style: {
                    colors: '#566a7f',
                    fontSize: '12px'
                }
            },
            axisBorder: {
                show: false
            },
            axisTicks: {
                show: false
            }
        },
        yaxis: {
            labels: {
                style: {
                    colors: '#566a7f',
                    fontSize: '12px'
                }
            }
        },
        grid: {
            borderColor: '#eceef1',
            strokeDashArray: 5,
            xaxis: {
                lines: {
                    show: false
                }
            }
        }
    };
    var kesulitanChart = new ApexCharts(document.querySelector("#soalKesulitanChart"), kesulitanOptions);
    kesulitanChart.render();
});
</script>
@endpush