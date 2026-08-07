<?php

namespace Tests\Feature\Commands;

use App\Models\AuditLog;
use App\Models\Bonus;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Payroll;
use App\Models\Position;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Feature tests for app:cleanup-trash Artisan command.
 *
 * Covers:
 *  - Old soft-deleted records are permanently deleted (per model)
 *  - Recently deleted records are kept
 *  - Old audit log entries are pruged
 *  - Recent audit log entries are kept
 *  - --logs-only and --trash-only flags work correctly
 *  - Custom --trash-days and --log-days options are respected
 *  - Summary output is shown
 */
class CleanUpTrashCommandTest extends TestCase
{
    use RefreshDatabase;

    // -----------------------------------------------------------------------
    // Helpers
    // -----------------------------------------------------------------------

    /** Soft-delete a model and backdate deleted_at by $days. */
    private function softDeleteOld(object $model, int $days = 91): void
    {
        $model->delete();
        $model::withTrashed()->where('id', $model->id)->update([
            'deleted_at' => now()->subDays($days),
        ]);
    }

    /** Create an AuditLog entry backdated by $days. */
    private function oldLog(int $days): AuditLog
    {
        return AuditLog::factory()->create([
            'created_at' => now()->subDays($days),
        ]);
    }

    // -----------------------------------------------------------------------
    // Basic run
    // -----------------------------------------------------------------------

    public function test_command_exists_and_runs_successfully(): void
    {
        $this->artisan('app:cleanup-trash')->assertSuccessful();
    }

    public function test_command_outputs_summary(): void
    {
        $this->artisan('app:cleanup-trash')
            ->expectsOutputToContain('Cleanup complete')
            ->assertSuccessful();
    }

    public function test_command_handles_empty_state_gracefully(): void
    {
        $this->artisan('app:cleanup-trash')
            ->assertSuccessful();
    }

    // -----------------------------------------------------------------------
    // Trash cleanup — soft-delete models
    // -----------------------------------------------------------------------

    public function test_permanently_deletes_old_trashed_users(): void
    {
        $old = User::factory()->create();
        $this->softDeleteOld($old, 91);

        $this->artisan('app:cleanup-trash')->assertSuccessful();

        $this->assertDatabaseMissing('users', ['id' => $old->id]);
    }

    public function test_keeps_recently_trashed_users(): void
    {
        $recent = User::factory()->create();
        $recent->delete(); // deleted_at = now()

        $this->artisan('app:cleanup-trash')->assertSuccessful();

        $this->assertDatabaseHas('users', ['id' => $recent->id]);
    }

    public function test_permanently_deletes_old_trashed_departments(): void
    {
        $dept = Department::factory()->create();
        $this->softDeleteOld($dept, 91);

        $this->artisan('app:cleanup-trash')->assertSuccessful();

        $this->assertDatabaseMissing('departments', ['id' => $dept->id]);
    }

    public function test_permanently_deletes_old_trashed_positions(): void
    {
        $position = Position::factory()->create();
        $this->softDeleteOld($position, 91);

        $this->artisan('app:cleanup-trash')->assertSuccessful();

        $this->assertDatabaseMissing('positions', ['id' => $position->id]);
    }

    public function test_permanently_deletes_old_trashed_employees(): void
    {
        $employee = Employee::factory()->create();
        $this->softDeleteOld($employee, 91);

        $this->artisan('app:cleanup-trash')->assertSuccessful();

        $this->assertDatabaseMissing('employees', ['id' => $employee->id]);
    }

    public function test_permanently_deletes_old_trashed_payrolls(): void
    {
        $payroll = Payroll::factory()->draft()->create();
        $this->softDeleteOld($payroll, 91);

        $this->artisan('app:cleanup-trash')->assertSuccessful();

        $this->assertDatabaseMissing('payrolls', ['id' => $payroll->id]);
    }

