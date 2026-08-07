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
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();

            // Who did the action (nullable for system/unauthenticated events)
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();

            // What action was performed
            $table->string('action', 50); // e.g. created, updated, deleted, restored, force_deleted, login, logout, login_failed, export

            // Which model was affected (polymorphic-style, no foreign key constraint)
            $table->string('auditable_type')->nullable(); // e.g. App\Models\Employee
            $table->unsignedBigInteger('auditable_id')->nullable(); // e.g. 42

            // Human-readable description
            $table->string('description')->nullable();

            // Data snapshots (JSON)
            $table->json('old_values')->nullable(); // state before change
            $table->json('new_values')->nullable(); // state after change

            // Network / device context (critical for remote-first)
            $table->string('ip_address', 45)->nullable(); // supports IPv4 and IPv6
            $table->text('user_agent')->nullable();

            // URL that triggered the action
            $table->string('url')->nullable();
            $table->string('method', 10)->nullable(); // GET, POST, PUT, DELETE, etc.

            $table->timestamp('created_at')->useCurrent();

            // Indexes for common query patterns
            $table->index('user_id');
            $table->index('action');
            $table->index(['auditable_type', 'auditable_id']);
            $table->index('ip_address');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
