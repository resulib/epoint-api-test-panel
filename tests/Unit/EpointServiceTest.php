<?php

namespace Tests\Unit;

use App\Exceptions\InvalidConfigurationException;
use App\Models\EpointLog;
use App\Services\EpointService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class EpointServiceTest extends TestCase
{
    use RefreshDatabase;

    protected EpointService $service;

    protected function setUp(): void
    {
        parent::setUp();

        Config::set('services.epoint', [
            'public_key' => 'test_public_key',
            'private_key' => 'test_private_key',
            'base_url' => 'https://test.epoint.az/api/1',
        ]);

        $this->service = new EpointService();
    }

    public function test_can_set_custom_keys(): void
    {
        $result = $this->service->setCustomKeys('custom_public', 'custom_private');

        $this->assertInstanceOf(EpointService::class, $result);
    }

    public function test_generates_correct_signature(): void
    {
        $reflection = new \ReflectionClass($this->service);
        $method = $reflection->getMethod('generateSignature');
        $method->setAccessible(true);

        $data = 'test_data';
        $signature = $method->invokeArgs($this->service, [$data]);

        $this->assertNotEmpty($signature);
        $this->assertIsString($signature);
    }

    public function test_payment_request_creates_log(): void
    {
        Http::fake([
            '*' => Http::response([
                'status' => 'success',
                'transaction' => 'te000000001',
            ], 200)
        ]);

        $params = [
            'amount' => 10.50,
            'currency' => 'AZN',
            'language' => 'az',
            'order_id' => 'TEST_' . time(),
            'description' => 'Test payment',
        ];

        $result = $this->service->paymentRequest($params);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('log_id', $result);
        $this->assertArrayHasKey('response', $result);

        $this->assertDatabaseHas('epoint_logs', [
            'id' => $result['log_id'],
            'api_endpoint' => '/request',
            'status' => 'success',
        ]);
    }

    public function test_get_status_request(): void
    {
        Http::fake([
            '*' => Http::response([
                'status' => 'success',
                'transaction' => 'te000000001',
                'payment_status' => 'paid',
            ], 200)
        ]);

        $result = $this->service->getStatus('te000000001');

        $this->assertIsArray($result);
        $this->assertArrayHasKey('response', $result);
        $this->assertEquals('success', $result['response']['status']);
    }
}
