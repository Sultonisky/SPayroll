<?php

namespace Tests\Unit;

use App\Models\AuditLog;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for AuditLog model computed attributes.
 * No DB needed — all tested via plain object instantiation.
 */
class AuditLogModelTest extends TestCase
{
    private function makeLog(string $action, ?string $type = null): AuditLog
    {
        $log = new AuditLog;
        $log->action = $action;
        $log->auditable_type = $type;

        return $log;
    }

    // -----------------------------------------------------------------------
    // getAuditableNameAttribute
    // -----------------------------------------------------------------------

    public function test_auditable_name_returns_class_basename(): void
    {
        $log = $this->makeLog('created', 'App\\Models\\Employee');
        $this->assertSame('Employee', $log->auditable_name);
    }

    public function test_auditable_name_returns_dash_when_null(): void
    {
        $log = $this->makeLog('login', null);
        $this->assertSame('—', $log->auditable_name);
    }

    public function test_auditable_name_works_for_all_model_types(): void
    {
        $map = [
            'App\\Models\\User' => 'User',
            'App\\Models\\Department' => 'Department',
            'App\\Models\\Position' => 'Position',
            'App\\Models\\Payroll' => 'Payroll',
            'App\\Models\\Bonus' => 'Bonus',
        ];

        foreach ($map as $fqn => $expected) {
            $this->assertSame($expected, $this->makeLog('updated', $fqn)->auditable_name, "Failed for $fqn");
        }
    }

    // -----------------------------------------------------------------------
    // getActionColorAttribute
    // -----------------------------------------------------------------------

    /** @dataProvider actionColorProvider */
    #[DataProvider('actionColorProvider')]
    public function test_action_color_returns_correct_badge(string $action, string $expectedColor): void
    {
        $this->assertSame($expectedColor, $this->makeLog($action)->action_color);
    }

    public static function actionColorProvider(): array
    {
        return [
            'created' => ['created',      'success'],
            'updated' => ['updated',       'warning'],
            'deleted' => ['deleted',       'danger'],
            'restored' => ['restored',      'info'],
            'force_deleted' => ['force_deleted', 'dark'],
            'login' => ['login',         'primary'],
            'logout' => ['logout',        'secondary'],
            'login_failed' => ['login_failed',  'danger'],
            'export' => ['export',        'info'],
            'approved' => ['approved',      'success'],
            'rejected' => ['rejected',      'danger'],
            'mark_paid' => ['mark_paid',     'success'],
            'unknown' => ['something_new', 'secondary'],
        ];
    }

    // -----------------------------------------------------------------------
    // getActionIconAttribute
    // -----------------------------------------------------------------------

    /** @dataProvider actionIconProvider */
    #[DataProvider('actionIconProvider')]
    public function test_action_icon_returns_fa_class(string $action, string $expectedIcon): void
    {
        $this->assertSame($expectedIcon, $this->makeLog($action)->action_icon);
    }

    public static function actionIconProvider(): array
    {
        return [
            'created' => ['created',      'fa-plus-circle'],
            'updated' => ['updated',       'fa-edit'],
            'deleted' => ['deleted',       'fa-trash'],
            'restored' => ['restored',      'fa-undo'],
            'force_deleted' => ['force_deleted', 'fa-times-circle'],
            'login' => ['login',         'fa-sign-in-alt'],
            'logout' => ['logout',        'fa-sign-out-alt'],
            'login_failed' => ['login_failed',  'fa-exclamation-triangle'],
            'export' => ['export',        'fa-download'],
            'approved' => ['approved',      'fa-check-circle'],
            'rejected' => ['rejected',      'fa-ban'],
            'mark_paid' => ['mark_paid',     'fa-money-bill-wave'],
            'unknown' => ['something_new', 'fa-circle'],
        ];
    }

    // -----------------------------------------------------------------------
    // Append-only settings
    // -----------------------------------------------------------------------

    public function test_timestamps_disabled(): void
    {
        $log = new AuditLog;
        $this->assertFalse($log->timestamps);
    }

    public function test_updated_at_is_null_constant(): void
    {
        $this->assertNull(AuditLog::UPDATED_AT);
    }
}
