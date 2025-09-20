<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\User;
use App\Models\Item;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    protected $model = Order::class;
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'item_id' => Item::factory(),
            'payment_method' => 1,
            'shipping_post_code' => $this->faker->postcode(),
            'shipping_address' => $this->faker->address(),
            'shipping_building' => $this->faker->secondaryAddress(),
        ];
    }
}
