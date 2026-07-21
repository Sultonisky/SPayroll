<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * - Rename column `status` → `employee_status`
     * - Add column `employee_type` (fulltime | internship)
     */
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            // Rename status → employee_status
            $table->renameColumn('status', 'employee_status');
        });

        Schema::table('employees', function (Blueprint $table) {
            // Add employee_type after employee_status
            $table->enum('employee_type', ['fulltime', 'internship'])
                  ->default('fulltime')
                  ->after('employee_status');

            $table->index('employee_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropIndex(['employee_type']);
            $table->dropColumn('employee_type');
        });

        Schema::table('employees', function (Blueprint $table) {
            $table->renameColumn('employee_status', 'status');
        });
    }
};
