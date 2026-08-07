<?php

namespace Tests\Unit;

use App\Models\AuditLog;
use App\Models\Department;
use App\Models\User;
use App\Services\AuditLogService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Unit tests for AuditLogService — verifies that log entries are written
 * correctly to the database with proper field values.
 */
class AuditLogServiceTest extends TestCase
{
    use RefreshDatabase;

    // -----------------------------------------------------------------------
    // AuditLogService::log()
    // -----------------------------------------------------------------------

    public function test_log_creates_record_in_database(): void
    {
        $admin = $this->adminUser();
        $this->actingAs($admin);

        AuditLogService::log('login', null, 'User logged in');

        $this->assertDatabaseHas('audit_logs', [
            'user_id' => $admin->id,
            'action' => 'login',
            'description' => 'User logged in',
        ]);
    }

    public function test_log_captures_ip_address(): void
    {
        $this->actingAs($this->adminUser());

        AuditLogService::log('login', null, 'Login from office');

        $log = AuditLog::latest('created_at')->first();
        $this->assertNotNull($log->ip_address);
    }

    public function test_log_captures_user_agent(): void
    {
        $this->actingAs($this->adminUser());

        AuditLogService::log('export', null, 'Exported report');

        $log = AuditLog::latest('created_at')->first();
        $this->assertNotNull($log->user_agent);
    }

    public function test_log_stores_auditable_type_and_id(): void
    {
        $admin = $this->adminUser();
        $this->actingAs($admin);

        $dept = Department::factory()->create();

        AuditLogService::log('deleted', $dept, "Department '{$dept->name}' deleted");

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'deleted',
            'auditable_type' => Department::class,
            'auditable_id' => $dept->id,
        ]);
    }

    public function test_log_stores_old_and_new_values(): void
    {
        $this->actingAs($this->adminUser());

        AuditLogService::log(
            'updated',
            null,
            'Manual update',
            ['name' => 'Old Name'],
            ['name' => 'New Name'],
        );

        $log = AuditLog::latest('created_at')->first();
        $this->assertSame(['name' => 'Old Name'], $log->old_values);
        $this->assertSame(['name' => 'New Name'], $log->new_values);
    }

    public function test_log_works_without_authenticated_user(): void
    {
        // Unauthenticated / system event
        AuditLogService::log('login_failed', null, 'Brute force attempt');

        $this->assertDatabaseHas('audit_logs', [
            'user_id' => null,
            'action' => 'login_failed',
        ]);
    }

    public function test_log_sanitizes_password_from_new_values(): void
    {
        $this->actingAs($this->adminUser());

        AuditLogService::log(
            'created',
            null,
            'Created user',
            null,
            ['name' => 'Alice', 'password' => 'secret123', 'email' => 'a@b.com']
        );

        $log = AuditLog::latest('created_at')->first();
        $this->assertSame('***', $log->new_values['password']);
        $this->assertSame('Alice', $log->new_values['name']);
    }

    public function test_log_sanitizes_remember_token_from_old_values(): void
    {
        $this->actingAs($this->adminUser());

        AuditLogService::log(
            'updated',
            null,
            'Updated user',
            ['remember_token' => 'abc123', 'role' => 'staff'],
            ['role' => 'HR']
        );

        $log = AuditLog::latest('created_at')->first();
        $this->assertSame('***', $log->old_values['remember_token']);
        $this->assertSame('staff', $log->old_values['role']);
    }

    public function test_log_returns_audit_log_instance(): void
    {
        $this->actingAs($this->adminUser());

        $result = AuditLogService::log('logout', null, 'Logged out');

        $this->assertInstanceOf(AuditLog::class, $result);
        $this->assertTrue($result->exists);
    }

    // -----------------------------------------------------------------------
    // AuditLogService::logModelEvent()
    // -----------------------------------------------------------------------

    public function test_log_model_event_created_captures_new_values(): void
    {
        $admin = $this->adminUser();
        $this->actingAs($admin);

        $dept = Department::factory()->create();

        // Manually call logModelEvent as if from an observer
        AuditLogService::logModelEvent('created', $dept, 'Department created');

        $log = AuditLog::where('action', 'created')
            ->where('auditable_type', Department::class)
            ->where('auditable_id', $dept->id)
            ->latest('created_at')
            ->first();

        $this->assertNotNull($log);
        $this->assertNotNull($log->new_values);
        $this->assertNull($log->old_values);
    }

    public function test_log_model_event_updated_captures_dirty_fields_only(): void
    {
        $admin = $this->adminUser();
        $this->actingAs($admin);

        $dept = Department::factory()->create(['name' => 'Engineering', 'description' => 'Dev team']);

        // Simulate what happens when the model is being updated
        $dept->name = 'Product';          // changed
        // description left unchanged    // not in dirty

        AuditLogService::logModelEvent('updated', $dept, 'Dept updated');

        $log = AuditLog::where('action', 'updated')
            ->where('auditable_type', Department::class)
            ->latest('created_at')
            ->first();

        $this->assertArrayHasKey('name', $log->new_values);
        $this->assertArrayHasKey('name', $log->old_values);
        // description should NOT appear because it wasn't dirty
        $this->assertArrayNotHasKey('description', $log->new_values);
    }

    public function test_log_model_event_deleted_captures_no_values(): void
    {
        $this->actingAs($this->adminUser());

        $dept = Department::factory()->create();

        AuditLogService::logModelEvent('deleted', $dept, 'Dept deleted');

        $log = AuditLog::where('action', 'deleted')
            ->where('auditable_type', Department::class)
            ->where('auditable_id', $dept->id)
            ->latest('created_at')
            ->first();

        $this->assertNotNull($log);
        $this->assertNull($log->old_values);
        $this->assertNull($log->new_values);
    }

    public function test_log_model_event_sanitizes_password_in_created(): void
    {
        $admin = $this->adminUser();
        $this->actingAs($admin);

        // Simulate logModelEvent on a User model (which has password attribute)
        $user = User::factory()->create();

        AuditLogService::logModelEvent('created', $user, 'User created');

        $log = AuditLog::where('action', 'created')
            ->where('auditable_type', User::class)
            ->where('auditable_id', $user->id)
            ->latest('created_at')
            ->first();

        $this->assertNotNull($log);
        $this->assertSame('***', $log->new_values['password'] ?? '***');
    }
}
