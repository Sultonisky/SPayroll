<?php

namespace Tests\Unit;

use App\Models\User;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for User model helper methods (no DB needed).
 */
class UserModelTest extends TestCase
{
    private function makeUser(string $role, bool $isDemo = false): User
    {
        $user           = new User();
        $user->role     = $role;
        $user->is_demo  = $isDemo;

        return $user;
    }

    // -----------------------------------------------------------------------
    // Role helpers
    // -----------------------------------------------------------------------

    public function test_is_admin_returns_true_for_admin(): void
    {
        $this->assertTrue($this->makeUser('admin')->isAdmin());
    }

    public function test_is_admin_returns_false_for_others(): void
    {
        foreach (['HR', 'manager', 'staff'] as $role) {
            $this->assertFalse($this->makeUser($role)->isAdmin(), "Failed for role: $role");
        }
    }

    public function test_is_hr_returns_true_for_hr(): void
    {
        $this->assertTrue($this->makeUser('HR')->isHR());
    }

    public function test_is_manager_returns_true_for_manager(): void
    {
        $this->assertTrue($this->makeUser('manager')->isManager());
    }

    public function test_is_staff_returns_true_for_staff(): void
    {
        $this->assertTrue($this->makeUser('staff')->isStaff());
    }

    // -----------------------------------------------------------------------
    // hasRole()
    // -----------------------------------------------------------------------

    public function test_has_role_returns_true_for_matching_role(): void
    {
        $this->assertTrue($this->makeUser('admin')->hasRole('admin'));
    }

    public function test_has_role_returns_false_for_non_matching_role(): void
    {
        $this->assertFalse($this->makeUser('staff')->hasRole('admin'));
    }

    // -----------------------------------------------------------------------
    // isDemo()
    // -----------------------------------------------------------------------

    public function test_is_demo_true_when_flag_set(): void
    {
        $this->assertTrue($this->makeUser('admin', true)->isDemo());
    }

    public function test_is_demo_false_when_flag_not_set(): void
    {
        $this->assertFalse($this->makeUser('admin', false)->isDemo());
    }
}
