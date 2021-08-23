<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\Team;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TeamTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(\Database\Seeders\PermissionsSeeder::class);

        $this->withoutExceptionHandling();
    }

    /**
     * @test
     */
    public function it_gets_teams_list()
    {
        $teams = Team::factory()
            ->count(5)
            ->create();

        $response = $this->getJson(route('api.v1.teams.index'));

        $response->assertOk()->assertSee($teams[0]->name);
    }

    /**
     * @test
     */
    public function it_stores_the_team()
    {
        $data = Team::factory()
            ->make()
            ->toArray();

        $response = $this->postJson(route('api.v1.teams.store'), $data);

        $this->assertDatabaseHas('teams', $data);

        $response->assertStatus(201)->assertJsonFragment($data);
    }

    /**
     * @test
     */
    public function it_updates_the_team()
    {
        $team = Team::factory()->create();

        $data = [
            'name' => $this->faker->name,
            'logoURI' => $this->faker->text(255),
        ];

        $response = $this->putJson(route('api.v1.teams.update', $team), $data);

        $data['id'] = $team->id;

        $this->assertDatabaseHas('teams', $data);

        $response->assertOk()->assertJsonFragment($data);
    }

    /**
     * @test
     */
    public function it_deletes_the_team()
    {
        $team = Team::factory()->create();

        $response = $this->deleteJson(route('api.v1.teams.destroy', $team));

        $this->assertDeleted($team);

        $response->assertNoContent();
    }
}
