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
            <div class="row">
                @if ($hasil_cek_waktu && $hasil_cek_ip)
                    @if ($hasil_cek_double_absensi === false)
                        <a href="{{ route('absensi.create') }}" class="btn btn-primary my-2 w-100"
                            data-bs-toggle="modal" data-bs-target="#modal_absensi">Absensi</a>
                    @else
                        <a class="btn btn-secondary mt-2 mb-0 w-100"" disabled>Absensi</a>
                        <p class="m-0"><small>Absensi tidak tersedia! anda sudah melakukan absensi hari
                                ini!</small>
                        </p>
                    @endif
                @else
                    <a class="btn btn-secondary mt-2 mb-0 w-100"" disabled>Absensi</a>
                    @if ($hasil_cek_waktu === false)
                        <p class="m-0"><small>Absensi tidak tersedia! waktu habis!</small></p>
                    @elseif($hasil_cek_ip === false)
                        <p class="m-0"><small>Absensi tidak tersedia! anda berada di luar jangkauan jaringan!</small>
                        </p>
                    @endif
                @endif
            </div>
            <div class="row">
                <div class="col-6 d-flex" style="gap: 10px">
                    <button class="filter-btn btn btn-info" data-filter="hari"
                        data-value="{{ \Carbon\Carbon::today()->toDateString() }}">
                        Filter Hari Ini
                    </button>
                    <button class="filter-btn btn btn-info" data-filter="bulan"
                        data-value="{{ \Carbon\Carbon::now()->format('Y-m') }}">
                        Filter Bulan Ini
                    </button>
                    <button class="filter-btn btn btn-info" data-filter="tahun"
                        data-value="{{ \Carbon\Carbon::now()->year }}">
                        Filter Tahun Ini
                    </button>
                </div>
                <div class="col-6">

                </div>
            </div>
            <div class="row">
                <div class="col-12 p-0 mt-2">
                    <div class="card">
                        <div class="table-responsive">
                            <table class="table align-items-center mb-0" id="absensi-table">
                                <thead class="text-center">
                                    <tr>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            No
                                        </th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Nama
                                        </th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Waktu absensi
                                        </th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Keterangan
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="text-center">
                                    @php
                                        $no = 1;
                                    @endphp
                                    @forelse ($absensis as $absensi)
                                        <tr>
                                            <td>
                                                <p class="text-xs font-weight-normal mb-0">{{ $no }}</p>
                                                @php
                                                    $no++;
                                                @endphp
                                            </td>
                                            <td>
                                                <p class="text-xs font-weight-normal mb-0">{{ $absensi->user->name }}
                                                </p>
                                            </td>
                                            <td>
                                                <p class="text-xs font-weight-normal mb-0">{{ $absensi->created_at }}
                                                </p>
                                            </td>
                                            <td>
                                                <p class="text-xs font-weight-normal mb-0">
                                                    {{ $absensi->status_absensi }}</p>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-xs font-weight-normal">
                                                Tidak ada data absensi !
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
        <div class="modal fade" id="modal_absensi" tabindex="-1" role="dialog" aria-labelledby="modal_absensiLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title font-weight-normal" id="exampleModalLabel">Absensi</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form action="{{ route('absensi.store') }}" method="post">
                        <div class="modal-body">
                            @csrf
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="status_absensi" id="status_absensi"
                                    value="hadir">
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
                $('.filter-btn').on('click', function() {
                    const filter = $(this).data('filter'); // hari, bulan, atau tahun
                    const value = $(this).data('value'); // Nilai filter

                    $.ajax({
                        url: '/absensi',
                        type: 'GET',
                        data: {
                            filter: filter,
                            value: value
                        },
                        success: function(response) {
                            const tbody = $('#absensi-table tbody');
                            tbody.empty();

                            if (response.data.length > 0) {
                                let no = 1; // Nomor urut
                                response.data.forEach(item => {
                                    // Gunakan moment.js untuk memformat created_at
                                    const formattedDate = moment(item.created_at).format(
                                        'DD-MM-YYYY HH:mm:ss');
                                    const userName = item.user ? item.user.name :
                                        'Nama tidak tersedia';
                                    const row = `<tr>
                            <td><p class="text-xs font-weight-normal mb-0">${no}</p></td>
                            <td><p class="text-xs font-weight-normal mb-0">${userName}</p></td>
                            <td><p class="text-xs font-weight-normal mb-0">${formattedDate}</p></td>
                            <td><p class="text-xs font-weight-normal mb-0">${item.status_absensi}</p></td>
                        </tr>`;
                                    tbody.append(row);
                                    no++;
                                });
                            } else {
                                tbody.append(`
                        <tr>
                            <td colspan="4" class="text-xs font-weight-normal">Tidak ada data absensi !</td>
                        </tr>
                    `);
                            }
                        },
                        error: function(err) {
                            console.error('Gagal memuat data:', err);
                        }
                    });
                });
            })
        </script>
    </main>
</x-layout>
