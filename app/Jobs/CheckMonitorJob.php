<?php

namespace App\Jobs;

use App\Enums\MonitorStatus;
use App\Models\Monitor;
use App\Notifications\MonitorStatusChangedNotification;
use App\Services\MonitorCheckerService;
use App\Traits\CalculatesUptime;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CheckMonitorJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, CalculatesUptime;

    /**
     * Create a new job instance.
     */


    public int $tries = 3;
    public int $backoff = 10;

    public function __construct(public Monitor $monitor)
    {
        //
    }

    /**
     * Execute the job.
     */


    public function handle(MonitorCheckerService $checker): void
    {
        $result = $checker->check($this->monitor->url);
      
        $this->monitor->checks()->create([
            'status_code' => $result['status_code'],
            'response_time_ms' => $result['response_time_ms'],
            'is_up' => $result['is_up'],
            'checked_at' => now(),
        ]);
   
        $previousStatus = $this->monitor->status;
     

        if ($result['is_up']) {
            $this->monitor->update([
                'status' => MonitorStatus::UP,
                'consecutive_failures' => 0,
                'last_checked_at' => now(),
                'uptime_percentage' => $this->computeUptime($this->monitor),
            ]);

        } else {
            $failures = $this->monitor->consecutive_failures + 1;
            $newStatus = $failures >= $this->monitor->threshold ? MonitorStatus::DOWN : $this->monitor->status;
            $this->monitor->update([
                'status' => $newStatus,
                'consecutive_failures' => $failures,
                'last_checked_at' => now(),
                'uptime_percentage' => $this->computeUptime($this->monitor),
            ]);
        }
     
        $this->monitor->refresh();
        $newStatus = $this->monitor->status;
        $wentDown = $previousStatus !== MonitorStatus::DOWN && $newStatus === MonitorStatus::DOWN;
        $cameBack = $previousStatus === MonitorStatus::DOWN && $newStatus === MonitorStatus::UP;

        if ($wentDown || $cameBack) {
            $this->monitor->notify(
                new MonitorStatusChangedNotification($this->monitor, $newStatus)
            );
        }
    }

}