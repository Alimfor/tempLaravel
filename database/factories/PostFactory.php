<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Post>
 */
class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [

           'username' => fake() -> realText(20),
            'imageUrl' => fake() -> text,
            'caption' => fake() -> realText(50),
            'lastModifiedBy' => fake() -> realText
        ];
    }
}
