<?php

namespace Database\Factories;

use App\Models\Player;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

class PlayerFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Player::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'firstName' => $this->faker->text(255),
            'lastName' => $this->faker->lastName,
            'playerImageURI' => $this->faker->text(255),
            'team_id' => \App\Models\Team::factory(),
        ];
    }
}
