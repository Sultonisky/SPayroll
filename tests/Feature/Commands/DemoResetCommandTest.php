<?php

namespace Tests\Feature\Commands;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

/**
 * Feature tests for demo:reset Artisan command.
 *
 * Uses DatabaseMigrations instead of RefreshDatabase because demo:reset runs
 * migrate:fresh (which executes VACUUM on SQLite) — that cannot run inside
 * the transaction RefreshDatabase wraps each test in.
 */
class DemoResetCommandTest extends TestCase
{
    use DatabaseMigrations;

    public function test_command_aborts_when_user_declines_confirmation(): void
    {
        $this->artisan('demo:reset')
            ->expectsConfirmation('This will wipe all demo data and re-seed. Continue?', 'no')
            ->expectsOutput('Aborted.')
            ->assertSuccessful();
    }

    public function test_command_proceeds_when_user_confirms(): void
    {
        $this->artisan('demo:reset')
            ->expectsConfirmation('This will wipe all demo data and re-seed. Continue?', 'yes')
            ->assertSuccessful();
    }

    public function test_command_skips_confirmation_with_force_flag(): void
    {
        $this->artisan('demo:reset', ['--force' => true])
            ->assertSuccessful();
    }
}
