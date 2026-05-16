<?php

namespace App\Http\Controllers\Monitor;

use App\Http\Controllers\Controller;
use App\Http\Resources\MonitorResource;
use App\Models\Monitor;

class ListMonitorsController extends Controller
{

    public function __construct(protected Monitor $monitor){}

    public function __invoke()
    {
        return MonitorResource::collection(
            $this->monitor->orderBy('created_at', 'desc')->get()
        );
    }
}
