<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Absensi</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }

        table,
        th,
        td {
            border: 1px solid black;
            font-size: 13px;
        }


        th,
        td {
            padding: 8px;
            text-align: center;
        }
    </style>
</head>

<body>
    <h1 style="text-align: center;">Laporan Absensi</h1>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama</th>
                <th>Waktu Absensi</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 1; @endphp
            @foreach ($absensis as $absensi)
                <tr>
                    <td>{{ $no++ }}</td>
                    <td>{{ $absensi->user->name }}</td>
                    <td>{{ $absensi->created_at }}</td>
                    @php
                        $terlambat_datang = false;
                        $createdAt = \Carbon\Carbon::parse($absensi->created_at)->format('H:i:s');
                        $checkIn = \Carbon\Carbon::parse($pengaturan_absensi->check_in)->format('H:i:s');
                        if ($createdAt > $checkIn) {
                            $terlambat_datang = true;
                        }
                    @endphp
                    <td>
                        @if ($absensi->status_absensi === 'izin')
                            {{ $absensi->status_absensi }}
                        @elseif($terlambat_datang)
                            @if ($absensi->status_absensi === 'pulang')
                                {{ $absensi->status_absensi }}
                            @else
                                <p class="text-danger">Terlambat</p>
                            @endif
                        @else
                            {{ $absensi->status_absensi }}
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    @if (auth()->user()->role === 'admin')
        <h2>Laporan Gaji</h2>
        <p>Periode: {{ $startDate->format('d-m-Y') }} s/d {{ $endDate->format('d-m-Y') }}</p>
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>Jabatan</th>
                    <th>Gaji Pokok</th>
                    <th>Izin</th>
                    <th>Pinalti Izin</th>
                    <th>Pinalti Keterlambatan</th>
                    <th>Total Pinalti</th>
                    <th>Gaji Akhir</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $item)
                    <tr>
                        <td>{{ $item->nomor }}</td>
                        <td>{{ $item->nama }}</td>
                        <td>{{ $item->jabatan }}</td>
                        <td>Rp {{ number_format($item->gaji_pokok, 0, ',', '.') }}</td>
                        <td>{{ $item->izin }}</td>
                        <td>Rp {{ number_format($item->pinalti_izin, 0, ',', '.') }}</td>
                        <td>Rp {{ number_format($item->pinalti_keterlambatan, 0, ',', '.') }}</td>
                        <td>Rp {{ number_format($item->total_pinalti, 0, ',', '.') }}</td>
                        <td>Rp {{ number_format($item->gaji_akhir, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <p>Terlambat</p>
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>Keterlambatan</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $item)
                    <tr>
                        <td>{{ $item->nomor }}</td>
                        <td>{{ $item->nama }}</td>
                        <td>{{ $item->keterlambatan_format }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <p>Tepat waktu</p>
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>Kehadiran</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $item)
                    <tr>
                        <td>{{ $item->nomor }}</td>
                        <td>{{ $item->nama }}</td>
                        <td>{{ $item->kehadiran_format }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

</body>

</html>
