<?php

namespace Database\Factories;

use App\Enums\MonitorStatus;
use App\Models\Monitor;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Monitor>
 */
class MonitorFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'url' => $this->faker->url(),
            'check_interval' => 60,
            'threshold' => 3,
            'status' => MonitorStatus::UP,
            'consecutive_failures' => 0,
            'uptime_percentage' => 100.0,
        ];
    }
}
