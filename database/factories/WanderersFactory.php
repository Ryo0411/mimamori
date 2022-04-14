<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Wanderers;
use Faker\Generator as Faker;

$factory->define(Wanderers::class, function (Faker $faker) {
    return [
        // 'name' => $faker->name,
    ];
});

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Wanderers>
 */
class WanderersFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            //
        ];
    }
}
