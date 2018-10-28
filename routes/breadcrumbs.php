<?php

// Home
Breadcrumbs::for('home', function ($breadcrumbs) {
    $breadcrumbs->push('Home', route('home'));
});

/*
 * CLIENTS
 **/

// Home > Clients
Breadcrumbs::for('client.index', function ($breadcrumbs) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push('Clients', route('client.index'));
});

// Home > Clients > Create
Breadcrumbs::for('client.create', function ($breadcrumbs) {
    $breadcrumbs->parent('client.index');
    $breadcrumbs->push('Create Client', route('client.create'));
});

// Home > Clients > [Show]
Breadcrumbs::for('client.show', function ($breadcrumbs, $client) {
    $breadcrumbs->parent('client.index');
    $breadcrumbs->push($client->name, route('client.show', ['client' => $client]));
});

// Home > Clients > [Edit]
Breadcrumbs::for('client.edit', function ($breadcrumbs, $client) {
    $breadcrumbs->parent('client.index');
    $breadcrumbs->push('Edit '.$client->name, route('client.edit', ['client' => $client]));
});

/*
 * Projects
 **/

// Home > Projects
Breadcrumbs::for('project.index', function ($breadcrumbs) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push('Projects', route('project.index'));
});

// Home > Projects > Create
Breadcrumbs::for('project.create', function ($breadcrumbs) {
    $breadcrumbs->parent('project.index');
    $breadcrumbs->push('Create Project', route('project.create'));
});

// Home > Projects > [Show]
Breadcrumbs::for('project.show', function ($breadcrumbs, $project) {
    $breadcrumbs->parent('project.index');
    $breadcrumbs->push($project->name, route('project.show', ['project' => $project]));
});

// Home > Projects > [Edit]
Breadcrumbs::for('project.edit', function ($breadcrumbs, $project) {
    $breadcrumbs->parent('project.index');
    $breadcrumbs->push('Edit '.$project->name, route('project.edit', ['project' => $project]));
});

/*
 * Sprints
 **/

// Home > Sprints
Breadcrumbs::for('sprint.index', function ($breadcrumbs) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push('Sprints', route('sprint.index'));
});

// Home > Sprints > Create
Breadcrumbs::for('sprint.create', function ($breadcrumbs) {
    $breadcrumbs->parent('sprint.index');
    $breadcrumbs->push('Create Sprint', route('sprint.create'));
});

// Home > Sprints > [Show]
Breadcrumbs::for('sprint.show', function ($breadcrumbs, $sprint) {
    $breadcrumbs->parent('sprint.index');
    $breadcrumbs->push($sprint->name, route('sprint.show', ['sprint' => $sprint]));
});

// Home > Sprints > [Edit]
Breadcrumbs::for('sprint.edit', function ($breadcrumbs, $sprint) {
    $breadcrumbs->parent('sprint.index');
    $breadcrumbs->push('Edit '.$sprint->name, route('sprint.edit', ['sprint' => $sprint]));
});

/*
 * Tasks
 **/

// Home > Tasks
Breadcrumbs::for('task.index', function ($breadcrumbs) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push('Tasks', route('task.index'));
});

// Home > Tasks > Create
Breadcrumbs::for('task.create', function ($breadcrumbs) {
    $breadcrumbs->parent('task.index');
    $breadcrumbs->push('Create Task', route('task.create'));
});

// Home > Tasks > Show Task
Breadcrumbs::for('task.show', function ($breadcrumbs, $task) {
    $breadcrumbs->parent('task.index');
    $breadcrumbs->push($task->name, route('task.show', ['task' => $task]));
});

// Home > Tasks > Edit Task
Breadcrumbs::for('task.edit', function ($breadcrumbs, $task) {
    $breadcrumbs->parent('task.index');
    $breadcrumbs->push('Edit'.$task->name, route('task.edit', ['task' => $task]));
});

// Home > Sessions
Breadcrumbs::for('session.index', function ($breadcrumbs) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push('Sessions', route('session.index'));
});

// Home > Sessions > Edit Session
Breadcrumbs::for('session.edit', function ($breadcrumbs, $session) {
    $breadcrumbs->parent('session.index');
    $breadcrumbs->push('Edit'.$session->name, route('session.edit', ['session' => $session]));
});

// Home > Invoices
Breadcrumbs::for('invoice.index', function ($breadcrumbs) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push('Invoices', route('invoice.index'));
});

// Home > Invoices > Create Invoice
Breadcrumbs::for('invoice.create', function ($breadcrumbs) {
    $breadcrumbs->parent('invoice.index');
    $breadcrumbs->push('Create Invoice', route('invoice.create'));
});

// Home > Invoices > Create Invoice
Breadcrumbs::for('invoice.show', function ($breadcrumbs, $invoice) {
    $breadcrumbs->parent('invoice.index');
    $breadcrumbs->push('#'.$invoice->id, route('invoice.show', ['invoice' => $invoice]));
});

// Home > Invoices > Edit Invoice
Breadcrumbs::for('invoice.edit', function ($breadcrumbs, $invoice) {
    $breadcrumbs->parent('invoice.index');
    $breadcrumbs->push('Edit '.$invoice->number, route('invoice.edit', ['invoice' => $invoice]));
});
