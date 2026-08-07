<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\Bonus;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Payroll;
use App\Models\Position;
use App\Models\User;
use App\Observers\BonusObserver;
use App\Observers\DepartmentObserver;
use App\Observers\EmployeeObserver;
use App\Observers\PayrollObserver;
use App\Observers\PositionObserver;
use App\Observers\UserObserver;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Integration tests verifying each observer correctly writes audit log entries.
 *
 * The base TestCase flushes all observers — we explicitly re-register the one
 * under test in setUp() to get clean, isolated coverage.
 */
class AuditLogObserverIntegrationTest extends TestCase
{
    use RefreshDatabase;

    // -----------------------------------------------------------------------
    // UserObserver
    // -----------------------------------------------------------------------

    protected function setUpUserObserver(): void
    {
        User::observe(UserObserver::class);
    }

    public function test_user_created_writes_audit_log(): void
    {
        $this->setUpUserObserver();
        $actor = $this->adminUser();
        $this->actingAs($actor);

        $user = User::factory()->create();

        $this->assertDatabaseHas('audit_logs', [
            'user_id'        => $actor->id,
            'action'         => 'created',
            'auditable_type' => User::class,
            'auditable_id'   => $user->id,
        ]);
    }

    public function test_user_updated_writes_audit_log(): void
    {
        $this->setUpUserObserver();
        $actor = $this->adminUser();
        $this->actingAs($actor);

        $user = User::factory()->create(['name' => 'Old Name']);
        AuditLog::query()->delete(); // clear create log

        User::observe(UserObserver::class); // re-register after clear
        $user->update(['name' => 'New Name']);

        $this->assertDatabaseHas('audit_logs', [
            'action'         => 'updated',
            'auditable_type' => User::class,
            'auditable_id'   => $user->id,
        ]);
    }

    public function test_user_deleted_writes_audit_log(): void
    {
        $this->setUpUserObserver();
        $actor = $this->adminUser();
        $this->actingAs($actor);

        $user = User::factory()->create();
        AuditLog::query()->delete();

        User::observe(UserObserver::class);
        $user->delete();

        $this->assertDatabaseHas('audit_logs', [
            'action'         => 'deleted',
            'auditable_type' => User::class,
            'auditable_id'   => $user->id,
        ]);
    }

    public function test_user_restored_writes_audit_log(): void
    {
        $this->setUpUserObserver();
        $actor = $this->adminUser();
        $this->actingAs($actor);

        $user = User::factory()->create();
        $user->delete();
        AuditLog::query()->delete();

        User::observe(UserObserver::class);
        $user->restore();

        $this->assertDatabaseHas('audit_logs', [
            'action'         => 'restored',
            'auditable_type' => User::class,
            'auditable_id'   => $user->id,
        ]);
    }

    public function test_user_force_deleted_writes_audit_log(): void
    {
        $this->setUpUserObserver();
        $actor = $this->adminUser();
        $this->actingAs($actor);

        $user = User::factory()->create();
        $userId = $user->id;
        $user->delete();
        AuditLog::query()->delete();

        User::observe(UserObserver::class);
        $user->forceDelete();

        $this->assertDatabaseHas('audit_logs', [
            'action'         => 'force_deleted',
            'auditable_type' => User::class,
            'auditable_id'   => $userId,
        ]);
    }

    // -----------------------------------------------------------------------
    // DepartmentObserver
    // -----------------------------------------------------------------------

    public function test_department_created_writes_audit_log(): void
    {
        Department::observe(DepartmentObserver::class);
        $actor = $this->adminUser();
        $this->actingAs($actor);

        $dept = Department::factory()->create();

        $this->assertDatabaseHas('audit_logs', [
            'action'         => 'created',
            'auditable_type' => Department::class,
            'auditable_id'   => $dept->id,
        ]);
    }

