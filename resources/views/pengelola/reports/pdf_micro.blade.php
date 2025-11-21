<!DOCTYPE html>
<html>
<head>
    <title>Laporan Harian Kelas</title>
    <style>
        body { font-family: 'Helvetica', sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #333; padding-bottom: 10px; }
        h1 { margin: 0; font-size: 18px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        th, td { border: 1px solid #444; padding: 6px; text-align: right; }
        th { background-color: #eee; text-align: center; font-weight: bold; }
        .text-center { text-align: center; }
        .balance-row { background-color: #f9f9f9; font-style: italic; }
    </style>
</head>
<body>
    <div class="header">
        <h1>LAPORAN KEUANGAN HARIAN</h1>
        <p><strong>KELAS: {{ $class->full_name }}</strong></p>
        <p>Periode: {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} s/d {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 25%;">Tanggal</th>
                <th style="width: 25%;">Pemasukan</th>
                <th style="width: 25%;">Pengeluaran</th>
                <th style="width: 25%;">Saldo Akhir</th>
            </tr>
        </thead>
        <tbody>
            @foreach($processedData as $row)
                {{-- Tampilan Khusus untuk Baris Saldo Awal --}}
                @if(isset($row->is_opening) && $row->is_opening)
                <tr class="balance-row">
                    <td class="text-center">Saldo Awal</td>
                    <td>-</td>
                    <td>-</td>
                    <td><strong>Rp {{ number_format($row->balance, 0, ',', '.') }}</strong></td>
                </tr>
                @else
                <tr>
                    <td class="text-center">{{ $row->date->format('d/m/Y') }}</td>
                    <td style="color: green;">
                        {{ $row->income > 0 ? number_format($row->income, 0, ',', '.') : '-' }}
                    </td>
                    <td style="color: red;">
                        {{ $row->expense > 0 ? number_format($row->expense, 0, ',', '.') : '-' }}
                    </td>
                    <td>
                        Rp {{ number_format($row->balance, 0, ',', '.') }}
                    </td>
                </tr>
                @endif
            @endforeach
        </tbody>
    </table>
</body>
</html>