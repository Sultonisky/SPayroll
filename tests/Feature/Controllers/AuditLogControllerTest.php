<?php

namespace Tests\Feature\Controllers;

use App\Models\AuditLog;
use App\Models\Department;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Feature tests for AuditLogController.
 *
 * Covers:
 *  - Access control (admin-only, demo exclusion)
 *  - Index: listing, filters (user, action, model, IP, date range)
 *  - Show: single entry detail
 */
class AuditLogControllerTest extends TestCase
{
    use RefreshDatabase;

    // -----------------------------------------------------------------------
    // Helpers
    // -----------------------------------------------------------------------

    /** Create N audit logs, all owned by the given user. */
    private function seedLogs(User $user, int $count = 5, array $state = []): void
    {
        AuditLog::factory()
            ->count($count)
            ->byUser($user)
            ->state($state)
            ->create();
    }

    // -----------------------------------------------------------------------
    // Access control — Index
    // -----------------------------------------------------------------------

    public function test_admin_can_access_audit_log_index(): void
    {
        $admin = $this->adminUser();
        $this->seedLogs($admin);

        $this->actingAs($admin)
            ->get(route('audit-logs.index'))
            ->assertOk();
    }

    public function test_demo_admin_cannot_access_audit_log_index(): void
    {
        $demo = $this->demoAdmin();

        $this->actingAs($demo)
            ->get(route('audit-logs.index'))
            ->assertForbidden();
    }

    public function test_hr_cannot_access_audit_log_index(): void
    {
        $this->actingAs($this->hrUser())
            ->get(route('audit-logs.index'))
            ->assertForbidden();
    }

    public function test_manager_cannot_access_audit_log_index(): void
    {
        $this->actingAs($this->managerUser())
            ->get(route('audit-logs.index'))
            ->assertForbidden();
    }

    public function test_staff_cannot_access_audit_log_index(): void
    {
        $this->actingAs($this->staffUser())
            ->get(route('audit-logs.index'))
            ->assertForbidden();
    }

    public function test_finance_cannot_access_audit_log_index(): void
    {
        $finance = User::factory()->finance()->create();

        $this->actingAs($finance)
            ->get(route('audit-logs.index'))
            ->assertForbidden();
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get(route('audit-logs.index'))
            ->assertRedirect(route('login'));
    }

    // -----------------------------------------------------------------------
    // Index — basic rendering
    // -----------------------------------------------------------------------

    public function test_index_shows_log_entries(): void
    {
        $admin = $this->adminUser();
        $this->seedLogs($admin, 3);

        $this->actingAs($admin)
            ->get(route('audit-logs.index'))
            ->assertOk()
            ->assertViewIs('dashboard.audit-logs.index')
            ->assertViewHas('logs');
    }

    public function test_index_empty_state_renders_without_error(): void
    {
        $this->actingAs($this->adminUser())
            ->get(route('audit-logs.index'))
            ->assertOk()
            ->assertViewHas('logs');
    }

    public function test_index_passes_filter_collections_to_view(): void
    {
        $admin = $this->adminUser();
        $this->seedLogs($admin, 2);

        $response = $this->actingAs($admin)
            ->get(route('audit-logs.index'));

        $response->assertViewHas('users');
        $response->assertViewHas('actions');
        $response->assertViewHas('modelTypes');
    }

    // -----------------------------------------------------------------------
    // Index — filters
    // -----------------------------------------------------------------------

    public function test_filter_by_user_id(): void
    {
        $admin = $this->adminUser();
        $other = $this->hrUser();

        AuditLog::factory()->byUser($admin)->count(3)->create();
        AuditLog::factory()->byUser($other)->count(2)->create();

        $response = $this->actingAs($admin)
            ->get(route('audit-logs.index', ['user_id' => $admin->id]));

        $logs = $response->viewData('logs');

        $this->assertTrue(
            $logs->every(fn ($log) => $log->user_id === $admin->id),
            'Filter should only return logs belonging to the specified user'
        );
    }

    public function test_filter_by_action(): void
    {
        $admin = $this->adminUser();

        AuditLog::factory()->login()->byUser($admin)->count(3)->create();
        AuditLog::factory()->deleted()->byUser($admin)->count(2)->create();

        $response = $this->actingAs($admin)
            ->get(route('audit-logs.index', ['action' => 'login']));

        $logs = $response->viewData('logs');

        $this->assertTrue(
            $logs->every(fn ($log) => $log->action === 'login'),
            'Filter should only return login entries'
        );
    }

    public function test_filter_by_ip_address(): void
    {
        $admin = $this->adminUser();
        $targetIp = '192.168.100.55';

        AuditLog::factory()->fromIp($targetIp)->byUser($admin)->count(2)->create();
        AuditLog::factory()->fromIp('10.0.0.1')->byUser($admin)->count(3)->create();

        $response = $this->actingAs($admin)
            ->get(route('audit-logs.index', ['ip_address' => $targetIp]));

        $logs = $response->viewData('logs');

        $this->assertTrue(
            $logs->every(fn ($log) => str_contains($log->ip_address, $targetIp)),
            'Filter should only return entries matching the IP address'
        );
    }

