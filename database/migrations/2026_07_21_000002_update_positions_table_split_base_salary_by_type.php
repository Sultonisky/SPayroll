<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * - Remove single `base_salary` column
     * - Add `base_salary_fulltime` and `base_salary_internship`
     *   (fulltime salary is expected to be larger than internship salary)
     */
    public function up(): void
    {
        Schema::table('positions', function (Blueprint $table) {
            // Drop the old generic base_salary index and column
            $table->dropIndex(['base_salary']);
            $table->dropColumn('base_salary');

            // Add separate salary columns per employee type
            $table->decimal('base_salary_fulltime', 15, 2)
                  ->nullable()
                  ->after('description')
                  ->comment('Base salary for fulltime employees (higher)');

            $table->decimal('base_salary_internship', 15, 2)
                  ->nullable()
                  ->after('base_salary_fulltime')
                  ->comment('Base salary for internship employees (lower)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('positions', function (Blueprint $table) {
            $table->dropColumn(['base_salary_fulltime', 'base_salary_internship']);

            $table->decimal('base_salary', 15, 2)
                  ->nullable()
                  ->after('description');

            $table->index('base_salary');
        });
    }
};
