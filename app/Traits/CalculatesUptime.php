<?php

namespace App\Traits;

use App\Models\Monitor;

trait CalculatesUptime
{
   
    protected function computeUptime(Monitor $monitor): float
    {
        $total = $monitor->checks()->count();

        if ($total === 0) {
            return 0.0;
        }

        $up = $monitor->checks()->where('is_up', true)->count();

        return round(($up / $total) * 100, 2);
    }
}
