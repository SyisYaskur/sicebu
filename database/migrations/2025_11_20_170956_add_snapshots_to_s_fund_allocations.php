<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    Schema::table('s_fund_allocations', function (Blueprint $table) {
        $table->decimal('balance_before', 15, 2)->nullable()->after('amount_transferred');
        $table->decimal('balance_after', 15, 2)->nullable()->after('balance_before');
    });
}

public function down(): void
{
    Schema::table('s_fund_allocations', function (Blueprint $table) {
        $table->dropColumn(['balance_before', 'balance_after']);
    });
}
};
