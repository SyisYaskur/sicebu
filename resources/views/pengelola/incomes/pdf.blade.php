<!DOCTYPE html>
<html>
<head>
    <title>Rekap Pemasukan</title>
    <style>
        body { font-family: sans-serif; font-size: 11px; }
        .header { text-align: center; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #333; padding: 5px; }
        th { background-color: #eee; text-align: center; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
    </style>
</head>
<body>
    <div class="header">
        <h2>REKAPITULASI PEMASUKAN KAS</h2>
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
            @foreach($incomes as $income)
            <tr>
                @if($showNo) <td class="text-center">{{ $no++ }}</td> @endif
                <td class="text-center">{{ \Carbon\Carbon::parse($income->date)->format('d/m/Y') }}</td>
                <td>{{ $income->classRoom->full_name }}</td>
                @if($showDesc) <td>{{ $income->description ?? '-' }}</td> @endif
                <td class="text-right">{{ number_format($income->amount, 0, ',', '.') }}</td>
            </tr>
            @endforeach
            <tr style="font-weight: bold; background-color: #f9f9f9;">
                {{-- Hitung colspan dinamis --}}
                @php 
                    $colspan = 2; // Tanggal + Kelas
                    if($showNo) $colspan++;
                    if($showDesc) $colspan++;
                @endphp
                <td colspan="{{ $colspan }}" class="text-center">TOTAL</td>
                <td class="text-right">{{ number_format($totalIncome, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>
</body>
</html>