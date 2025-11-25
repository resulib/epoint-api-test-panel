<?php

namespace Tests\Feature;

use App\Models\EpointLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EpointLogsTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_logs_index_page_requires_authentication(): void
    {
        $response = $this->get('/epoint-logs');

        $response->assertRedirect('/login');
    }

    public function test_authenticated_user_can_view_logs_index(): void
    {
        $this->actingAs($this->user);

        $response = $this->get('/epoint-logs');

        $response->assertStatus(200);
        $response->assertViewIs('epoint.logs.index');
    }

    public function test_logs_index_displays_logs(): void
    {
        $this->actingAs($this->user);

        EpointLog::factory()->count(5)->create();

        $response = $this->get('/epoint-logs');

        $response->assertStatus(200);
        $response->assertViewHas('logs');
        $response->assertViewHas('stats');
    }

    public function test_can_view_single_log_detail(): void
    {
        $this->actingAs($this->user);

        $log = EpointLog::factory()->create();

        $response = $this->get("/epoint-logs/{$log->id}");

        $response->assertStatus(200);
        $response->assertViewIs('epoint.logs.show');
        $response->assertViewHas('log');
    }

    public function test_can_delete_log(): void
    {
        $this->actingAs($this->user);

        $log = EpointLog::factory()->create();

        $response = $this->delete("/epoint-logs/{$log->id}");

        $response->assertRedirect('/epoint-logs');
        $this->assertDatabaseMissing('epoint_logs', ['id' => $log->id]);
    }

    public function test_can_filter_logs_by_status(): void
    {
        $this->actingAs($this->user);

        EpointLog::factory()->count(3)->create(['status' => 'success']);
        EpointLog::factory()->count(2)->create(['status' => 'failed']);

        $response = $this->get('/epoint-logs?status=success');

        $response->assertStatus(200);
    }

    public function test_dashboard_page_displays_statistics(): void
    {
        $this->actingAs($this->user);

        EpointLog::factory()->count(10)->create();

        $response = $this->get('/epoint-logs/dashboard');

        $response->assertStatus(200);
        $response->assertViewIs('epoint.logs.dashboard');
    }
}
