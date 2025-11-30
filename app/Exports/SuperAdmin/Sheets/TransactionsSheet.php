<?php

namespace App\Exports\SuperAdmin\Sheets;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithMapping;

class TransactionsSheet implements FromCollection, WithHeadings, WithTitle, ShouldAutoSize, WithMapping
{
    protected $transactions;

    public function __construct($transactions)
    {
        $this->transactions = $transactions;
    }

    public function collection()
    {
        return $this->transactions;
    }

    public function map($row): array
    {
        return [
            $row->date->format('Y-m-d'),
            $row->class_name, // Pastikan di query kita select ini
            $row->type == 'income' ? 'Pemasukan' : 'Pengeluaran',
            $row->type == 'income' ? $row->amount : 0,
            $row->type == 'expense' ? $row->amount : 0,
            $row->description,
            $row->pic_name, // Pencatat
        ];
    }

    public function headings(): array
    {
        return [
            'Tanggal',
            'Kelas',
            'Tipe',
            'Pemasukan (Rp)',
            'Pengeluaran (Rp)',
            'Keterangan',
            'Pencatat',
        ];
    }

    public function title(): string
    {
        return 'Data Transaksi';
    }
}