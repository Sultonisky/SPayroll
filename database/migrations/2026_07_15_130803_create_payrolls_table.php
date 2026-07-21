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
        Schema::create('payrolls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            // $table->foreignId('attendance_id')->nullable()->constrained()->nullOnDelete(); // TEMPORARILY DISABLED - attendance feature not yet needed
            $table->year('year'); // Tahun penggajian
            $table->tinyInteger('month'); // Bulan penggajian (1-12)
            $table->date('pay_date'); // Tanggal pembayaran gaji
            $table->decimal('base_salary', 15, 2); // Gaji pokok
            $table->decimal('allowances', 15, 2)->default(0); // Total tunjangan
            $table->decimal('bonus', 15, 2)->default(0); // Bonus
            $table->decimal('overtime_pay', 15, 2)->default(0); // Gaji lembur
            $table->decimal('deductions', 15, 2)->default(0); // Total potongan
            $table->decimal('total_salary', 15, 2); // Total gaji yang diterima
            $table->text('notes')->nullable(); // Catatan tambahan (opsional)
            $table->enum('status', ['draft', 'approved', 'paid'])->default('draft'); // Status penggajian
            $table->timestamps();
            $table->softDeletes();
            // Unique constraint untuk menghindari duplikat penggajian per karyawan per bulan
            $table->unique(['employee_id', 'year', 'month']);

            $table->index('year');
            $table->index('month');
            $table->index('status');
            $table->index('pay_date');
            $table->index(['year', 'month']);
            $table->index(['year', 'month', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payrolls');
    }
};
