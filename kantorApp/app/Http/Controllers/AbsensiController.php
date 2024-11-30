<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\PengaturanAbsensi;
use App\Models\Absensi;
class AbsensiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //mengambil waktu sekarang
        $waktu_sekarang = carbon::now();
        
        // ambil informasi waktu_buka dan waktu_tutup dari tabel pengaturan_absensis langsung konversi ke laravel carbon
        $pengaturan_absensis = PengaturanAbsensi::all();
        $waktu_buka = Carbon::parse($pengaturan_absensis[0]->waktu_buka);
        $waktu_tutup = Carbon::parse($pengaturan_absensis[0]->waktu_tutup);

        // 
        $hasil_cek_waktu = $waktu_sekarang->between($waktu_buka, $waktu_tutup);  // akan menghasilkan nilai 'true' atau 'false'
        return view('Absensi.index', compact('hasil_cek_waktu'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        $validasi = $request->validate([
            'status_absensi' => 'required'
        ]);

        $absensi = new Absensi();
        $absensi->status_absensi = $validasi['status_absensi'];
        $absensi->save();

        return redirect()->route('absensi.index')->with('sukses', 'Absensi berhasil dilakukan!');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
