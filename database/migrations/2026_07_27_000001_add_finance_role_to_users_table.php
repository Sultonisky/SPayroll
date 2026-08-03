<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add 'finance' to the role enum.
     *
     * MySQL/PostgreSQL : ALTER COLUMN directly.
     * SQLite           : enum is stored as TEXT with no native enforcement,
     *                    so no DDL change is needed — the new value is already
     *                    accepted at the database level. Application-level
     *                    validation (controller rules + RoleMiddleware) enforces
     *                    the allowed set.
     */
    public function up(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            // Nothing to do — SQLite has no enum type to migrate.
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['admin', 'HR', 'manager', 'staff', 'finance'])
                ->default('staff')
                ->change();
        });
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['admin', 'HR', 'manager', 'staff'])
                ->default('staff')
                ->change();
        });
    }
};
