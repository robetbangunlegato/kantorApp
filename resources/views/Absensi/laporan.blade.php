<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Absensi</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #ddd;
            text-align: center;
            padding: 8px;
        }

        th {
            background-color: #f2f2f2;
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
                    @if ($terlambat_datang)
                        @if ($absensi->status_absensi === 'pulang')
                            <td>{{ $absensi->status_absensi }}</td>
                        @elseif($absensi->status_absensi === 'datang')
                            <td>Terlambat</td>
                        @endif
                    @else
                        </td>{{ $absensi->status_absensi }}</td>
                    @endif
                </tr>
            @endforeach
        </tbody>
    </table>
    @if (auth()->user()->role === 'admin')
        <h1 style="text-align: center;">Laporan Gaji</h1>
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>Jabatan</th>
                    <th>Gaji Pokok</th>
                    <th>Jumlah keterlambatan</th>
                    <th>Pinalti 1x keterlambatan</th>
                    <th>Total Pinalti</th>
                    <th>Gaji Akhir</th>
                </tr>
            </thead>
            <tbody>
                @php $no = 1; @endphp
                @foreach ($users as $user)
                    <tr>
                        <td>{{ $no++ }}</td>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->nama_jabatan }}</td>
                        <td>{{ $user->besaran_gaji }}</td>
                        <td>{{ $user->jumlah_keterlambatan }}</td>
                        <td>{{ $user->pinalti_per_keterlambatan }}</td>
                        <td>{{ $user->total_pinalti }}</td>
                        <td>{{ $user->gaji_akhir }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

</body>

</html>
