<?php

namespace Database\Factories;

use App\Models\Translation;
use Illuminate\Database\Eloquent\Factories\Factory;

class TranslationFactory extends Factory
{
    protected $model = Translation::class;

    public function definition(): array
    {
        return [
            'key' => 'key-'.uniqid('', true).'-'.random_int(1, PHP_INT_MAX),
            'content' => $this->faker->sentence(),
            'locale' => 'en',
            'created_at' => now(),
            'updated_at' => now()
        ];
    }
} 