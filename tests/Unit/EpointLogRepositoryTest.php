<?php

namespace Tests\Unit;

use App\Models\EpointLog;
use App\Repositories\EpointLogRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EpointLogRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected EpointLogRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new EpointLogRepository(new EpointLog());
    }

    public function test_can_create_log(): void
    {
        $data = [
            'api_endpoint' => '/request',
            'api_name' => 'Payment Request',
            'public_key_used' => 'test_public_key',
            'used_custom_keys' => false,
            'request_params' => ['amount' => 10.50],
            'request_data' => 'base64_encoded_data',
            'request_signature' => 'test_signature',
            'response_data' => ['status' => 'success'],
            'response_status_code' => 200,
            'status' => 'success',
            'execution_time' => 150.5,
        ];

        $log = $this->repository->create($data);

        $this->assertInstanceOf(EpointLog::class, $log);
        $this->assertEquals('/request', $log->api_endpoint);
        $this->assertEquals('success', $log->status);
    }

    public function test_can_find_by_id(): void
    {
        $log = EpointLog::factory()->create([
            'api_endpoint' => '/request',
            'status' => 'success',
        ]);

        $found = $this->repository->findById($log->id);

        $this->assertNotNull($found);
        $this->assertEquals($log->id, $found->id);
    }

    public function test_can_get_statistics(): void
    {
        EpointLog::factory()->count(5)->create(['status' => 'success']);
        EpointLog::factory()->count(3)->create(['status' => 'failed']);

        $stats = $this->repository->getStatistics();

        $this->assertIsArray($stats);
        $this->assertEquals(8, $stats['total']);
        $this->assertEquals(5, $stats['success']);
        $this->assertEquals(3, $stats['failed']);
    }

    public function test_can_filter_by_status(): void
    {
        EpointLog::factory()->count(3)->create(['status' => 'success']);
        EpointLog::factory()->count(2)->create(['status' => 'failed']);

        $logs = $this->repository->getByStatus('success');

        $this->assertCount(3, $logs);
        $this->assertTrue($logs->every(fn($log) => $log->status === 'success'));
    }

    public function test_can_delete_log(): void
    {
        $log = EpointLog::factory()->create();

        $result = $this->repository->delete($log->id);

        $this->assertTrue($result);
        $this->assertDatabaseMissing('epoint_logs', ['id' => $log->id]);
    }

    public function test_can_get_unique_endpoints(): void
    {
        EpointLog::factory()->create(['api_endpoint' => '/request', 'api_name' => 'Payment Request']);
        EpointLog::factory()->create(['api_endpoint' => '/request', 'api_name' => 'Payment Request']);
        EpointLog::factory()->create(['api_endpoint' => '/get-status', 'api_name' => 'Get Status']);

        $endpoints = $this->repository->getUniqueEndpoints();

        $this->assertCount(2, $endpoints);
    }
}
