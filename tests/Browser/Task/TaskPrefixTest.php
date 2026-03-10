<?php

use App\Models\Project;
use App\Models\User;

it('does not change task name when selecting project with prefix and name is not empty', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create([
        'task_code_prefix' => 'TEST',
    ]);

    $this->actingAs($user);

    $page = visit(route('task.create'))
        ->assertSee('Create Task')
        ->type('Task Name', 'My Task Name')
        ->click('Select a project')
        ->click($project->name)
        ->assertSee('My Task Name')
        ->assertDontSee('TEST-0001:');
});

it('does not change task name when it already has matching prefix', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create([
        'task_code_prefix' => 'TEST',
    ]);

    $this->actingAs($user);

    $page = visit(route('task.create'))
        ->assertSee('Create Task')
        ->type('Task Name', 'TEST-0005: My Task Name')
        ->click('Select a project')
        ->click($project->name)
        ->assertSee('TEST-0005: My Task Name')
        ->assertDontSee('TEST-0001: TEST-0005: My Task Name');
});

it('sets prefix when task name is empty and project is selected', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create([
        'task_code_prefix' => 'TEST',
    ]);

    $this->actingAs($user);

    $page = visit(route('task.create'))
        ->assertSee('Create Task')
        ->click('Select a project')
        ->click($project->name)
        ->assertSee('TEST-0001:');
});

it('does not change task name when selecting project without a prefix', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create([
        'task_code_prefix' => null,
    ]);

    $this->actingAs($user);

    $page = visit(route('task.create'))
        ->assertSee('Create Task')
        ->type('Task Name', 'LEP-1234: Fix the bug')
        ->click('Select a project')
        ->click($project->name)
        ->assertSee('LEP-1234: Fix the bug');
});