    public function test_permanently_deletes_old_trashed_bonuses(): void
    {
        $bonus = Bonus::factory()->pending()->create();
        $this->softDeleteOld($bonus, 91);

        $this->artisan('app:cleanup-trash')->assertSuccessful();

        $this->assertDatabaseMissing('bonuses', ['id' => $bonus->id]);
    }

    public function test_respects_custom_trash_days_option(): void
    {
        // Deleted 30 days ago — normally kept (< 90d), but should be removed with --trash-days=20
        $user = User::factory()->create();
        $this->softDeleteOld($user, 30);

        $this->artisan('app:cleanup-trash', ['--trash-days' => 20])->assertSuccessful();

        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }

    public function test_keeps_records_within_custom_trash_days(): void
    {
        // Deleted 10 days ago — should NOT be removed with --trash-days=20
        $user = User::factory()->create();
        $this->softDeleteOld($user, 10);

        $this->artisan('app:cleanup-trash', ['--trash-days' => 20])->assertSuccessful();

        $this->assertDatabaseHas('users', ['id' => $user->id]);
    }

    // -----------------------------------------------------------------------
    // Audit log pruning
    // -----------------------------------------------------------------------

    public function test_purges_old_audit_logs(): void
    {
        $old = $this->oldLog(366);

        $this->artisan('app:cleanup-trash')->assertSuccessful();

        $this->assertDatabaseMissing('audit_logs', ['id' => $old->id]);
    }

    public function test_keeps_recent_audit_logs(): void
    {
        $recent = $this->oldLog(30);

        $this->artisan('app:cleanup-trash')->assertSuccessful();

        $this->assertDatabaseHas('audit_logs', ['id' => $recent->id]);
    }

    public function test_keeps_logs_exactly_at_retention_boundary(): void
    {
        // 365 days old exactly — not older than 365, so should be kept
        $boundary = $this->oldLog(365);

        $this->artisan('app:cleanup-trash')->assertSuccessful();

        $this->assertDatabaseHas('audit_logs', ['id' => $boundary->id]);
    }

    public function test_respects_custom_log_days_option(): void
    {
        // Log from 40 days ago — kept by default (365d), removed with --log-days=30
        $log = $this->oldLog(40);

        $this->artisan('app:cleanup-trash', ['--log-days' => 30])->assertSuccessful();

        $this->assertDatabaseMissing('audit_logs', ['id' => $log->id]);
    }

    public function test_keeps_logs_within_custom_log_days(): void
    {
        // Log from 10 days ago — should NOT be removed with --log-days=30
        $log = $this->oldLog(10);

        $this->artisan('app:cleanup-trash', ['--log-days' => 30])->assertSuccessful();

        $this->assertDatabaseHas('audit_logs', ['id' => $log->id]);
    }

    // -----------------------------------------------------------------------
    // --logs-only flag
    // -----------------------------------------------------------------------

    public function test_logs_only_skips_trash_cleanup(): void
    {
        $user = User::factory()->create();
        $this->softDeleteOld($user, 91);

        $old = $this->oldLog(366);

        $this->artisan('app:cleanup-trash', ['--logs-only' => true])->assertSuccessful();

        // Old user should still be in trash (trash cleanup was skipped)
        $this->assertDatabaseHas('users', ['id' => $user->id]);

        // Old audit log should be gone
        $this->assertDatabaseMissing('audit_logs', ['id' => $old->id]);
    }

    // -----------------------------------------------------------------------
    // --trash-only flag
    // -----------------------------------------------------------------------

    public function test_trash_only_skips_audit_log_pruning(): void
    {
        $user = User::factory()->create();
        $this->softDeleteOld($user, 91);

        $old = $this->oldLog(366);

        $this->artisan('app:cleanup-trash', ['--trash-only' => true])->assertSuccessful();

        // Old user should be permanently deleted
        $this->assertDatabaseMissing('users', ['id' => $user->id]);

        // Old audit log should still be present (log pruning was skipped)
        $this->assertDatabaseHas('audit_logs', ['id' => $old->id]);
    }
}
