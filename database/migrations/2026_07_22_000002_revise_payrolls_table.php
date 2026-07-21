<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Simplify the payrolls table to:
     *   total_salary = base_salary + bonus
     *
     * Removed columns: allowances, overtime_pay, deductions
     * These are intentionally excluded — this payroll system is designed
     * for remote-first companies where compensation = fixed salary + bonus only.
     */
    public function up(): void
    {
        Schema::table('payrolls', function (Blueprint $table) {
            $table->dropColumn(['allowances', 'overtime_pay', 'deductions']);
        });
    }

    public function down(): void
    {
        Schema::table('payrolls', function (Blueprint $table) {
            $table->decimal('allowances', 15, 2)->default(0)->after('base_salary');
            $table->decimal('overtime_pay', 15, 2)->default(0)->after('bonus');
            $table->decimal('deductions', 15, 2)->default(0)->after('overtime_pay');
        });
    }
};
