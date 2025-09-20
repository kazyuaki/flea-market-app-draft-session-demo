<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Item;
use App\Models\User;

class ItemFactory extends Factory
{
    protected $model = Item::class;
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'name' => $this->faker->word(),
            'price' => $this->faker->numberBetween(100, 10000),
            'detail' => $this->faker->text(100),
            'condition' => $this->faker->numberBetween(1, 5),
        ];
    }
}
