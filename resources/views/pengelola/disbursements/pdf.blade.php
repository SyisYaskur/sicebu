<!DOCTYPE html>
<html>
<head>
    <title>Laporan Penyaluran Dana</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; color: #333; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #333; padding-bottom: 10px; }
        .header h1 { margin: 0; font-size: 18px; text-transform: uppercase; }
        .header h2 { margin: 5px 0; font-size: 14px; font-weight: normal; }
        .meta-info { margin-bottom: 20px; width: 100%; }
        .meta-info td { padding: 3px 0; vertical-align: top; }
        .label { font-weight: bold; width: 130px; }
        
        table.data { width: 100%; border-collapse: collapse; margin-top: 10px; }
        table.data th, table.data td { border: 1px solid #444; padding: 6px 8px; text-align: left; }
        table.data th { background-color: #eee; text-align: center; font-weight: bold; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .grand-total { background-color: #eee; font-weight: bold; }
        
        .signatures { margin-top: 50px; width: 100%; }
        .signatures td { width: 33%; text-align: center; vertical-align: top; }
        .sign-space { height: 70px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>BERITA ACARA PENYALURAN DANA KAS</h1>
        <h2>SICEBU - SMKN 1 Talaga</h2>
    </div>

    <table class="meta-info">
        <tr>
            <td class="label">Tujuan Penyaluran</td>
            <td>: <strong>{{ $disbursement->purpose }}</strong></td>
        </tr>
        <tr>
            <td class="label">Tanggal Penyaluran</td>
            <td>: {{ \Carbon\Carbon::parse($disbursement->disbursement_date)->format('d F Y') }}</td>
        </tr>
        <tr>
            <td class="label">Total Dana</td>
            <td>: <strong>Rp {{ number_format($disbursement->total_amount, 0, ',', '.') }}</strong></td>
        </tr>
        <tr>
            <td class="label">Catatan</td>
            <td>: {{ $disbursement->notes ?? '-' }}</td>
        </tr>
    </table>

    <h3>Rincian Sumber Dana</h3>
    <table class="data">
        <thead>
            <tr>
                <th style="width: 5%">No</th>
                <th>Kelas</th>
                <th>Waktu Transaksi</th>
                <th class="text-right">Saldo Awal</th>
                <th class="text-right">Jumlah Diambil</th>
                <th class="text-right">Saldo Akhir</th>
            </tr>
        </thead>
        <tbody>
            @foreach($disbursement->allocations as $index => $allocation)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $allocation->classRoom->full_name ?? '-' }}</td>
                <td class="text-center">
                    {{ $allocation->created_at->format('d/m/Y H:i') }}
                </td>
                <td class="text-right">
                    Rp {{ number_format($allocation->balance_before, 0, ',', '.') }}
                </td>
                <td class="text-right">
                    Rp {{ number_format($allocation->amount_transferred, 0, ',', '.') }}
                </td>
                <td class="text-right">
                    Rp {{ number_format($allocation->balance_after, 0, ',', '.') }}
                </td>
            </tr>
            @endforeach
            <tr class="grand-total">
                <td colspan="4" class="text-right">TOTAL DISALURKAN</td>
                <td class="text-right">Rp {{ number_format($disbursement->total_amount, 0, ',', '.') }}</td>
                <td></td>
            </tr>
        </tbody>
    </table>

    <table class="signatures">
        <tr>
            <td>
                Mengetahui,<br>Kepala Sekolah
                <div class="sign-space"></div>
                (_______________________)
            </td>
            <td></td>
            <td>
                Majalengka, {{ now()->format('d F Y') }}<br>
                Pengelola Keuangan
                <div class="sign-space"></div>
                <strong>{{ $disbursement->creator->name ?? '.........................' }}</strong>
            </td>
        </tr>
    </table>
</body>
</html>