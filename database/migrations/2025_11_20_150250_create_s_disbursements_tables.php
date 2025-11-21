<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tabel Induk: Mencatat tujuan dan total penyaluran
        Schema::create('s_disbursements', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('purpose'); // Tujuan (misal: Uang Orang Sakit)
            $table->decimal('total_amount', 15, 2); // Total yang disalurkan
            $table->date('disbursement_date');
            $table->text('notes')->nullable();
            
            $table->foreignUuid('created_by')->nullable()->constrained('core_users')->onDelete('set null');
            $table->timestamps();
        });

        // 2. Tabel Detail: Mencatat uang diambil dari kelas mana saja
        Schema::create('s_fund_allocations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            
            $table->foreignUuid('disbursement_id')->constrained('s_disbursements')->onDelete('cascade');
            $table->foreignUuid('class_id')->constrained('ref_classes')->onDelete('cascade');
            
            $table->decimal('amount_transferred', 15, 2); // Jumlah yang diambil dari kelas ini
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('s_fund_allocations');
        Schema::dropIfExists('s_disbursements');
    }
};