    public function test_filter_by_auditable_type(): void
    {
        $admin = $this->adminUser();

        AuditLog::factory()
            ->byUser($admin)
            ->forModel(Department::class, 1)
            ->count(2)
            ->create();

        AuditLog::factory()
            ->byUser($admin)
            ->forModel(User::class, 1)
            ->count(3)
            ->create();

        $response = $this->actingAs($admin)
            ->get(route('audit-logs.index', ['auditable_type' => 'Department']));

        $logs = $response->viewData('logs');

        $this->assertTrue(
            $logs->every(fn ($log) => str_contains($log->auditable_type ?? '', 'Department')),
            'Filter should only return Department entries'
        );
    }

    public function test_filter_by_date_from(): void
    {
        $admin = $this->adminUser();

        AuditLog::factory()
            ->byUser($admin)
            ->state(['created_at' => now()->subDays(10)])
            ->count(2)
            ->create();

        AuditLog::factory()
            ->byUser($admin)
            ->state(['created_at' => now()->subDays(30)])
            ->count(2)
            ->create();

        $dateFrom = now()->subDays(15)->format('Y-m-d');

        $response = $this->actingAs($admin)
            ->get(route('audit-logs.index', ['date_from' => $dateFrom]));

        $logs = $response->viewData('logs');

        $this->assertTrue(
            $logs->every(fn ($log) => $log->created_at->gte(now()->subDays(15)->startOfDay())),
            'Filter should only return entries on or after date_from'
        );
    }

    public function test_filter_by_date_to(): void
    {
        $admin = $this->adminUser();

        AuditLog::factory()
            ->byUser($admin)
            ->state(['created_at' => now()->subDays(20)])
            ->count(2)
            ->create();

        AuditLog::factory()
            ->byUser($admin)
            ->state(['created_at' => now()])
            ->count(2)
            ->create();

        $dateTo = now()->subDays(10)->format('Y-m-d');

        $response = $this->actingAs($admin)
            ->get(route('audit-logs.index', ['date_to' => $dateTo]));

        $logs = $response->viewData('logs');

        $this->assertTrue(
            $logs->every(fn ($log) => $log->created_at->lte(now()->subDays(10)->endOfDay())),
            'Filter should only return entries on or before date_to'
        );
    }

    public function test_multiple_filters_combined(): void
    {
        $admin = $this->adminUser();
        $other = $this->hrUser();

        AuditLog::factory()
            ->login()
            ->byUser($admin)
            ->fromIp('1.2.3.4')
            ->state(['created_at' => now()->subDays(2)])
            ->count(2)
            ->create();

        // Should NOT appear — different user
        AuditLog::factory()->login()->byUser($other)->count(2)->create();

        $response = $this->actingAs($admin)
            ->get(route('audit-logs.index', [
                'user_id' => $admin->id,
                'action'  => 'login',
            ]));

        $logs = $response->viewData('logs');

        $this->assertTrue(
            $logs->every(fn ($log) => $log->user_id === $admin->id && $log->action === 'login'),
        );
    }

    // -----------------------------------------------------------------------
    // Show — access control
    // -----------------------------------------------------------------------

    public function test_admin_can_view_audit_log_detail(): void
    {
        $admin = $this->adminUser();
        $log = AuditLog::factory()->byUser($admin)->create();

        $this->actingAs($admin)
            ->get(route('audit-logs.show', $log->id))
            ->assertOk()
            ->assertViewIs('dashboard.audit-logs.show')
            ->assertViewHas('auditLog');
    }

    public function test_demo_admin_cannot_view_audit_log_detail(): void
    {
        $admin = $this->adminUser();
        $demo  = $this->demoAdmin();
        $log   = AuditLog::factory()->byUser($admin)->create();

        $this->actingAs($demo)
            ->get(route('audit-logs.show', $log->id))
            ->assertForbidden();
    }

    public function test_hr_cannot_view_audit_log_detail(): void
    {
        $log = AuditLog::factory()->create();

        $this->actingAs($this->hrUser())
            ->get(route('audit-logs.show', $log->id))
            ->assertForbidden();
    }

    public function test_show_404_for_nonexistent_log(): void
    {
        $this->actingAs($this->adminUser())
            ->get(route('audit-logs.show', 999999))
            ->assertNotFound();
    }

    // -----------------------------------------------------------------------
    // Show — content
    // -----------------------------------------------------------------------

    public function test_show_displays_correct_log_entry(): void
    {
        $admin = $this->adminUser();
        $log   = AuditLog::factory()
            ->byUser($admin)
            ->state([
                'action'      => 'deleted',
                'description' => 'Employee moved to trash',
                'ip_address'  => '192.168.1.99',
            ])
            ->create();

        $response = $this->actingAs($admin)
            ->get(route('audit-logs.show', $log->id));

        $viewLog = $response->viewData('auditLog');
        $this->assertSame($log->id, $viewLog->id);
        $this->assertSame('deleted', $viewLog->action);
        $this->assertSame('192.168.1.99', $viewLog->ip_address);
    }
}
