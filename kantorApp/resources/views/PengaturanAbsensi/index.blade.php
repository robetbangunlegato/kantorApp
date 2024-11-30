<x-layout bodyClass="g-sidenav-show  bg-gray-200">
    <x-navbars.sidebar activePage='absensi'></x-navbars.sidebar>
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">
        <!-- Navbar -->
        <x-navbars.navs.auth titlePage="Pengaturan waktu absensi"></x-navbars.navs.auth>
        <!-- End Navbar -->
        <div class="container-fluid py-4">
            <div class="col-12">
                <form action="{{ route('pengaturanabsensi.update', $pengaturan_absensi[0]->id) }}" method="post">
                    @csrf
                    @method('PUT')
                    <div class="row my-3">
                        <div class="col-xl-6 col-sm-12">
                            <div class="input-group input-group-static">
                                <label for="pengaturanWaktuBukaAbsensi">Waktu Buka</label>
                                <input type="time" class="form-control" id="pengaturanWaktuBukaAbsensi"
                                    name="waktu_buka" value="{{ $pengaturan_absensi[0]->waktu_buka }}">
                            </div>
                        </div>
                        <div class="col-xl-6 col-sm-12">
                            <div class="input-group input-group-static">
                                <label for="pengaturanWaktuTutupAbsensi">Waktu Tutup</label>
                                <input type="time" class="form-control" id="pengaturanWaktuTutupAbsensi"
                                    name="waktu_tutup" value="{{ $pengaturan_absensi[0]->waktu_tutup }}">
                            </div>
                        </div>
                        <input type="hidden" name="id" value="{{ $pengaturan_absensi[0]->id }}">
                    </div>
                    <div class="row my-3">
                        <button class="btn btn-primary" type="submit">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </main>
</x-layout>
