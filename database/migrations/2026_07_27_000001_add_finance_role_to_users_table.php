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
     * MySQL/PostgreSQL: alter column directly.
     * SQLite: does not support ALTER COLUMN, so we recreate the table
     *         (standard SQLite migration pattern).
     */
    public function up(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'sqlite') {
            $this->rebuildUsersTableSqlite(withFinance: true);
        } else {
            Schema::table('users', function (Blueprint $table) {
                $table->enum('role', ['admin', 'HR', 'manager', 'staff', 'finance'])
                    ->default('staff')
                    ->change();
            });
        }
    }

    public function down(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'sqlite') {
            $this->rebuildUsersTableSqlite(withFinance: false);
        } else {
            Schema::table('users', function (Blueprint $table) {
                $table->enum('role', ['admin', 'HR', 'manager', 'staff'])
                    ->default('staff')
                    ->change();
            });
        }
    }

    // -----------------------------------------------------------------------
    // SQLite helper: recreate users table with updated role CHECK constraint
    // -----------------------------------------------------------------------

    private function rebuildUsersTableSqlite(bool $withFinance): void
    {
        $roles = $withFinance
            ? "('admin', 'HR', 'manager', 'staff', 'finance')"
            : "('admin', 'HR', 'manager', 'staff')";

        // 1. Create temporary copy with updated CHECK
        DB::statement("
            CREATE TABLE users_new (
                id            INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
                name          VARCHAR(255)  NOT NULL,
                email         VARCHAR(255)  NOT NULL UNIQUE,
                email_verified_at DATETIME  NULL,
                password      VARCHAR(255)  NOT NULL,
                role          VARCHAR(255)  NOT NULL DEFAULT 'staff'
                                  CHECK (role IN {$roles}),
                foto          VARCHAR(255)  NULL,
                is_demo       TINYINT(1)    NOT NULL DEFAULT 0,
                remember_token VARCHAR(100) NULL,
                created_at    DATETIME      NULL,
                updated_at    DATETIME      NULL,
                deleted_at    DATETIME      NULL
            )
        ");

        // 2. Copy existing data
        DB::statement('INSERT INTO users_new SELECT * FROM users');

        // 3. Swap tables
        DB::statement('DROP TABLE users');
        DB::statement('ALTER TABLE users_new RENAME TO users');

        // 4. Recreate indexes that were on the original table
        DB::statement('CREATE INDEX users_email_index ON users (email)');
        DB::statement('CREATE INDEX users_deleted_at_index ON users (deleted_at)');
    }
};
