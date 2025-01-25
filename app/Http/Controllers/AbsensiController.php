<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\PengaturanAbsensi;
use App\Models\Absensi;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\User;

class AbsensiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request){

   

    // 2. SYARAT ALAMAT IP
    $ip_pengguna = $request->ip();
    $hasil_cek_ip = PengaturanAbsensi::where('rentang_awal_IP', '<=', $ip_pengguna)
                                     ->where('rentang_akhir_IP', '>=', $ip_pengguna)
                                     ->exists();

    // 3. SYARAT TIDAK MELAKUKAN DOUBLE ABSENSI KEDATANGAN PADA HARI YANG SAMA
    $double_absensi_datang = Absensi::where('user_id', auth()->id())
                                       ->whereDate('created_at', Carbon::today())
                                       ->where('status_absensi', 'datang')
                                       ->exists();


    // 4. MENGAMBIL DATA ABSENSI SESUAI PENGGUNA
    $pengguna_aktif = auth()->user();
    if ($pengguna_aktif->email == 'admin@material.com') {
        $absensis = Absensi::all();
    } else {
        $absensis = $pengguna_aktif->absensis;
    }

    // 5. DATA PENGATURAN ABSENSI
    $pengaturan_absensi = PengaturanAbsensi::find(1);
    $checkIn = $pengaturan_absensi->check_in;
    $checkOut = $pengaturan_absensi->check_out;

     // Logika Filter
   if ($request->ajax()) {
        $pengaturan_absensi = PengaturanAbsensi::find(1);
        $checkIn = $pengaturan_absensi->check_in;
        $checkOut = $pengaturan_absensi->check_out;
        $filterType = $request->input('filter'); // hari, bulan, atau tahun
        $filterValue = $request->input('value'); // nilai filter

        $query = Absensi::query();

        // Terapkan filter berdasarkan parameter
        if ($filterType === 'hari' && $filterValue) {
            $query->whereDate('created_at', $filterValue);
        } elseif ($filterType === 'bulan' && $filterValue) {
            $query->whereMonth('created_at', Carbon::parse($filterValue)->month)
                ->whereYear('created_at', Carbon::parse($filterValue)->year);
        } elseif ($filterType === 'tahun' && $filterValue) {
            $query->whereYear('created_at', $filterValue);
        }

        // Filter data sesuai peran pengguna
        $pengguna_aktif = auth()->user();
        if ($pengguna_aktif->role == 'pegawai') {
            $query->where('user_id', $pengguna_aktif->id);
        }

        // Tambahkan eager loading untuk relasi `user`
        $filteredData = $query->with('user')->get()->map(function ($item) {
            return [
                'id' => $item->id,
                'user' => $item->user,
                'created_at' => $item->created_at->toIso8601String(), // Format ISO 8601
                'status_absensi' => $item->status_absensi,
            ];
        });

        // Kembalikan respons JSON untuk AJAX
        return response()->json([
            'data' => $filteredData,
            'checkIn' => $checkIn,
            'checkOut' => $checkOut
        ]);
    }


    // 6.CEK KETERLAMBATAN UNTUK MELABELI HEADER MODAL ABSENSI
    $terlambat = null;
    if (Carbon::now()->between(Carbon::parse($pengaturan_absensi->check_in),Carbon::parse($pengaturan_absensi->check_out) )){
        $terlambat = true;
    };


    $terlambat_datang =  false;
    if(Carbon::now()->greaterThan(Carbon::parse($pengaturan_absensi->check_in))){
        $terlambat_datang = true;
    };

    // 7.  SYARAT TIDAK MELAKUKAN DOUBLE ABSENSI PULANG PADA HARI YANG SAMA
    $double_absensi_pulang = Absensi::where('user_id', auth()->id())
                                       ->whereDate('created_at', Carbon::today())
                                       ->where('status_absensi', 'pulang')
                                       ->exists();

