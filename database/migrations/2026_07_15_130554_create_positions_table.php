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
        Schema::create('positions', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Nama jabatan (Staff, Supervisor, Manager)
            $table->text('description')->nullable(); // Deskripsi jabatan (opsional)
            $table->decimal('base_salary', 15, 2)->nullable(); // Gaji pokok dasar untuk jabatan ini (opsional, untuk referensi)
            $table->timestamps();
            $table->softDeletes();

            $table->index('name');
            $table->index('base_salary');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('positions');
    }
};
