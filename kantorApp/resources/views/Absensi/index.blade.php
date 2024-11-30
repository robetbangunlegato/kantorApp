<x-layout bodyClass="g-sidenav-show  bg-gray-200">
    <x-navbars.sidebar activePage='absensi'></x-navbars.sidebar>
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">
        <!-- Navbar -->
        <x-navbars.navs.auth titlePage="Absensi"></x-navbars.navs.auth>
        <!-- End Navbar -->
        <div class="container-fluid py-4">
            @if (session('sukses'))
                <div class="row">
                    <div class="alert alert-success text-white" role="alert" id="pesan_sukses">
                        <strong>Berhasil!</strong> {{ session('sukses') }}
                    </div>
                </div>
            @endif
            <div class="row">
                <a href="{{ route('pengaturanabsensi.index') }}" class="btn btn-info my-2 flex w-100">Pengaturan
                    absensi</a>
            </div>
            <div class="row">
                @if ($hasil_cek_waktu)
                    <a href="{{ route('absensi.create') }}" class="btn btn-primary my-2 w-100" data-bs-toggle="modal"
                        data-bs-target="#modal_absensi">Absensi</a>
                @else
                    <a class="btn btn-secondary mt-2 mb-0 w-100"" disabled>Absensi</a>
                    <p class="m-0"><small>Absensi tidak tersedia!</small></p>
                @endif
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
            })
        </script>
    </main>
</x-layout>
