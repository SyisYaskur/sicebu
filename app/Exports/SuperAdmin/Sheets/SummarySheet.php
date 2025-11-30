<?php

namespace App\Exports\SuperAdmin\Sheets;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class SummarySheet implements FromArray, WithHeadings, WithTitle, ShouldAutoSize
{
    protected $stats;
    protected $filters;

    public function __construct($stats, $filters)
    {
        $this->stats = $stats;
        $this->filters = $filters;
    }

    public function array(): array
    {
        return [
            ['Total Pemasukan', $this->stats['totalIncome']],
            ['Total Pengeluaran', $this->stats['totalExpense']],
            ['Surplus / Defisit', $this->stats['netCashFlow']],
            ['Total Transaksi', $this->stats['transactionCount']],
            [''], // Spacer
            ['FILTER YANG DIGUNAKAN'],
            ['Tanggal Mulai', $this->filters['startDate']],
            ['Tanggal Akhir', $this->filters['endDate']],
            ['Kelas', $this->filters['className']],
            ['Kategori', $this->filters['categoryName']],
        ];
    }

    public function headings(): array
    {
        return ['RINGKASAN KEUANGAN', 'NILAI'];
    }

    public function title(): string
    {
        return 'Ringkasan & Filter';
    }
}