    // 7.  SYARAT TIDAK MELAKUKAN ABSENSI PULANG SEBELUM WAJTU PULANG
    $pengaturan_absensi = PengaturanAbsensi::find(1);
   
    $waktu_absensi_pulang = Carbon::now()->greaterThan(
    Carbon::createFromFormat('H:i:s.u', $pengaturan_absensi->check_out));

    $validasi_absensi_pulang = Absensi::where('user_id', auth()->id())
    ->where('status_absensi', 'datang')
    ->whereDate('created_at', Carbon::today()) // Mengecek data pada hari ini
    ->exists();
    
    // dd($double_absensi_pulang);

    
    
    return view('Absensi.index', compact('hasil_cek_ip', 'absensis', 'terlambat', 'pengaturan_absensi','double_absensi_datang', 'double_absensi_pulang', 'waktu_absensi_pulang','terlambat_datang','validasi_absensi_pulang'));
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

        $ip_pengguna = auth()->id();

        $absensi = new Absensi();
        $absensi->user_id = auth()->id();
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

   

public function downloadPDF(Request $request)
{
    $pengaturan_absensi = PengaturanAbsensi::find(1);
    $filterType = $request->input('filter'); // hari, bulan, atau tahun
    $filterValue = $request->input('value'); // nilai filter

    $query = Absensi::query();

    if ($filterType === 'hari' && $filterValue) {
        $query->whereDate('created_at', $filterValue);
    } elseif ($filterType === 'bulan' && $filterValue) {
        $query->whereMonth('created_at', Carbon::parse($filterValue)->month)
              ->whereYear('created_at', Carbon::parse($filterValue)->year);
    } elseif ($filterType === 'tahun' && $filterValue) {
        $query->whereYear('created_at', $filterValue);
    }

    $penggunaAktif = auth()->user();
    if ($penggunaAktif->email !== 'admin@material.com') {
        $query->where('user_id', $penggunaAktif->id);
    }
    
    $absensis = $query->orderBy('created_at', 'desc')->get();

    $users = User::with(['jabatan', 'absensis'])
        ->whereHas('absensis', function ($query) {
            $query->whereBetween('created_at', [
                Carbon::now()->startOfMonth(),
                Carbon::now()->endOfMonth(),
            ]);
        })
        ->get();

        // Ambil waktu check_in dan check_out dari tabel pengaturan absensi
        $pengaturan_absensi = PengaturanAbsensi::first(); // Asumsi hanya ada satu baris
        $checkIn = $pengaturan_absensi->check_in ?? '09:00:00'; // Default waktu jika tidak ditemukan
        $checkOut = $pengaturan_absensi->check_out ?? '17:00:00';

        $users = User::select('users.id', 'users.name', 'jabatan_organisasis.nama_jabatan', 'jabatan_organisasis.besaran_gaji')
    ->join('data_pribadis', 'users.id', '=', 'data_pribadis.user_id')
    ->join('jabatan_organisasis', 'data_pribadis.jabatan_organisasi_id', '=', 'jabatan_organisasis.id')
    ->withCount(['absensis as jumlah_keterlambatan' => function ($query) use ($checkIn) {
        $query->whereRaw("TIME(created_at) > ?", [$checkIn])
              ->where('status_absensi', 'datang');
    }])
    ->get()
    ->map(function ($user) {
        $user->pinalti_per_keterlambatan = 50000;
        $user->total_pinalti = $user->jumlah_keterlambatan * $user->pinalti_per_keterlambatan;
        $user->gaji_akhir = $user->besaran_gaji - $user->total_pinalti;
        return $user;
    });


            $terlambat_datang =  false;
    if(Carbon::now()->greaterThan(Carbon::parse($pengaturan_absensi->check_in))){
        $terlambat_datang = true;
    };
        
    $pdf = Pdf::loadView('Absensi.laporan', compact('absensis', 'pengaturan_absensi','users','terlambat_datang'));
    return $pdf->download('laporan_absensi.pdf');
}
}


