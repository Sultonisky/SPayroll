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
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->year('year'); // Tahun absensi
            $table->tinyInteger('month'); // Bulan absensi (1-12)
            $table->integer('work_days'); // Jumlah hari kerja dalam bulan
            $table->integer('present')->default(0); // Jumlah hari hadir
            $table->integer('sick')->default(0); // Jumlah hari sakit
            $table->integer('leave')->default(0); // Jumlah hari izin
            $table->integer('alpha')->default(0); // Jumlah hari tanpa keterangan (alpha)
            $table->integer('overtime_hours')->default(0); // Jumlah jam lembur
            $table->text('notes')->nullable(); // Catatan tambahan (opsional)
            $table->timestamps();
            $table->softDeletes();
            // Unique constraint untuk menghindari duplikat absensi per karyawan per bulan
            $table->unique(['employee_id', 'year', 'month']);

            $table->index('year');
            $table->index('month');
            $table->index(['year', 'month']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
