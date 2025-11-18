<!DOCTYPE html>
<html>
<head>
    <title>Laporan Rekapitulasi Sekolah</title>
    <style>
        body { font-family: sans-serif; font-size: 11px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #333; padding: 6px; }
        th { background-color: #ddd; text-align: center; }
        .text-right { text-align: right; }
        .header { text-align: center; margin-bottom: 20px; }
        h1 { margin: 0; font-size: 18px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>LAPORAN REKAPITULASI KEUANGAN SEKOLAH</h1>
        <p>Periode: {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} s/d {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Kelas</th>
                <th>Wali Kelas</th>
                <th>Pemasukan</th>
                <th>Pengeluaran</th>
                <th>Saldo</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 1; @endphp
            @foreach($recapData as $row)
            <tr>
                <td style="text-align: center;">{{ $no++ }}</td>
                <td>{{ $row->name }}</td>
                <td>{{ $row->teacher_name }}</td>
                <td class="text-right">{{ number_format($row->incomes_sum_amount, 0, ',', '.') }}</td>
                <td class="text-right">{{ number_format($row->expenses_sum_amount, 0, ',', '.') }}</td>
                <td class="text-right">{{ number_format($row->incomes_sum_amount - $row->expenses_sum_amount, 0, ',', '.') }}</td>
            </tr>
            @endforeach
            <tr style="background-color: #eee; font-weight: bold;">
                <td colspan="3" style="text-align: center;">GRAND TOTAL</td>
                <td class="text-right">{{ number_format($grandTotalIncome, 0, ',', '.') }}</td>
                <td class="text-right">{{ number_format($grandTotalExpense, 0, ',', '.') }}</td>
                <td class="text-right">{{ number_format($grandTotalIncome - $grandTotalExpense, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>
    <div style="margin-top: 30px; text-align: right;">
        <p>Dicetak oleh: {{ Auth::user()->name }}<br>Tanggal: {{ now()->format('d M Y H:i') }}</p>
    </div>
</body>
</html>