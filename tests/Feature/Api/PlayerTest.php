<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\Player;

use App\Models\Team;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PlayerTest extends TestCase
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
    public function it_gets_players_list()
    {
        $players = Player::factory()
            ->count(5)
            ->create();

        $response = $this->getJson(route('api.v1.players.index'));

        $response->assertOk()->assertSee($players[0]->firstName);
    }

    /**
     * @test
     */
    public function it_stores_the_player()
    {
        $data = Player::factory()
            ->make()
            ->toArray();

        $response = $this->postJson(route('api.v1.players.store'), $data);

        $this->assertDatabaseHas('players', $data);

        $response->assertStatus(201)->assertJsonFragment($data);
    }

    /**
     * @test
     */
    public function it_updates_the_player()
    {
        $player = Player::factory()->create();

        $team = Team::factory()->create();

        $data = [
            'firstName' => $this->faker->text(255),
            'lastName' => $this->faker->lastName,
            'playerImageURI' => $this->faker->text(255),
            'team_id' => $team->id,
        ];

        $response = $this->putJson(route('api.v1.players.update', $player), $data);

        $data['id'] = $player->id;

        $this->assertDatabaseHas('players', $data);

        $response->assertOk()->assertJsonFragment($data);
    }

    /**
     * @test
     */
    public function it_deletes_the_player()
    {
        $player = Player::factory()->create();

        $response = $this->deleteJson(route('api.v1.players.destroy', $player));

        $this->assertDeleted($player);

        $response->assertNoContent();
    }
}
