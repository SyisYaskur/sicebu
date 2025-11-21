<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   public function up(): void
{
    Schema::table('s_fund_allocations', function (Blueprint $table) {
        // Hubungkan alokasi ini dengan pengeluaran spesifik di buku kas kelas
        $table->foreignUuid('class_expense_id')->nullable()->after('class_id')->constrained('s_class_expenses')->onDelete('cascade');
    });
}

public function down(): void
{
    Schema::table('s_fund_allocations', function (Blueprint $table) {
        $table->dropForeign(['class_expense_id']);
        $table->dropColumn('class_expense_id');
    });
}
};
