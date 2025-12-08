<?php

namespace App\Exports\SuperAdmin;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use App\Exports\SuperAdmin\Sheets\SummarySheet;
use App\Exports\SuperAdmin\Sheets\TransactionsSheet;

class FinancialReportExport implements WithMultipleSheets
{
    use Exportable;

    protected $stats;
    protected $transactions;
    protected $filters;

    public function __construct($stats, $transactions, $filters)
    {
        $this->stats = $stats;
        $this->transactions = $transactions;
        $this->filters = $filters;
    }

    public function sheets(): array
    {
        return [
            new SummarySheet($this->stats, $this->filters),
            new TransactionsSheet($this->transactions),
        ];
    }
}