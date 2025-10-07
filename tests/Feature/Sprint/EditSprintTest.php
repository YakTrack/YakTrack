<?php

use App\Models\Project;
use App\Models\Sprint;

it('can view the page to edit a sprint', function () {
    $sprint = Sprint::factory()->create();

    $this->actingAsUser();

    $response = $this->get(route('sprint.edit', ['sprint' => $sprint]));

    $response->assertSuccessful();

    $response->assertSee($sprint->name);
});

it('can submit a patch request to update a sprint', function () {
    $this->withoutExceptionHandling();
    $sprint = Sprint::factory()->create([
        'is_open' => 0,
    ]);

    $newProject = Project::factory()->create();

    $this->actingAsUser();

    $response = $this->patch(route('sprint.update', ['sprint' => $sprint]), $newSprintDetails = [
        'name'       => 'New sprint name',
        'project_id' => $newProject->id,
        'is_open'    => 'is_open',
    ]);

    $response->assertRedirect(route('sprint.index'));

    $this->assertDatabaseHas(
        'sprints',
        array_merge(
            $newSprintDetails,
            [
                'id'      => $sprint->id,
                'is_open' => 1,
            ],
        )
    );
});
