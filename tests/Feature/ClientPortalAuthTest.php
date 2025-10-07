<?php

use App\Models\Client;
use App\Models\ClientUser;
use App\Models\Project;
use App\Models\Session;
use App\Models\Task;

it('loads the client portal login page', function () {
    $response = $this->get('/client-portal/login');

    $response->assertStatus(200);
});

it('can login as a client user', function () {
    $client = Client::factory()->create();
    $clientUser = ClientUser::factory()->create([
        'client_id' => $client->id,
        'email'     => 'test@client.com',
        'password'  => bcrypt('password'),
    ]);

    $response = $this->post('/client-portal/login', [
        'email'    => 'test@client.com',
        'password' => 'password',
    ]);

    $response->assertRedirect('/client-portal');
    $this->assertAuthenticated('client');
});

it('cannot login with invalid credentials', function () {
    $client = Client::factory()->create();
    $clientUser = ClientUser::factory()->create([
        'client_id' => $client->id,
        'email'     => 'test@client.com',
        'password'  => bcrypt('password'),
    ]);

    $response = $this->post('/client-portal/login', [
        'email'    => 'test@client.com',
        'password' => 'wrong-password',
    ]);

    $response->assertSessionHasErrors('email');
    $this->assertGuest('client');
});

it('cannot login as an inactive client user', function () {
    $client = Client::factory()->create();
    $clientUser = ClientUser::factory()->create([
        'client_id' => $client->id,
        'email'     => 'test@client.com',
        'password'  => bcrypt('password'),
        'is_active' => false,
    ]);

    $response = $this->post('/client-portal/login', [
        'email'    => 'test@client.com',
        'password' => 'password',
    ]);

    $response->assertSessionHasErrors('email');
    $this->assertGuest('client');
});

it('can access the dashboard', function () {
    $client = Client::factory()->create();
    $clientUser = ClientUser::factory()->create([
        'client_id' => $client->id,
    ]);

    $response = $this->actingAs($clientUser, 'client')
        ->get('/client-portal');

    $response->assertStatus(200);
});

it('can view their projects', function () {
    $client = Client::factory()->create();
    $clientUser = ClientUser::factory()->create([
        'client_id' => $client->id,
    ]);
    $project = Project::factory()->create([
        'client_id' => $client->id,
    ]);

    $response = $this->actingAs($clientUser, 'client')
        ->get('/client-portal/projects');

    $response->assertStatus(200);
    $response->assertInertia(
        fn ($page) => $page
        ->component('ClientPortal/Projects/Index')
        ->has('projects', 1)
    );
});

it('cannot view other clients projects', function () {
    $client1 = Client::factory()->create();
    $client2 = Client::factory()->create();
    $clientUser = ClientUser::factory()->create([
        'client_id' => $client1->id,
    ]);
    $project = Project::factory()->create([
        'client_id' => $client2->id,
    ]);

    $response = $this->actingAs($clientUser, 'client')
        ->get('/client-portal/projects/'.$project->id);

    $response->assertStatus(403);
});

it('can view their tasks', function () {
    $client = Client::factory()->create();
    $clientUser = ClientUser::factory()->create([
        'client_id' => $client->id,
    ]);
    $project = Project::factory()->create([
        'client_id' => $client->id,
    ]);
    $task = Task::factory()->create([
        'project_id' => $project->id,
    ]);

    $response = $this->actingAs($clientUser, 'client')
        ->get('/client-portal/tasks');

    $response->assertStatus(200);
    $response->assertInertia(
        fn ($page) => $page
        ->component('ClientPortal/Tasks/Index')
        ->has('tasks', 1)
    );
});

it('can view their billable sessions', function () {
    $client = Client::factory()->create();
    $clientUser = ClientUser::factory()->create([
        'client_id' => $client->id,
    ]);
    $project = Project::factory()->create([
        'client_id' => $client->id,
    ]);
    $task = Task::factory()->create([
        'project_id' => $project->id,
    ]);
    $session = Session::factory()->create([
        'task_id'     => $task->id,
        'is_billable' => true,
    ]);

    $response = $this->actingAs($clientUser, 'client')
        ->get('/client-portal/sessions');

    $response->assertStatus(200);
    $response->assertInertia(
        fn ($page) => $page
        ->component('ClientPortal/Sessions/Index')
        ->has('sessions.data', 1)
    );
});

it('can logout', function () {
    $client = Client::factory()->create();
    $clientUser = ClientUser::factory()->create([
        'client_id' => $client->id,
    ]);

    $response = $this->actingAs($clientUser, 'client')
        ->post('/client-portal/logout');

    $response->assertRedirect('/client-portal/login');
    $this->assertGuest('client');
});