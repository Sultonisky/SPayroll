<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Bonuses are general, ad-hoc additions to an employee's salary for a given period.
     * The `type` field is a free-form label (e.g. "Performance", "Project Completion",
     * "Referral") so the system is not opinionated about what counts as a bonus.
     *
     * A bonus must be approved before it is included in payroll calculation.
     */
    public function up(): void
    {
        Schema::create('bonuses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->unsignedSmallInteger('year');
            $table->unsignedTinyInteger('month');   // 1–12
            $table->string('type');                  // free-form label
            $table->text('description')->nullable(); // optional detail
            $table->decimal('amount', 15, 2);
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->text('notes')->nullable();       // reviewer notes
            $table->timestamps();
            $table->softDeletes();

            $table->index(['employee_id', 'year', 'month']);
            $table->index('status');
            $table->index(['year', 'month']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bonuses');
    }
};
