<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Keuangan</title>
    <style>
        body {
            font-family: 'Helvetica', sans-serif;
            font-size: 11px;
            color: #333;
        }
        .container {
            width: 100%;
            margin: 0 auto;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .header p {
            margin: 2px 0;
            font-size: 14px;
        }
        .table-wrapper {
            margin-bottom: 25px;
        }
        h2 {
            font-size: 16px;
            border-bottom: 1px solid #ccc;
            padding-bottom: 5px;
            margin-bottom: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: left;
            word-wrap: break-word; /* Mencegah teks terlalu panjang */
        }
        th {
            background-color: #f4f4f4;
            font-size: 12px;
        }
        td.text-right, th.text-right {
            text-align: right;
        }
        .summary-table td {
            font-size: 14px;
            font-weight: bold;
            border: none;
            padding: 5px;
        }
        .summary-table .balance {
            border-top: 2px solid #333;
            color: #000;
        }
        .footer {
            margin-top: 40px;
            text-align: right;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>LAPORAN KEUANGAN KAS KELAS</h1>
            <p style="font-size: 18px; font-weight: bold;">KELAS: {{ $class->name }} ({{ $class->academic_year }})</p>
            <p>Wali Kelas: {{ $class->teacher_name ?? Auth::user()->name }}</p>
            <p>Periode: {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} s/d {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}</p>
        </div>

        <h2>Ringkasan Saldo</h2>
        <table class="summary-table">
            <tr>
                <td style="width: 70%;">Total Pemasukan</td>
                <td class="text-right" style="color: green;">Rp {{ number_format($stats['totalIncome'], 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Total Pengeluaran</td>
                <td class="text-right" style="color: red;">Rp {{ number_format($stats['totalExpense'], 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td class="balance">Sisa Saldo</td>
                <td class="text-right balance">Rp {{ number_format($stats['balance'], 0, ',', '.') }}</td>
            </tr>
        </table>

        <div class="table-wrapper">
            <h2>Rincian Pemasukan</h2>
            <table>
                <thead>
                    <tr>
                        <th style="width: 5%;">No</th>
                        <th style="width: 15%;">Tanggal</th>
                        <th class="text-right" style="width: 20%;">Jumlah (Rp)</th>
                        <th>Keterangan</th>
                        <th style="width: 20%;">Dicatat Oleh</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($incomes as $index => $income)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $income->date->format('d M Y') }}</td>
                        <td class="text-right">{{ number_format($income->amount, 0, ',', '.') }}</td>
                        <td>{{ $income->description ?? '-' }}</td>
                        <td>{{ $income->creator->name ?? '-' }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="5" style="text-align: center;">Tidak ada data pemasukan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="table-wrapper">
            <h2>Rincian Pengeluaran</h2>
            <table>
                <thead>
                    <tr>
                        <th style="width: 5%;">No</th>
                        <th style="width: 15%;">Tanggal</th>
                        <th class="text-right" style="width: 15%;">Jumlah (Rp)</th>
                        <th style="width: 25%;">Keterangan</th>
                        <th style="width: 20%;">Penerima</th>
                        {{-- REVISI 1: Tambah Kolom Header --}}
                        <th style="width: 20%;">Dicatat Oleh</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($expenses as $index => $expense)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $expense->expense_date->format('d M Y') }}</td>
                        <td class="text-right">{{ number_format($expense->amount, 0, ',', '.') }}</td>
                        <td>{{ $expense->description }}</td>
                        <td>{{ $expense->recipient ?? '-' }}</td>
                        {{-- REVISI 2: Tambah Data Pencatat --}}
                        <td>{{ $expense->creator->name ?? '-' }}</td>
                    </tr>
                    @empty
                    {{-- REVISI 3: Update colspan dari 5 menjadi 6 --}}
                    <tr><td colspan="6" style="text-align: center;">Tidak ada data pengeluaran.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="footer">
            <p>Dicetak pada: {{ now()->format('d M Y H:i') }}</p>
        </div>
    </div>
</body>
</html>