<!DOCTYPE html>
<html>
<head>
    <title>Rekap Pengeluaran</title>
    <style>
        body { font-family: sans-serif; font-size: 11px; }
        .header { text-align: center; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #333; padding: 5px; vertical-align: top; }
        th { background-color: #eee; text-align: center; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
    </style>
</head>
<body>
    <div class="header">
        <h2>REKAPITULASI PENGELUARAN KAS</h2>
        <p>Periode: {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} s/d {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}</p>
        <p>Kelas: {{ $className }}</p>
    </div>

    <table>
        <thead>
            <tr>
                @if($showNo) <th style="width: 5%">No</th> @endif
                <th style="width: 12%">Tanggal</th>
                <th style="width: 15%">Kelas</th>
                @if($showDesc) <th>Keterangan</th> @endif
                <th style="width: 15%">Jumlah (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 1; @endphp
            @foreach($expenses as $expense)
            <tr>
                @if($showNo) <td class="text-center">{{ $no++ }}</td> @endif
                <td class="text-center">{{ \Carbon\Carbon::parse($expense->expense_date)->format('d/m/Y') }}</td>
                <td>{{ $expense->classRoom->full_name }}</td>
                @if($showDesc) 
                    <td>
                        {{ $expense->description }}
                        <br>
                        <span style="color: #555; font-size: 10px;">
                            [Ke: {{ ($expense->recipient == 'Siswa' && $expense->student) ? $expense->student->full_name : ($expense->recipient ?? '-') }}]
                        </span>
                    </td> 
                @endif
                <td class="text-right">{{ number_format($expense->amount, 0, ',', '.') }}</td>
            </tr>
            @endforeach
            <tr style="font-weight: bold; background-color: #f9f9f9;">
                @php 
                    $colspan = 2; 
                    if($showNo) $colspan++;
                    if($showDesc) $colspan++;
                @endphp
                <td colspan="{{ $colspan }}" class="text-center">TOTAL PENGELUARAN</td>
                <td class="text-right">{{ number_format($totalExpense, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>
</body>
</html>