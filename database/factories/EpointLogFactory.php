<?php

namespace Database\Factories;

use App\Models\EpointLog;
use Illuminate\Database\Eloquent\Factories\Factory;

class EpointLogFactory extends Factory
{
    protected $model = EpointLog::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'api_endpoint' => $this->faker->randomElement(['/request', '/get-status', '/execute-pay']),
            'api_name' => $this->faker->randomElement(['Payment Request', 'Get Status', 'Execute Pay']),
            'public_key_used' => 'test_public_key_' . $this->faker->uuid,
            'used_custom_keys' => $this->faker->boolean(20),
            'request_params' => [
                'amount' => $this->faker->randomFloat(2, 0.01, 1000),
                'currency' => 'AZN',
                'order_id' => 'TEST_' . $this->faker->unique()->numberBetween(1000, 9999),
            ],
            'request_data' => base64_encode(json_encode(['test' => 'data'])),
            'request_signature' => $this->faker->sha256,
            'response_data' => [
                'status' => $this->faker->randomElement(['success', 'failed', 'error']),
                'transaction' => 'te' . $this->faker->numberBetween(100000000, 999999999),
            ],
            'response_status_code' => $this->faker->randomElement([200, 400, 422, 500]),
            'transaction_id' => 'te' . $this->faker->numberBetween(100000000, 999999999),
            'order_id' => 'TEST_' . $this->faker->numberBetween(1000, 9999),
            'amount' => $this->faker->randomFloat(2, 0.01, 1000),
            'status' => $this->faker->randomElement(['success', 'failed', 'error', 'pending']),
            'ip_address' => $this->faker->ipv4,
            'user_agent' => $this->faker->userAgent,
            'execution_time' => $this->faker->randomFloat(3, 50, 5000),
            'created_at' => $this->faker->dateTimeBetween('-30 days', 'now'),
        ];
    }

    /**
     * Indicate that the log is successful.
     */
    public function successful(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'success',
            'response_status_code' => 200,
            'response_data' => array_merge($attributes['response_data'] ?? [], ['status' => 'success']),
        ]);
    }

    /**
     * Indicate that the log is failed.
     */
    public function failed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'failed',
            'response_status_code' => 422,
            'response_data' => array_merge($attributes['response_data'] ?? [], [
                'status' => 'failed',
                'error' => 'Payment failed',
            ]),
        ]);
    }
}
