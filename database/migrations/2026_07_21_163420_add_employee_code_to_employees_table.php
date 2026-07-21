<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Add `employee_code` — a unique, human-readable code for each employee
     * (e.g. EMP-0001). Placed before `nik`.
     */
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->string('employee_code')->unique()->nullable()->after('position_id');
            $table->index('employee_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropIndex(['employee_code']);
            $table->dropColumn('employee_code');
        });
    }
};
