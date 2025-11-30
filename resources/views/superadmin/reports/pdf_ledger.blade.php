<!DOCTYPE html>
<html>
<head>
    <title>Laporan Keuangan Lengkap</title>
    <style>
        body { font-family: sans-serif; font-size: 10px; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #333; padding-bottom: 10px; }
        h1 { margin: 0; font-size: 18px; text-transform: uppercase; }
        .meta-table { width: 100%; margin-bottom: 15px; font-size: 11px; }
        .meta-table td { padding: 2px; }
        
        .summary-box { border: 1px solid #000; padding: 10px; margin-bottom: 20px; }
        .summary-title { font-weight: bold; font-size: 12px; border-bottom: 1px solid #ccc; margin-bottom: 5px; padding-bottom: 2px; }
        
        table.data { width: 100%; border-collapse: collapse; }
        table.data th, table.data td { border: 1px solid #444; padding: 5px; text-align: left; word-wrap: break-word; }
        table.data th { background-color: #eee; text-align: center; font-weight: bold; }
        
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .text-success { color: green; }
        .text-danger { color: red; }
        .fw-bold { font-weight: bold; }
        
        .footer { margin-top: 30px; text-align: right; font-style: italic; font-size: 9px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>LAPORAN KEUANGAN TERPUSAT (LEDGER)</h1>
        <p>SICEBU - SMKN 1 Talaga</p>
    </div>

    {{-- Informasi Filter & Ringkasan --}}
    <table class="meta-table">
        <tr>
            <td width="60%" valign="top">
                <strong>FILTER DATA:</strong><br>
                Periode: {{ $filters['startDate'] }} s/d {{ $filters['endDate'] }}<br>
                Kelas: {{ $filters['className'] }}<br>
                Kategori: {{ ucfirst($filters['categoryName']) }}
            </td>
            <td width="40%" valign="top">
                <div class="summary-box">
                    <div class="summary-title">RINGKASAN KEUANGAN</div>
                    <table width="100%">
                        <tr>
                            <td>Total Pemasukan</td>
                            <td class="text-right text-success">{{ number_format($stats['totalIncome'], 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td>Total Pengeluaran</td>
                            <td class="text-right text-danger">{{ number_format($stats['totalExpense'], 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold" style="border-top: 1px solid #999;">Arus Kas Bersih</td>
                            <td class="text-right fw-bold" style="border-top: 1px solid #999;">{{ number_format($stats['netCashFlow'], 0, ',', '.') }}</td>
                        </tr>
                    </table>
                </div>
            </td>
        </tr>
    </table>

    {{-- Tabel Data Transaksi --}}
    <table class="data">
        <thead>
            <tr>
                <th style="width: 4%">No</th>
                <th style="width: 10%">Tanggal</th>
                <th style="width: 12%">Kelas</th>
                <th style="width: 8%">Tipe</th>
                <th style="width: 28%">Keterangan & Penerima</th>
                <th style="width: 12%" class="text-right">Masuk (Rp)</th>
                <th style="width: 12%" class="text-right">Keluar (Rp)</th>
                <th style="width: 14%">Pencatat</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 1; @endphp
            @foreach($transactions as $t)
            <tr>
                <td class="text-center">{{ $no++ }}</td>
                <td class="text-center">{{ \Carbon\Carbon::parse($t->date)->format('d/m/Y') }}</td>
                <td>{{ $t->class_name }}</td>
                <td class="text-center">
                    @if($t->type == 'income') <span class="text-success">Pemasukan</span> 
                    @else <span class="text-danger">Pengeluaran</span> @endif
                </td>
                <td>
                    {{ $t->description }}
                    @if($t->type == 'expense' && $t->recipient)
                        <br><i style="font-size: 9px; color: #555;">Ke: {{ $t->recipient }}</i>
                    @endif
                </td>
                <td class="text-right text-success">
                    {{ $t->type == 'income' ? number_format($t->amount, 0, ',', '.') : '-' }}
                </td>
                <td class="text-right text-danger">
                    {{ $t->type == 'expense' ? number_format($t->amount, 0, ',', '.') : '-' }}
                </td>
                <td>{{ $t->pic_name }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Dicetak oleh: {{ Auth::user()->name }} pada {{ now()->format('d F Y H:i') }}
    </div>
</body>
</html>