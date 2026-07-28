<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * SQLite does not support modifying enum columns, so we use a string check workaround.
     * For MySQL/PostgreSQL, we alter the column directly.
     */
    public function up(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'sqlite') {
            // SQLite: enum is stored as TEXT with no native type enforcement,
            // so no schema change is needed — just document the new allowed value.
            // Application-level validation and model casting will enforce the constraint.
        } else {
            Schema::table('users', function (Blueprint $table) {
                $table->enum('role', ['admin', 'HR', 'manager', 'staff', 'finance'])
                    ->default('staff')
                    ->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $driver = DB::getDriverName();

        if ($driver !== 'sqlite') {
            Schema::table('users', function (Blueprint $table) {
                $table->enum('role', ['admin', 'HR', 'manager', 'staff'])
                    ->default('staff')
                    ->change();
            });
        }
    }
};
