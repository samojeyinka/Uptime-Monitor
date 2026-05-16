<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

use App\Models\Monitor;
use Illuminate\Contracts\Console\Kernel;

$monitor = Monitor::find('01krpvcqncy5thsc25p2ec69e7');
if ($monitor) {
    $counts = $monitor->checks()->selectRaw('is_up, count(*) as count')->groupBy('is_up')->get();
    foreach ($counts as $c) {
        echo 'is_up: '.($c->is_up ? 'true' : 'false')." - Count: {$c->count}\n";
    }
} else {
    echo "Monitor not found\n";
}
