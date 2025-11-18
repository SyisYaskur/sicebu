<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('s_class_incomes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('class_id')->constrained('ref_classes')->onDelete('cascade');
            $table->date('date'); // Tanggal pemasukan
            $table->decimal('amount', 15, 2); // Jumlah uang terkumpul
            $table->string('description')->nullable(); // Keterangan/Catatan

            $table->foreignUuid('created_by')->nullable()->constrained('core_users')->onDelete('set null');
            $table->foreignUuid('updated_by')->nullable()->constrained('core_users')->onDelete('set null');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('s_class_incomes');
    }
};