    public function test_department_updated_writes_audit_log(): void
    {
        $actor = $this->adminUser();
        $this->actingAs($actor);

        $dept = Department::factory()->create();
        AuditLog::query()->delete();

        Department::observe(DepartmentObserver::class);
        $dept->update(['name' => 'Renamed Dept']);

        $this->assertDatabaseHas('audit_logs', [
            'action'         => 'updated',
            'auditable_type' => Department::class,
            'auditable_id'   => $dept->id,
        ]);
    }

    public function test_department_deleted_writes_audit_log(): void
    {
        $actor = $this->adminUser();
        $this->actingAs($actor);

        $dept = Department::factory()->create();
        AuditLog::query()->delete();

        Department::observe(DepartmentObserver::class);
        $dept->delete();

        $this->assertDatabaseHas('audit_logs', [
            'action'         => 'deleted',
            'auditable_type' => Department::class,
            'auditable_id'   => $dept->id,
        ]);
    }

    // -----------------------------------------------------------------------
    // PositionObserver
    // -----------------------------------------------------------------------

    public function test_position_created_writes_audit_log(): void
    {
        Position::observe(PositionObserver::class);
        $actor = $this->adminUser();
        $this->actingAs($actor);

        $position = Position::factory()->create();

        $this->assertDatabaseHas('audit_logs', [
            'action'         => 'created',
            'auditable_type' => Position::class,
            'auditable_id'   => $position->id,
        ]);
    }

    // -----------------------------------------------------------------------
    // EmployeeObserver
    // -----------------------------------------------------------------------

    public function test_employee_created_writes_audit_log(): void
    {
        Employee::observe(EmployeeObserver::class);
        $actor = $this->adminUser();
        $this->actingAs($actor);

        $employee = Employee::factory()->create();

        $this->assertDatabaseHas('audit_logs', [
            'action'         => 'created',
            'auditable_type' => Employee::class,
            'auditable_id'   => $employee->id,
        ]);
    }

    public function test_employee_updated_writes_audit_log(): void
    {
        $actor = $this->adminUser();
        $this->actingAs($actor);

        $employee = Employee::factory()->create();
        AuditLog::query()->delete();

        Employee::observe(EmployeeObserver::class);
        $employee->update(['name' => 'Updated Employee Name']);

        $this->assertDatabaseHas('audit_logs', [
            'action'         => 'updated',
            'auditable_type' => Employee::class,
            'auditable_id'   => $employee->id,
        ]);
    }

    public function test_employee_deleted_writes_audit_log(): void
    {
        $actor = $this->adminUser();
        $this->actingAs($actor);

        $employee = Employee::factory()->create();
        AuditLog::query()->delete();

        Employee::observe(EmployeeObserver::class);
        $employee->delete();

        $this->assertDatabaseHas('audit_logs', [
            'action'         => 'deleted',
            'auditable_type' => Employee::class,
            'auditable_id'   => $employee->id,
        ]);
    }

    // -----------------------------------------------------------------------
    // BonusObserver — special status-based actions
    // -----------------------------------------------------------------------

    public function test_bonus_created_writes_audit_log(): void
    {
        Bonus::observe(BonusObserver::class);
        $actor = $this->adminUser();
        $this->actingAs($actor);

        $bonus = Bonus::factory()->pending()->create();

        $this->assertDatabaseHas('audit_logs', [
            'action'         => 'created',
            'auditable_type' => Bonus::class,
            'auditable_id'   => $bonus->id,
        ]);
    }

    public function test_bonus_approved_writes_approved_action(): void
    {
        $actor = $this->adminUser();
        $this->actingAs($actor);

        $bonus = Bonus::factory()->pending()->create();
        AuditLog::query()->delete();

        Bonus::observe(BonusObserver::class);
        $bonus->update([
            'status'      => 'approved',
            'approved_by' => $actor->id,
            'approved_at' => now(),
        ]);

        $this->assertDatabaseHas('audit_logs', [
            'action'         => 'approved',
            'auditable_type' => Bonus::class,
            'auditable_id'   => $bonus->id,
        ]);
    }

