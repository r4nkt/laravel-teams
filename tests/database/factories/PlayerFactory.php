<?php

namespace R4nkt\Teams\Tests\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use R4nkt\Teams\Tests\TestClasses\Models\Player;

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
            //
        ];
    }
}
