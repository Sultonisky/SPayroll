<?php

namespace Tests\Feature\Commands;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Feature tests for app:cleanup-trash Artisan command.
 */
class CleanUpTrashCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_command_exists_and_runs_successfully(): void
    {
        $this->artisan('app:cleanup-trash')
            ->assertSuccessful();
    }

    public function test_command_permanently_deletes_old_trashed_users(): void
    {
        // Soft-delete a user and backdate the deleted_at timestamp past 90 days
        $old = User::factory()->create();
        $old->delete();
        User::withTrashed()->where('id', $old->id)->update([
            'deleted_at' => now()->subDays(91),
        ]);

        $this->artisan('app:cleanup-trash')->assertSuccessful();

        $this->assertDatabaseMissing('users', ['id' => $old->id]);
    }

    public function test_command_keeps_recently_trashed_users(): void
    {
        // Soft-delete a user deleted only 10 days ago — should NOT be removed
        $recent = User::factory()->create();
        $recent->delete(); // deleted_at = now()

        $this->artisan('app:cleanup-trash')->assertSuccessful();

        $this->assertDatabaseHas('users', ['id' => $recent->id]);
    }

    public function test_command_outputs_cleanup_summary(): void
    {
        User::factory()->create()->delete();
        User::withTrashed()->latest('deleted_at')->first()->update([
            'deleted_at' => now()->subDays(91),
        ]);

        $this->artisan('app:cleanup-trash')
            ->expectsOutputToContain('Cleaning up trash')
            ->assertSuccessful();
    }

    public function test_command_handles_empty_trash_gracefully(): void
    {
        $this->artisan('app:cleanup-trash')
            ->expectsOutputToContain('Total items permanently deleted: 0')
            ->assertSuccessful();
    }
}
