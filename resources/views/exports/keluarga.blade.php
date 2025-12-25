<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Kartu Keluarga {{ $keluarga->no_kk }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11px;
            margin: 30px;
            color: #000;
        }

        .header {
            text-align: center;
            margin-bottom: 10px;
        }

        .header h2,
        .header h3 {
            margin: 0;
            line-height: 1.2;
        }

        .info-table {
            width: 100%;
            margin-bottom: 10px;
            border-collapse: collapse;
        }

        .info-table td {
            padding: 2px 5px;
            vertical-align: top;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
        }

        .table th,
        .table td {
            border: 1px solid #000;
            padding: 4px;
            text-align: center;
        }

        .table th {
            background-color: #f2f2f2;
        }

        .signature {
            width: 100%;
            margin-top: 40px;
            font-size: 10px;
        }

        .signature td {
            text-align: center;
            vertical-align: bottom;
            height: 80px;
        }
    </style>
</head>

<body>

    {{-- HEADER --}}
    <div class="header">
        <h2>KARTU KELUARGA</h2>
        <h3>No: {{ $keluarga->no_kk ?? '............................' }}</h3>
    </div>

    {{-- FAMILY INFORMATION --}}
    <table class="info-table">
        <tr>
            <td><strong>Kepala Keluarga</strong></td>
            <td>: {{ $keluarga->kepala->nama ?? '..........................................' }}</td>
            <td><strong>Alamat</strong></td>
            <td>: {{ $keluarga->alamat ?? '..........................................' }}</td>
        </tr>
        <tr>
            <td><strong>RT/RW</strong></td>
            <td>: {{ $keluarga->rt->nomor ?? '...' }}/{{ $keluarga->rw->nomor ?? '...' }}</td>
            <td><strong>Desa/Kelurahan</strong></td>
            <td>: {{ $kelurahan->nama ?? '..........................................' }}</td>
        </tr>
        <tr>
            <td><strong>Kecamatan</strong></td>
            <td>: {{ $kelurahan->kecamatan ?? '..........................................' }}</td>
            <td><strong>Kabupaten/Kota</strong></td>
            <td>: {{ $kelurahan->kota ?? '..........................................' }}</td>
        </tr>
        <tr>
            <td><strong>Provinsi</strong></td>
            <td>: {{ $kelurahan->provinsi ?? '..........................................' }}</td>
            <td><strong>Kode Pos</strong></td>
            <td>: {{ $kelurahan->kode_pos ?? '........' }}</td>
        </tr>
    </table>

    {{-- FAMILY MEMBERS --}}
    <table class="table">
        <thead>
            <tr>
                <th>No.</th>
                <th>Nama Lengkap</th>
                <th>NIK</th>
                <th>Jenis Kelamin</th>
                <th>Tempat & Tanggal Lahir</th>
                <th>Usia</th>
                <th>Agama</th>
                <th>Pendidikan</th>
                <th>Pekerjaan</th>
                <th>Status Perkawinan</th>
                <th>SHDK</th>
                {{-- <th>Kewarganegaraan</th> --}}
            </tr>
        </thead>
        <tbody>
            @forelse ($keluarga->penduduks as $i => $anggota)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td style="text-align:left">{{ $anggota->nama }}</td>
                    <td>{{ $anggota->nik }}</td>
                    <td>{{ $anggota->jenis_kelamin }}</td>
                    <td>{{ $anggota->tempat_lahir }},
                        {{ \Carbon\Carbon::parse($anggota->tanggal_lahir)->translatedFormat('d M Y') }}
                    </td>

                    @php
                        $dob = \Carbon\Carbon::parse($anggota->tanggal_lahir);
                        $now = \Carbon\Carbon::now();
                        $diff = $dob->diff($now);
                    @endphp
                    <td>{{ $diff->y }} tahun</td>
                    {{--  {{ $diff->m }} bulan {{ $diff->d }} hari --}}

                    <td>{{ $anggota->agama }}</td>
                    <td>{{ $anggota->pendidikan->name }}</td>
                    <td>{{ $anggota->pekerjaan->name }}</td>
                    <td>{{ $anggota->status_perkawinan }}</td>
                    <td>{{ $anggota->shdk }}</td>
                    {{-- <td>WNI</td> --}}
                </tr>
            @empty
                <tr>
                    <td colspan="11" style="text-align:center">Belum ada data anggota
                        keluarga</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- FOOTER / SIGNATURE --}}
    <table class="signature">
        <tr>
            <td style="width:60%"></td>
            <td>
                {{ $kelurahan->kota ?? '........................' }}, {{ now()->translatedFormat('d F Y') }} <br>
                Kepala Keluarga <br><br><br><br>
                <strong>{{ $keluarga->kepala->nama ?? '................................' }}</strong>
            </td>
        </tr>
    </table>

</body>

</html>
