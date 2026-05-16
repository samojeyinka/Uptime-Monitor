<?php

use App\Enums\MonitorStatus;
use App\Jobs\CheckMonitorJob;
use App\Models\Monitor;
use App\Notifications\MonitorStatusChangedNotification;
use App\Services\MonitorCheckerService;
use Illuminate\Support\Facades\Notification;
use Mockery\MockInterface;

it('updates monitor status to up and records a check when successful', function () {
    Notification::fake();

    $monitor = Monitor::factory()->create([
        'status' => MonitorStatus::DOWN,
        'consecutive_failures' => 3,
    ]);

    $this->mock(MonitorCheckerService::class, function (MockInterface $mock) use ($monitor) {
        $mock->shouldReceive('check')
            ->once()
            ->with($monitor->url)
            ->andReturn([
                'status_code' => 200,
                'response_time_ms' => 150,
                'is_up' => true,
            ]);
    });

    CheckMonitorJob::dispatchSync($monitor);

    $monitor->refresh();

    expect($monitor->status)->toBe(MonitorStatus::UP);
    expect($monitor->consecutive_failures)->toBe(0);
    expect($monitor->checks()->count())->toBe(1);

    $check = $monitor->checks()->first();
    expect($check->status_code)->toBe(200);
    expect($check->is_up)->toBe(true);

    Notification::assertSentTo(
        $monitor,
        MonitorStatusChangedNotification::class,
        fn ($notification) => $notification->newStatus === MonitorStatus::UP
    );
});

it('updates consecutive failures when check fails but threshold not reached', function () {
    Notification::fake();

    $monitor = Monitor::factory()->create([
        'status' => MonitorStatus::UP,
        'threshold' => 3,
        'consecutive_failures' => 0,
    ]);

    $this->mock(MonitorCheckerService::class, function (MockInterface $mock) use ($monitor) {
        $mock->shouldReceive('check')
            ->once()
            ->with($monitor->url)
            ->andReturn([
                'status_code' => 500,
                'response_time_ms' => 50,
                'is_up' => false,
            ]);
    });

    CheckMonitorJob::dispatchSync($monitor);

    $monitor->refresh();

    expect($monitor->status)->toBe(MonitorStatus::UP);
    expect($monitor->consecutive_failures)->toBe(1);
    expect($monitor->checks()->count())->toBe(1);

    Notification::assertNothingSent();
});

it('updates monitor status to down and notifies when threshold reached', function () {
    Notification::fake();

    $monitor = Monitor::factory()->create([
        'status' => MonitorStatus::UP,
        'threshold' => 3,
        'consecutive_failures' => 2,
    ]);

    $this->mock(MonitorCheckerService::class, function (MockInterface $mock) use ($monitor) {
        $mock->shouldReceive('check')
            ->once()
            ->with($monitor->url)
            ->andReturn([
                'status_code' => 0,
                'response_time_ms' => null,
                'is_up' => false,
            ]);
    });

    CheckMonitorJob::dispatchSync($monitor);

    $monitor->refresh();

    expect($monitor->status)->toBe(MonitorStatus::DOWN);
    expect($monitor->consecutive_failures)->toBe(3);

    Notification::assertSentTo(
        $monitor,
        MonitorStatusChangedNotification::class,
        fn ($notification) => $notification->newStatus === MonitorStatus::DOWN
    );
});
