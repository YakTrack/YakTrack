<?php

// Home
Breadcrumbs::register('home', function ($breadcrumbs) {
    $breadcrumbs->push('Home', route('home'));
});

/*
 * CLIENTS
 **/

// Home > Clients
Breadcrumbs::register('client.index', function ($breadcrumbs) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push('Clients', route('client.index'));
});

// Home > Clients > Create
Breadcrumbs::register('client.create', function ($breadcrumbs) {
    $breadcrumbs->parent('client.index');
    $breadcrumbs->push('Add Client', route('client.create'));
});

// Home > Clients > [Show]
Breadcrumbs::register('client.show', function ($breadcrumbs, $client) {
    $breadcrumbs->parent('client.index');
    $breadcrumbs->push($client->name, route('client.show', ['client' => $client]));
});

// Home > Clients > [Edit]
Breadcrumbs::register('client.edit', function ($breadcrumbs, $client) {
    $breadcrumbs->parent('client.index');
    $breadcrumbs->push('Edit ' . $client->name, route('client.edit', ['client' => $client]));
});

/*
 * Projects
 **/

// Home > Projects
Breadcrumbs::register('project.index', function ($breadcrumbs) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push('Projects', route('project.index'));
});

// Home > Projects > Create
Breadcrumbs::register('project.create', function ($breadcrumbs) {
    $breadcrumbs->parent('project.index');
    $breadcrumbs->push('Create Project', route('project.create'));
});

// Home > Projects > [Show]
Breadcrumbs::register('project.show', function ($breadcrumbs, $project) {
    $breadcrumbs->parent('project.index');
    $breadcrumbs->push($project->name, route('project.show', ['project' => $project]));
});

// Home > Projects > [Edit]
Breadcrumbs::register('project.edit', function ($breadcrumbs, $project) {
    $breadcrumbs->parent('project.index');
    $breadcrumbs->push('Edit ' . $project->name, route('project.edit', ['project' => $project]));
});

/*
 * Sprints
 **/

// Home > Sprints
Breadcrumbs::register('sprint.index', function ($breadcrumbs) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push('Sprints', route('sprint.index'));
});

// Home > Sprints > Create
Breadcrumbs::register('sprint.create', function ($breadcrumbs) {
    $breadcrumbs->parent('sprint.index');
    $breadcrumbs->push('Create Sprint', route('sprint.create'));
});

// Home > Sprints > [Show]
Breadcrumbs::register('sprint.show', function ($breadcrumbs, $sprint) {
    $breadcrumbs->parent('sprint.index');
    $breadcrumbs->push($sprint->name, route('sprint.show', ['sprint' => $sprint]));
});

// Home > Sprints > [Edit]
Breadcrumbs::register('sprint.edit', function ($breadcrumbs, $sprint) {
    $breadcrumbs->parent('sprint.index');
    $breadcrumbs->push('Edit ' . $sprint->name, route('sprint.edit', ['sprint' => $sprint]));
});

/*
 * Tasks
 **/

// Home > Tasks
Breadcrumbs::register('task.index', function ($breadcrumbs) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push('Tasks', route('task.index'));
});

// Home > Tasks > Create
Breadcrumbs::register('task.create', function ($breadcrumbs) {
    $breadcrumbs->parent('task.index');
    $breadcrumbs->push('Create Task', route('task.create'));
});

// Home > Tasks > Show Task
Breadcrumbs::register('task.show', function ($breadcrumbs, $task) {
    $breadcrumbs->parent('task.index');
    $breadcrumbs->push($task->name, route('task.show', ['task' => $task]));
});

// Home > Tasks > Edit Task
Breadcrumbs::register('task.edit', function ($breadcrumbs, $task) {
    $breadcrumbs->parent('task.index');
    $breadcrumbs->push('Edit' . $task->name, route('task.edit', ['task' => $task]));
});

// Home > Sessions
Breadcrumbs::register('session.index', function ($breadcrumbs) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push('Sessions', route('session.index'));
});

// Home > Sessions > Edit Session
Breadcrumbs::register('session.edit', function ($breadcrumbs, $session) {
    $breadcrumbs->parent('session.index');
    $breadcrumbs->push('Edit' . $session->name, route('session.edit', ['session' => $session]));
});

// Home > Invoices
Breadcrumbs::register('invoice.index', function ($breadcrumbs) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push('Invoices', route('invoice.index'));
});

// Home > Invoices > Create Invoice
Breadcrumbs::register('invoice.create', function ($breadcrumbs) {
    $breadcrumbs->parent('invoice.index');
    $breadcrumbs->push('Create Invoice', route('invoice.create'));
});
