<?php

use App\Models\Session;
use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

/**
 * Logging in happens via an Inertia XHR, so the browser never reloads app.blade.php
 * and the `<meta name="csrf-token">` tag baked into the initial page load is left in
 * place. But logging in also regenerates the session's CSRF token server-side, so
 * that meta tag goes stale the instant login succeeds. Components that read it
 * directly (like ManagePendingTasksModal's hand-rolled fetch() calls) would then
 * send a stale X-CSRF-TOKEN and get "CSRF token mismatch." Login.vue's onSuccess
 * handler refreshes the meta tag immediately to fix this.
 *
 * Note: Laravel disables CSRF verification for any request served while
 * app()->runningUnitTests() is true (see VerifyCsrfToken::handle()), which is
 * always the case for Pest's browser test server. So a real 419 can't be
 * reproduced here; instead we assert directly that the meta tag matches the
 * server's live token, which is the actual condition the bug depends on.
 */
it('refreshes the CSRF meta tag immediately after logging in', function () {
    User::factory()->create([
        'email'    => 'test@domain.com',
        'password' => Hash::make('password'),
    ]);

    $page = visit('/login');

    $page->fill('email', 'test@domain.com')
        ->fill('password', 'password')
        ->click('Login')
        ->assertNoJavaScriptErrors();

    // The refresh fires from an async fetch, so poll briefly rather than
    // asserting the instant the login click resolves.
    $metaTokenMatchesLiveToken = $page->script(<<<'JS'
        (async () => {
            const { token: liveToken } = await fetch('/csrf-token', { headers: { Accept: 'application/json' } })
                .then((response) => response.json());

            const deadline = Date.now() + 3000;
            let metaToken = document.querySelector('meta[name="csrf-token"]').content;

            while (metaToken !== liveToken && Date.now() < deadline) {
                await new Promise((resolve) => setTimeout(resolve, 50));
                metaToken = document.querySelector('meta[name="csrf-token"]').content;
            }

            return metaToken === liveToken;
        })()
        JS);

    expect($metaTokenMatchesLiveToken)->toBeTrue();
});

it('can link a task from the manage linked tasks modal immediately after logging in', function () {
    User::factory()->create([
        'email'    => 'test@domain.com',
        'password' => Hash::make('password'),
    ]);
    $task = Task::factory()->create(['name' => 'A newly created task']);
    Session::factory()->running()->create();

    $page = visit('/login');

    $page->fill('email', 'test@domain.com')
        ->fill('password', 'password')
        ->click('Login')
        ->assertNoJavaScriptErrors();

    $this->assertAuthenticated();

    // Click the sidebar's Inertia <Link> rather than navigating directly, so the
    // browser never reloads app.blade.php and the post-login CSRF meta tag stays
    // exactly as stale as it would for a real user clicking around the SPA.
    $page->click('Sessions')
        ->click('Manage linked tasks')
        ->assertSee('Manage linked tasks')
        ->click('Select option')
        ->click($task->name)
        ->click('Add')
        ->assertSee($task->name)
        ->assertDontSee('CSRF token mismatch');

    $this->assertDatabaseHas('session_pending_tasks', [
        'task_id' => $task->id,
    ]);
});
