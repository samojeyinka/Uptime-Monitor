<?php

namespace App\Http\Controllers\Monitor;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateMonitorRequest;
use App\Http\Resources\MonitorResource;
use App\Jobs\CheckMonitorJob;
use App\Models\Monitor;


class CreateMonitorController extends Controller
{

    public function __construct(protected Monitor $monitor){}
    public function __invoke(CreateMonitorRequest $request)
    {
        $monitor = $this->monitor->create($request->validated());

        CheckMonitorJob::dispatch($monitor);
        return (new MonitorResource($monitor))
            ->response()
            ->setStatusCode(201);
    }
}
