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
    public function index(Request $request)
{
    // 1. SYARAT ALAMAT IP
    $ip_pengguna = $request->ip();
    $hasil_cek_ip = PengaturanAbsensi::where('rentang_awal_IP', '<=', $ip_pengguna)
        ->where('rentang_akhir_IP', '>=', $ip_pengguna)
        ->exists();

    // 2. SYARAT TIDAK MELAKUKAN DOUBLE ABSENSI KEDATANGAN PADA HARI YANG SAMA
    $double_absensi_datang = Absensi::where('user_id', auth()->id())
        ->whereDate('created_at', Carbon::today())
        ->where('status_absensi', 'datang')
        ->orwhere('status_absensi','izin')
        ->exists();
    //  3.FILTER


     $absensis = \App\Models\Absensi::query();
    
    // Filter berdasarkan request
    if ($request->has('filter') && $request->filter === 'today') {
        $absensis->whereDate('created_at', Carbon::today());
    } elseif ($request->has('month') && $request->has('year')) {
        // Prioritaskan filter bulan dan tahun
        $absensis->whereYear('created_at', $request->year)
                ->whereMonth('created_at', $request->month);
    } elseif ($request->has('year')) {
        // Jika hanya filter tahun
        $absensis->whereYear('created_at', $request->year);
    }

    $absensis = $absensis->get();

    // Dapatkan tahun yang tersedia dari data absensi
    $availableYears = \App\Models\Absensi::selectRaw('YEAR(created_at) as year')
        ->distinct()
        ->pluck('year');

    // Dapatkan bulan yang tersedia dari data absensi
    $availableMonths = \App\Models\Absensi::selectRaw('MONTH(created_at) as month, YEAR(created_at) as year')
        ->distinct()
        ->get()
        ->map(function ($item) {
            $date = Carbon::create($item->year, $item->month, 1);
            return [
                'key' => $item->year . '-' . str_pad($item->month, 2, '0', STR_PAD_LEFT),
                'label' => $date->translatedFormat('F Y'),
            ];
        });
    // 5. DATA PENGATURAN ABSENSI
    $pengaturan_absensi = PengaturanAbsensi::find(1);
    $checkIn = $pengaturan_absensi->check_in;
    $checkOut = $pengaturan_absensi->check_out;

    // 6. CEK KETERLAMBATAN UNTUK MODAL ABSENSI
    $terlambat = null;
    if (Carbon::now()->between(Carbon::parse($pengaturan_absensi->check_in), Carbon::parse($pengaturan_absensi->check_out))) {
        $terlambat = true;
    }

    // 7. SYARAT DOUBLE ABSENSI PULANG DAN VALIDASI
    $double_absensi_pulang = Absensi::where('user_id', auth()->id())
        ->whereDate('created_at', Carbon::today())
        ->where('status_absensi', 'pulang')
        ->exists();


    

    $waktu_absensi_pulang = Carbon::now()->greaterThan(
        Carbon::createFromFormat('H:i:s.u', $pengaturan_absensi->check_out)
    );

    $validasi_absensi_pulang = Absensi::where('user_id', auth()->id())
        ->where('status_absensi', 'datang')
        ->whereDate('created_at', Carbon::today())
        ->exists();

    // 8. IZIN
    $izin = Absensi::where('user_id', auth()->id())
        ->whereDate('created_at', Carbon::today())
        ->where('status_absensi', 'izin')
        ->exists();

    return view('Absensi.index', compact(
        'hasil_cek_ip',
        'absensis',
        'terlambat',
        'pengaturan_absensi',
        'double_absensi_datang',
        'double_absensi_pulang',
        'waktu_absensi_pulang',
        'validasi_absensi_pulang',
        'availableYears', 
        'availableMonths',
        'izin',
    ));
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
    // Tentukan range waktu default (30 hari terakhir)
    $startDate = $request->has('month') 
        ? Carbon::create($request->year, $request->month, 1)->startOfMonth()
        : Carbon::now()->subDays(29)->startOfDay();

    $endDate = $request->has('month') 
        ? Carbon::create($request->year, $request->month, 1)->endOfMonth()
        : Carbon::now()->endOfDay();

    // Query data
    $data = \DB::table('absensis')
    ->join('users', 'absensis.user_id', '=', 'users.id')
    ->join('data_pribadis', 'users.id', '=', 'data_pribadis.user_id')
    ->join('jabatan_organisasis', 'data_pribadis.jabatan_organisasi_id', '=', 'jabatan_organisasis.id')
    ->join('pengaturan_absensis', \DB::raw('1'), '=', \DB::raw('1')) // Join dummy karena tidak ada relasi langsung
    ->select(
        'users.name as nama',
        'jabatan_organisasis.nama_jabatan as jabatan',
        'jabatan_organisasis.besaran_gaji as gaji_pokok',
        \DB::raw('COUNT(CASE WHEN absensis.status_absensi = "datang" AND TIME(absensis.created_at) <= pengaturan_absensis.check_in THEN 1 END) as kehadiran'),
        \DB::raw('COUNT(CASE WHEN absensis.status_absensi = "izin" THEN 1 END) as izin'),
        \DB::raw('SUM(CASE WHEN absensis.status_absensi = "datang" AND TIME(absensis.created_at) > pengaturan_absensis.check_in THEN 1 ELSE 0 END) as keterlambatan')
    )
    ->whereBetween('absensis.created_at', [$startDate, $endDate])
    ->groupBy('users.id', 'jabatan_organisasis.nama_jabatan', 'jabatan_organisasis.besaran_gaji')
    ->get();

    // Tambahkan data kalkulasi
    foreach ($data as $index => $item) {
        $item->nomor = $index + 1;
        $item->total_hari = $startDate->diffInDays($endDate) + 1;
        $item->kehadiran_format = "{$item->kehadiran}/{$item->total_hari}";
        $item->keterlambatan_format = "{$item->keterlambatan}/{$item->total_hari}";
        $item->pinalti_izin = max(0, ($item->izin - 3) * 50000);
        $item->pinalti_keterlambatan = $item->keterlambatan * 25000;
        $item->total_pinalti = $item->pinalti_izin + $item->pinalti_keterlambatan;
        $item->gaji_akhir = $item->gaji_pokok - $item->total_pinalti;
    }

    // $absensi = null;
    if ($request->year == null || $request->month == null) {  
        // Jika tidak ada parameter year dan month
        $absensis = Absensi::whereBetween('created_at', [$startDate, $endDate])->get();
    }else{
        $absensis = Absensi::whereYear('created_at',$request->year )->whereMonth('created_at',$request->month)->get();
    }
   
    $pengaturan_absensi = PengaturanAbsensi::find(1);

    // Generate PDF
    $pdf = Pdf::loadView('Absensi.laporan', compact('data', 'startDate', 'endDate','absensis','pengaturan_absensi'))->setPaper('f4','landscape');

    // Unduh PDF
    return $pdf->download('laporan_absensi.pdf');
        
    // $pdf = Pdf::loadView('Absensi.laporan', compact('absensis', 'pengaturan_absensi','users','terlambat_datang'));
    // return $pdf->download('laporan_absensi.pdf');
}
}


