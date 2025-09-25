<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\ClientUser;
use App\Models\Project;
use App\Models\Task;
use App\Models\Session;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ClientPortalAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_client_portal_login_page_loads()
    {
        $response = $this->get('/client-portal/login');

        $response->assertStatus(200);
    }

    public function test_client_user_can_login()
    {
        $client = Client::factory()->create();
        $clientUser = ClientUser::factory()->create([
            'client_id' => $client->id,
            'email' => 'test@client.com',
            'password' => bcrypt('password'),
        ]);

        $response = $this->post('/client-portal/login', [
            'email' => 'test@client.com',
            'password' => 'password',
        ]);

        $response->assertRedirect('/client-portal');
        $this->assertAuthenticated('client');
    }

    public function test_client_user_cannot_login_with_invalid_credentials()
    {
        $client = Client::factory()->create();
        $clientUser = ClientUser::factory()->create([
            'client_id' => $client->id,
            'email' => 'test@client.com',
            'password' => bcrypt('password'),
        ]);

        $response = $this->post('/client-portal/login', [
            'email' => 'test@client.com',
            'password' => 'wrong-password',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest('client');
    }

    public function test_inactive_client_user_cannot_login()
    {
        $client = Client::factory()->create();
        $clientUser = ClientUser::factory()->create([
            'client_id' => $client->id,
            'email' => 'test@client.com',
            'password' => bcrypt('password'),
            'is_active' => false,
        ]);

        $response = $this->post('/client-portal/login', [
            'email' => 'test@client.com',
            'password' => 'password',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest('client');
    }

    public function test_client_user_can_access_dashboard()
    {
        $client = Client::factory()->create();
        $clientUser = ClientUser::factory()->create([
            'client_id' => $client->id,
        ]);

        $response = $this->actingAs($clientUser, 'client')
            ->get('/client-portal');

        $response->assertStatus(200);
    }

    public function test_client_user_can_view_their_projects()
    {
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
        $response->assertInertia(fn ($page) => $page
            ->component('ClientPortal/Projects/Index')
            ->has('projects', 1)
        );
    }

    public function test_client_user_cannot_view_other_clients_projects()
    {
        $client1 = Client::factory()->create();
        $client2 = Client::factory()->create();
        $clientUser = ClientUser::factory()->create([
            'client_id' => $client1->id,
        ]);
        $project = Project::factory()->create([
            'client_id' => $client2->id,
        ]);

        $response = $this->actingAs($clientUser, 'client')
            ->get('/client-portal/projects/' . $project->id);

        $response->assertStatus(403);
    }

    public function test_client_user_can_view_their_tasks()
    {
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
        $response->assertInertia(fn ($page) => $page
            ->component('ClientPortal/Tasks/Index')
            ->has('tasks', 1)
        );
    }

    public function test_client_user_can_view_their_billable_sessions()
    {
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
            'task_id' => $task->id,
            'is_billable' => true,
        ]);

        $response = $this->actingAs($clientUser, 'client')
            ->get('/client-portal/sessions');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('ClientPortal/Sessions/Index')
            ->has('sessions.data', 1)
        );
    }

    public function test_client_user_can_logout()
    {
        $client = Client::factory()->create();
        $clientUser = ClientUser::factory()->create([
            'client_id' => $client->id,
        ]);

        $response = $this->actingAs($clientUser, 'client')
            ->post('/client-portal/logout');

        $response->assertRedirect('/client-portal/login');
        $this->assertGuest('client');
    }
}
