<?php

namespace Tests\Unit;

use App\Models\Bonus;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for Bonus model status helper methods.
 */
class BonusModelTest extends TestCase
{
    private function makeBonus(string $status): Bonus
    {
        $bonus         = new Bonus();
        $bonus->status = $status;

        return $bonus;
    }

    public function test_is_pending_true_for_pending(): void
    {
        $this->assertTrue($this->makeBonus('pending')->isPending());
    }

    public function test_is_pending_false_for_approved(): void
    {
        $this->assertFalse($this->makeBonus('approved')->isPending());
    }

    public function test_is_pending_false_for_rejected(): void
    {
        $this->assertFalse($this->makeBonus('rejected')->isPending());
    }

    public function test_is_approved_true_for_approved(): void
    {
        $this->assertTrue($this->makeBonus('approved')->isApproved());
    }

    public function test_is_approved_false_for_pending(): void
    {
        $this->assertFalse($this->makeBonus('pending')->isApproved());
    }

    public function test_is_rejected_true_for_rejected(): void
    {
        $this->assertTrue($this->makeBonus('rejected')->isRejected());
    }

    public function test_is_rejected_false_for_approved(): void
    {
        $this->assertFalse($this->makeBonus('approved')->isRejected());
    }
}
