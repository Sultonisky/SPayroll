<?php

namespace Tests\Unit;

use App\Models\Position;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for Position model helper methods.
 */
class PositionModelTest extends TestCase
{
    private function makePosition(?float $fulltime, ?float $internship): Position
    {
        $position = new Position;
        $position->base_salary_fulltime = $fulltime;
        $position->base_salary_internship = $internship;

        return $position;
    }

    public function test_returns_fulltime_salary_for_fulltime_type(): void
    {
        $position = $this->makePosition(10_000_000, 2_000_000);

        $this->assertEquals(10_000_000, $position->getBaseSalaryFor('fulltime'));
    }

    public function test_returns_internship_salary_for_internship_type(): void
    {
        $position = $this->makePosition(10_000_000, 2_000_000);

        $this->assertEquals(2_000_000, $position->getBaseSalaryFor('internship'));
    }

    public function test_returns_null_for_unknown_type(): void
    {
        $position = $this->makePosition(10_000_000, 2_000_000);

        $this->assertNull($position->getBaseSalaryFor('contract'));
    }

    public function test_returns_null_when_fulltime_salary_not_set(): void
    {
        $position = $this->makePosition(null, 2_000_000);

        $this->assertNull($position->getBaseSalaryFor('fulltime'));
    }

    public function test_returns_null_when_internship_salary_not_set(): void
    {
        $position = $this->makePosition(10_000_000, null);

        $this->assertNull($position->getBaseSalaryFor('internship'));
    }
}
