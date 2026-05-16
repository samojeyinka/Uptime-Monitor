<?php

namespace App\Http\Controllers\Monitor;

use App\Http\Controllers\Controller;
use App\Http\Resources\MonitorCheckResource;
use App\Models\Monitor;
use Illuminate\Http\Request;

class MonitorHistoryController extends Controller
{
    public function __invoke(Request $request, Monitor $monitor)
    {
        $perPage = min($request->integer('per_page', 15), 100);
        $checks = $monitor->checks()
            ->orderBy('checked_at', 'desc')
            ->paginate($perPage);

        return MonitorCheckResource::collection($checks);
    }
}
