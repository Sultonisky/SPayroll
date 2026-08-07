<?php

namespace Tests\Feature\Commands;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Feature tests for demo:reset Artisan command.
 *
 * Note: we avoid actually running migrate:fresh in CI — we test the
 * confirmation/abort path and the --force flag behaviour only.
 */
class DemoResetCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_command_aborts_when_user_declines_confirmation(): void
    {
        $this->artisan('demo:reset')
            ->expectsConfirmation('This will wipe all demo data and re-seed. Continue?', 'no')
            ->expectsOutput('Aborted.')
            ->assertSuccessful();
    }

    public function test_command_proceeds_when_user_confirms(): void
    {
        // migrate:fresh runs outside the RefreshDatabase transaction and hits the
        // real connection, which causes unique-constraint collisions when run on
        // a persistent database that already has seeded data.
        $connection = config('database.default');
        $database   = config("database.connections.{$connection}.database");

        if ($connection !== 'sqlite' || $database !== ':memory:') {
            $this->markTestSkipped(
                'demo:reset runs migrate:fresh which conflicts with RefreshDatabase on a persistent database.'
            );
        }

        $this->artisan('demo:reset')
            ->expectsConfirmation('This will wipe all demo data and re-seed. Continue?', 'yes')
            ->assertSuccessful();
    }

    public function test_command_skips_confirmation_with_force_flag(): void
    {
        // migrate:fresh runs outside the RefreshDatabase transaction and hits the
        // real connection, which causes unique-constraint collisions when the
        // seeder is run a second time on a database that already has data from
        // a previous test in the same suite.  Skip on any non-:memory: database.
        $connection = config('database.default');
        $database   = config("database.connections.{$connection}.database");

        if ($connection !== 'sqlite' || $database !== ':memory:') {
            $this->markTestSkipped(
                'demo:reset runs migrate:fresh which conflicts with RefreshDatabase on a persistent database.'
            );
        }

        $this->artisan('demo:reset', ['--force' => true])
            ->assertSuccessful();
    }
}
