<x-layout bodyClass="g-sidenav-show  bg-gray-200">
    <x-navbars.sidebar activePage='absensi'></x-navbars.sidebar>
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">
        <!-- Navbar -->
        <x-navbars.navs.auth titlePage="Absensi"></x-navbars.navs.auth>
        <!-- End Navbar -->

        <div class="container-fluid">
            @if (session('sukses'))
                <div class="row">
                    <div class="alert alert-success text-white" role="alert" id="pesan_sukses">
                        <strong>Berhasil!</strong> {{ session('sukses') }}
                    </div>
                </div>
            @endif
            @if (Auth()->user()->role == 'admin')
                <div class="row">
                    <div class="col-12 p-0">
                        <a href="{{ route('pengaturanabsensi.index') }}" class="btn btn-primary w-100">Pengaturan
                            absensi</a>
                    </div>
                </div>
            @endif

            @if (Auth()->user()->role == 'pegawai')
                <div class="row">
                    @if ($hasil_cek_ip)
                        {{-- <a href="{{ route('absensi.create') }}" class="btn btn-primary mb-4 w-100"
                            data-bs-toggle="modal" data-bs-target="#modal_absensi">Absensi</a> --}}

                        <button type="button" class="btn btn-primary mb-4 w-100" data-bs-toggle="modal"
                            data-bs-target="#absensiModal">
                            Absensi
                        </button>
                    @else
                        <p class="m-0"><small>Absensi tidak tersedia! anda berada di luar jangkauan
                                jaringan!</small>
                        </p>
                    @endif
                </div>
            @endif


            <div class="row">
                <div class="modal fade" id="absensiModal" tabindex="-1" aria-labelledby="absensiModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form id="absensiForm" method="POST" action="{{ route('absensi.store') }}">
                                @csrf
                                <div class="modal-header d-flex justify-content-center">
                                    <h5 class="modal-title" id="absensiModalLabel">Absensi</h5>
                                </div>
                                <div class="modal-body">
                                    <!-- Navigasi Tabs -->
                                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link active" id="masuk-tab" data-bs-toggle="tab"
                                                data-bs-target="#masuk" type="button" role="tab"
                                                aria-controls="masuk" aria-selected="true">Absensi Masuk</button>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link" id="pulang-tab" data-bs-toggle="tab"
                                                data-bs-target="#pulang" type="button" role="tab"
                                                aria-controls="pulang" aria-selected="false">Absensi Pulang</button>
                                        </li>
                                    </ul>
                                    <div class="tab-content" id="myTabContent">
                                        <!-- Tab Absensi Masuk -->
                                        <div class="tab-pane fade show active" id="masuk" role="tabpanel"
                                            aria-labelledby="masuk-tab">
                                            <p>Form absensi kedatangan</p>
                                            <div class="form-check">
                                                @if ($double_absensi_datang)
                                                    <input class="form-check-input absensi-radio" type="radio"
                                                        name="status_absensi" id="absensi_datang" value="datang"
                                                        disabled>
                                                    <label class="form-check-label text-disabled"
                                                        for="absensi_datang">Datang <span class="text-danger">(Anda
                                                            sudah
                                                            melakukan absensi kedatangan hari ini!)</span></label>
                                                @elseif(!$double_absensi_datang)
                                                    <input class="form-check-input absensi-radio" type="radio"
                                                        name="status_absensi" id="absensi_datang" value="datang">
                                                    <label class="form-check-label" for="absensi_datang">Datang</label>

                                                    <input class="form-check-input absensi-radio" type="radio"
                                                        name="status_absensi" id="izin" value="izin">
                                                    <label class="form-check-label" for="izin">Izin</label>
                                                @endif


                                            </div>
                                        </div>
                                        <!-- Tab Absensi Pulang -->
                                        <div class="tab-pane fade" id="pulang" role="tabpanel"
                                            aria-labelledby="pulang-tab">
                                            <p>Anda hanya dapat melakukan absensi pulang jika telah melakukan absensi
                                                kedatangan</p>
                                            <div class="form-check">
                                                <!-- cek apakah sudah lakukan absensi datang -->
                                                @if ($validasi_absensi_pulang)
                                                    @if (!$double_absensi_pulang)
                                                        <input class="form-check-input absensi-radio" type="radio"
                                                            name="status_absensi" id="absensi_pulang" value="pulang">
                                                        <label class="form-check-label"
                                                            for="absensi_pulang">Pulang</label>
                                                    @else
                                                        <input class="form-check-input absensi-radio" type="radio"
                                                            name="status_absensi" id="absensi_pulang" value="pulang"
                                                            disabled>
                                                        <label class="form-check-label text-disabled"
                                                            for="absensi_pulang">Pulang <span
                                                                class="text-danger">(Anda
                                                                sudah melakukan absensi
                                                                pulang hari ini!)</span></label>
                                                    @endif
                                                @elseif($izin)
                                                    <input class="form-check-input absensi-radio" type="radio"
                                                        name="status_absensi" id="absensi_pulang" value="pulang"
                                                        disabled>
                                                    <label class="form-check-label text-disabled"
                                                        for="absensi_pulang">Pulang <span class="text-danger">(Anda
                                                            melakukan izin hari ini!)</span></label>
                                                @else
                                                    <input class="form-check-input absensi-radio" type="radio"
                                                        name="status_absensi" id="absensi_pulang" value="pulang"
                                                        disabled>
                                                    <label class="form-check-label text-disabled"
                                                        for="absensi_pulang">Pulang <span class="text-danger">(Isi
                                                            terlebih dahulu absensi datang!)</span></label>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary"
                                        data-bs-dismiss="modal">Tutup</button>
                                    <button type="submit" class="btn btn-success">Simpan</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>


            </div>
            <div class="row">
                <div class="col-12">
                    <!-- Baris Filter -->
                    <div class="d-flex justify-content-between align-items-center">
                        <!-- Bagian Kiri: Filter Hari Ini, Tahun, Bulan -->
                        <div class="d-flex gap-2 align-items-center">
                            <!-- Filter Hari Ini -->
                            <a href="{{ route('absensi.index', ['filter' => 'today']) }}" class="btn btn-primary"
                                style="margin-top: 15px;">
                                Hari Ini
                            </a>
                            <!-- Filter Tahun -->
                            <select class="form-select w-auto" onchange="location = this.value;">
                                <option value="">Pilih Tahun</option>
                                @foreach ($availableYears as $year)
                                    <option value="{{ route('absensi.index', ['year' => $year]) }}"
                                        {{ request('year') == $year ? 'selected' : '' }}>
                                        {{ $year }}
                                    </option>
                                @endforeach
                            </select>
                            <!-- Filter Bulan -->
                            <form id="filterForm" method="GET" action="{{ route('absensi.index') }}"
                                class="d-inline-block">
                                <select name="month" class="form-select w-auto"
                                    onchange="document.getElementById('filterForm').submit();">
                                    <option value="" {{ request('month') ? '' : 'selected' }}>Pilih Bulan
                                    </option>
                                    @foreach ($availableMonths as $month)
                                        <option value="{{ explode('-', $month['key'])[1] }}"
                                            {{ request('month') == explode('-', $month['key'])[1] ? 'selected' : '' }}>
                                            {{ $month['label'] }}
                                        </option>
                                    @endforeach
                                </select>
                                <!-- Input hidden untuk year -->
                                <input type="hidden" name="year" value="{{ request('year') ?? now()->year }}">
                            </form>
                        </div>
                        <!-- Bagian Kanan: Tombol Unduh PDF -->
                        <form method="GET" action="{{ route('absensi.download-pdf') }}" class="d-inline-block"
                            style="margin-top: 15px;">
                            <input type="hidden" name="month" value="{{ request('month') }}">
                            <input type="hidden" name="year" value="{{ request('year') }}">
                            <button type="submit" class="btn btn-success">Unduh PDF</button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="row">
                <h2>Absensi Datang</h2>
            </div>
            <div class="row">
                <div class="col-12 p-0 mt-2">
                    <div class="card">
                        <div class="table-responsive">
                            <table class="table align-items-center mb-0" id="absensi-table">
                                <thead class="text-center">
                                    <tr>
                                        <th class="text-uppercase text-secondary font-weight-bolder opacity-7">
                                            No
                                        </th>
                                        <th class="text-uppercase text-secondary font-weight-bolder opacity-7">
                                            Nama
                                        </th>
                                        <th class="text-uppercase text-secondary font-weight-bolder opacity-7">
                                            Waktu absensi
                                        </th>
                                        <th class="text-uppercase text-secondary font-weight-bolder opacity-7">
                                            Keterangan
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="text-center" style="">
                                    @php
                                        $no = 1;
                                    @endphp
                                    @forelse ($absensis as $absensi)
                                        @php
                                            $terlambat_datang = false;
                                            $createdAt = \Carbon\Carbon::parse($absensi->created_at)->format('H:i:s');
                                            $checkIn = \Carbon\Carbon::parse($pengaturan_absensi->check_in)->format(
                                                'H:i:s',
                                            );
                                            if ($createdAt > $checkIn) {
                                                $terlambat_datang = true;
                                            }
                                        @endphp
                                        @if ($absensi->status_absensi === 'datang' && !$terlambat_datang)
                                            <tr>
                                                <td>
                                                    <p class="font-weight-normal mb-0">{{ $no }}</p>
                                                    @php
                                                        $no++;
                                                    @endphp
                                                </td>
                                                <td>
                                                    <p class="font-weight-normal mb-0">{{ $absensi->user->name }}</p>
                                                </td>
                                                <td>
                                                    <p class="font-weight-normal mb-0">{{ $absensi->created_at }}</p>
                                                </td>
                                                @php
                                                    $terlambat_datang = false;
                                                    $createdAt = \Carbon\Carbon::parse($absensi->created_at)->format(
                                                        'H:i:s',
                                                    );
                                                    $checkIn = \Carbon\Carbon::parse(
                                                        $pengaturan_absensi->check_in,
                                                    )->format('H:i:s');
                                                    if ($createdAt > $checkIn) {
                                                        $terlambat_datang = true;
                                                    }
                                                @endphp
                                                <td>
                                                    {{ $absensi->status_absensi }}
                                                </td>
                                            </tr>
                                        @endif
                                    @empty
                                        <tr>
                                            <td colspan="4" class="font-weight-normal">
                                                Tidak ada data absensi!
                                            </td>
                                        </tr>
                                    @endforelse

                                </tbody>
                            </table>
                            {{-- {{ $absensis->links() }} --}}
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <h2>Terlambat</h2>
            </div>
            <div class="row">
                <div class="col-12 p-0 mt-2">
                    <div class="card">
                        <div class="table-responsive">
                            <table class="table align-items-center mb-0" id="absensi-table">
                                <thead class="text-center">
                                    <tr>
                                        <th class="text-uppercase text-secondary font-weight-bolder opacity-7">
                                            No
                                        </th>
                                        <th class="text-uppercase text-secondary font-weight-bolder opacity-7">
                                            Nama
                                        </th>
                                        <th class="text-uppercase text-secondary font-weight-bolder opacity-7">
                                            Waktu absensi
                                        </th>
                                        <th class="text-uppercase text-secondary font-weight-bolder opacity-7">
                                            Keterangan
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="text-center" style="">
                                    @php
                                        $no = 1;
                                    @endphp

                                    @forelse ($absensis as $absensi)
                                        @php
                                            $terlambat_datang = false;
                                            $createdAt = \Carbon\Carbon::parse($absensi->created_at)->format('H:i:s');
                                            $checkIn = \Carbon\Carbon::parse($pengaturan_absensi->check_in)->format(
                                                'H:i:s',
                                            );
                                            if ($createdAt > $checkIn) {
                                                $terlambat_datang = true;
                                            }
                                        @endphp
                                        @if ($terlambat_datang && $absensi->status_absensi == 'datang')
                                            <tr>
                                                <td>
                                                    <p class="font-weight-normal mb-0">{{ $no }}</p>
                                                    @php
                                                        $no++;
                                                    @endphp
                                                </td>
                                                <td>
                                                    <p class="font-weight-normal mb-0">{{ $absensi->user->name }}</p>
                                                </td>
                                                <td>
                                                    <p class="font-weight-normal mb-0">{{ $absensi->created_at }}</p>
                                                </td>

                                                <td>
                                                    <span class="badge bg-danger">Terlambat</span>
                                                </td>
                                            </tr>
                                        @endif
                                    @empty
                                        <tr>
                                            <td colspan="4" class="font-weight-normal">
                                                Tidak ada data absensi!
                                            </td>
                                        </tr>
                                    @endforelse

                                </tbody>
                            </table>
                            {{-- {{ $absensis->links() }} --}}
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <h2>Absensi Pulang</h2>
            </div>
            <div class="row">
                <div class="col-12 p-0 mt-2">
                    <div class="card">
                        <div class="table-responsive">
                            <table class="table align-items-center mb-0" id="absensi-table">
                                <thead class="text-center">
                                    <tr>
                                        <th class="text-uppercase text-secondary font-weight-bolder opacity-7">
                                            No
                                        </th>
                                        <th class="text-uppercase text-secondary font-weight-bolder opacity-7">
                                            Nama
                                        </th>
                                        <th class="text-uppercase text-secondary font-weight-bolder opacity-7">
                                            Waktu absensi
                                        </th>
                                        <th class="text-uppercase text-secondary font-weight-bolder opacity-7">
                                            Keterangan
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="text-center" style="">
                                    @php
                                        $no = 1;
                                    @endphp
                                    @forelse ($absensis as $absensi)
                                        @if ($absensi->status_absensi === 'pulang')
                                            <tr>
                                                <td>
                                                    <p class="font-weight-normal mb-0">{{ $no }}</p>
                                                    @php
                                                        $no++;
                                                    @endphp
                                                </td>
                                                <td>
                                                    <p class="font-weight-normal mb-0">{{ $absensi->user->name }}</p>
                                                </td>
                                                <td>
                                                    <p class="font-weight-normal mb-0">{{ $absensi->created_at }}</p>
                                                </td>
                                                @php
                                                    $terlambat_datang = false;
                                                    $createdAt = \Carbon\Carbon::parse($absensi->created_at)->format(
                                                        'H:i:s',
                                                    );
                                                    $checkIn = \Carbon\Carbon::parse(
                                                        $pengaturan_absensi->check_in,
                                                    )->format('H:i:s');
                                                    if ($createdAt > $checkIn) {
                                                        $terlambat_datang = true;
                                                    }
                                                @endphp
                                                <td>
                                                    {{ $absensi->status_absensi }}


                                                </td>
                                            </tr>
                                        @endif
                                    @empty
                                        <tr>
                                            <td colspan="4" class="font-weight-normal">
                                                Tidak ada data absensi!
                                            </td>
                                        </tr>
                                    @endforelse

                                </tbody>
                            </table>
                            {{-- {{ $absensis->links() }} --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Modal -->
        <div class="modal fade" id="modal_absensi" tabindex="-1" role="dialog"
            aria-labelledby="modal_absensiLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <div class="d-flex modal-title font-weight-normal gap-1">
                            <h5 class="" id="exampleModalLabel">Absensi</h5>
                            @if ($terlambat == true)
                                <span class="badge bg-danger d-flex align-items-center">Terlambat!</span>
                            @endif
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form action="{{ route('absensi.store') }}" method="post">
                        <div class="modal-body">
                            @csrf
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="status_absensi"
                                    id="status_absensi" value="hadir">
                                <label class="custom-control-label" for="status_absensi">Hadir</label>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn bg-gradient-secondary"
                                data-bs-dismiss="modal">Tutup</button>
                            <button type="submit" class="btn bg-gradient-primary">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <script>
            $(document).ready(function() {
                $("#pesan_sukses").delay(3000).fadeOut("slow");
                let currentFilter = null;



                $('.download-pdf').on('click', function(e) {
                    e.preventDefault();
                    let url = "{{ route('absensi.download-pdf') }}";
                    // if (currentFilter) {
                    //     url += `?filter=${currentFilter.filter}&value=${currentFilter.value}`;
                    // }
                    window.location.href = url;
                });
            })
        </script>
    </main>
</x-layout>