    public function test_bonus_rejected_writes_rejected_action(): void
    {
        $actor = $this->adminUser();
        $this->actingAs($actor);

        $bonus = Bonus::factory()->pending()->create();
        AuditLog::query()->delete();

        Bonus::observe(BonusObserver::class);
        $bonus->update([
            'status' => 'rejected',
            'notes'  => 'Budget exceeded',
        ]);

        $this->assertDatabaseHas('audit_logs', [
            'action'         => 'rejected',
            'auditable_type' => Bonus::class,
            'auditable_id'   => $bonus->id,
        ]);
    }

    // -----------------------------------------------------------------------
    // PayrollObserver — special status-based actions
    // -----------------------------------------------------------------------

    public function test_payroll_created_writes_audit_log(): void
    {
        Payroll::observe(PayrollObserver::class);
        $actor = $this->adminUser();
        $this->actingAs($actor);

        $payroll = Payroll::factory()->create();

        $this->assertDatabaseHas('audit_logs', [
            'action'         => 'created',
            'auditable_type' => Payroll::class,
            'auditable_id'   => $payroll->id,
        ]);
    }

    public function test_payroll_approved_writes_approved_action(): void
    {
        $actor = $this->adminUser();
        $this->actingAs($actor);

        $payroll = Payroll::factory()->draft()->create();
        AuditLog::query()->delete();

        Payroll::observe(PayrollObserver::class);
        $payroll->update(['status' => 'approved']);

        $this->assertDatabaseHas('audit_logs', [
            'action'         => 'approved',
            'auditable_type' => Payroll::class,
            'auditable_id'   => $payroll->id,
        ]);
    }

    public function test_payroll_mark_paid_writes_mark_paid_action(): void
    {
        $actor = $this->adminUser();
        $this->actingAs($actor);

        $payroll = Payroll::factory()->approved()->create();
        AuditLog::query()->delete();

        Payroll::observe(PayrollObserver::class);
        $payroll->update(['status' => 'paid']);

        $this->assertDatabaseHas('audit_logs', [
            'action'         => 'mark_paid',
            'auditable_type' => Payroll::class,
            'auditable_id'   => $payroll->id,
        ]);
    }

    public function test_payroll_deleted_writes_audit_log(): void
    {
        $actor = $this->adminUser();
        $this->actingAs($actor);

        $payroll = Payroll::factory()->create();
        AuditLog::query()->delete();

        Payroll::observe(PayrollObserver::class);
        $payroll->delete();

        $this->assertDatabaseHas('audit_logs', [
            'action'         => 'deleted',
            'auditable_type' => Payroll::class,
            'auditable_id'   => $payroll->id,
        ]);
    }

    // -----------------------------------------------------------------------
    // IP address is always captured
    // -----------------------------------------------------------------------

    public function test_observer_log_includes_ip_address(): void
    {
        Department::observe(DepartmentObserver::class);
        $this->actingAs($this->adminUser());

        $dept = Department::factory()->create();

        $log = AuditLog::where('auditable_type', Department::class)
            ->where('auditable_id', $dept->id)
            ->first();

        $this->assertNotNull($log);
        $this->assertNotNull($log->ip_address);
    }

    // -----------------------------------------------------------------------
    // Updated — old/new values captured correctly
    // -----------------------------------------------------------------------

    public function test_updated_log_captures_old_and_new_values(): void
    {
        $actor = $this->adminUser();
        $this->actingAs($actor);

        $dept = Department::factory()->create(['name' => 'Before Name']);
        AuditLog::query()->delete();

        Department::observe(DepartmentObserver::class);
        $dept->update(['name' => 'After Name']);

        $log = AuditLog::where('action', 'updated')
            ->where('auditable_type', Department::class)
            ->first();

        $this->assertNotNull($log->old_values);
        $this->assertNotNull($log->new_values);
        $this->assertSame('Before Name', $log->old_values['name']);
        $this->assertSame('After Name', $log->new_values['name']);
    }
}
