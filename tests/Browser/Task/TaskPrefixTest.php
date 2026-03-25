<?php

use App\Models\Project;
use App\Models\User;

const TASK_NAME_INPUT = '[placeholder="Enter task name"]';

/**
 * Browser tests use a real HTTP session. We use a testing-only route that performs a
 * server-side login so the session cookie matches the Pest HTTP server.
 */
function visitTaskCreatePage(User $user): mixed
{
    return visit(route('testing.browser-login', $user, false))
        ->navigate(route('task.create', [], false))
        ->assertPathIs('/task/create')
        ->assertSee('Create Task');
}

it('does not change task name when selecting project with prefix and name is not empty', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create([
        'task_code_prefix' => 'TEST',
    ]);

    $page = visitTaskCreatePage($user)
        ->type(TASK_NAME_INPUT, 'My Task Name')
        ->click('Select a project')
        ->click($project->name);

    expect($page->value(TASK_NAME_INPUT))->toBe('My Task Name');
    expect($page->value(TASK_NAME_INPUT))->not->toContain('TEST-0001:');
});

it('does not change task name when it already has matching prefix', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create([
        'task_code_prefix' => 'TEST',
    ]);

    $page = visitTaskCreatePage($user)
        ->type(TASK_NAME_INPUT, 'TEST-0005: My Task Name')
        ->click('Select a project')
        ->click($project->name);

    expect($page->value(TASK_NAME_INPUT))->toBe('TEST-0005: My Task Name');
    expect($page->value(TASK_NAME_INPUT))->not->toContain('TEST-0001: TEST-0005');
});

it('sets prefix when task name is empty and project is selected', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create([
        'task_code_prefix' => 'TEST',
    ]);

    $page = visitTaskCreatePage($user)
        ->click('Select a project')
        ->click($project->name);

    expect($page->value(TASK_NAME_INPUT))->toBe('TEST-0001: ');
});

it('does not change task name when selecting project without a prefix', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create([
        'task_code_prefix' => null,
    ]);

    $page = visitTaskCreatePage($user)
        ->type(TASK_NAME_INPUT, 'LEP-1234: Fix the bug')
        ->click('Select a project')
        ->click($project->name);

    expect($page->value(TASK_NAME_INPUT))->toBe('LEP-1234: Fix the bug');
});
