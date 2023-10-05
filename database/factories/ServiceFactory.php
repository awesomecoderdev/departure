<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Service>
 */
class ServiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            "name" => fake()->name(),
            "price" => fake()->randomNumber(),
            "guide_id" => 3,
            // "agency_id" => "required|exists:agencies,id",
            // "category_id" => "required|exists:categories,id",
            "short_description" => fake()->text(),
            "long_description" => fake()->text(),
            "address" => fake()->address(),
            // "discount" => "required|integer",
            // "thumbnail.*" => "nullable|image|mimes:jpeg,png,jpg|max:2048",
            // 'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ];
    }
}
