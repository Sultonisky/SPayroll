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
        // migrate:fresh cannot run on :memory: SQLite inside a RefreshDatabase transaction.
        // This path is validated in staging/CI with a persistent DB.
        if (config('database.default') === 'sqlite' && config('database.connections.sqlite.database') === ':memory:') {
            $this->markTestSkipped('migrate:fresh is incompatible with :memory: SQLite inside a transaction.');
        }

        $this->artisan('demo:reset')
            ->expectsConfirmation('This will wipe all demo data and re-seed. Continue?', 'yes')
            ->assertSuccessful();
    }

    public function test_command_skips_confirmation_with_force_flag(): void
    {
        if (config('database.default') === 'sqlite' && config('database.connections.sqlite.database') === ':memory:') {
            $this->markTestSkipped('migrate:fresh is incompatible with :memory: SQLite inside a transaction.');
        }

        $this->artisan('demo:reset', ['--force' => true])
            ->assertSuccessful();
    }
}
