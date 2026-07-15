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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete(); // Relasi ke user (untuk login karyawan, opsional)
            $table->foreignId('department_id')->constrained()->cascadeOnDelete();
            $table->foreignId('position_id')->constrained()->cascadeOnDelete();
            $table->string('nik')->unique(); // Nomor Induk Karyawan (unique)
            $table->string('name'); // Nama lengkap karyawan
            $table->string('email')->unique(); // Email karyawan
            $table->string('phone'); // No HP
            $table->string('address')->nullable(); // Alamat (opsional)
            $table->date('join_date'); // Tanggal masuk
            $table->date('birth_date')->nullable(); // Tanggal lahir (opsional)
            $table->enum('status', ['active', 'inactive', 'resigned'])->default('active'); // Status karyawan
            $table->decimal('base_salary', 15, 2); // Gaji pokok karyawan
            $table->timestamps();
            $table->softDeletes();

            $table->index('department_id');
            $table->index('position_id');
            $table->index('status');
            $table->index('join_date');
            $table->index('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
