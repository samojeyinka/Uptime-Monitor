<?php
namespace App\Services;
use Illuminate\Support\Facades\Http;
class MonitorCheckerService
{
    public function check(string $url): array
    {
        $start = microtime(true);
        try {
            $response = Http::timeout(10)->get($url);
            $ms = (int) round((microtime(true) - $start) * 1000);
            $code = $response->status();

            // 2xx = successful(), 3xx = redirect() — both count as up
            $isUp = $response->successful() || $response->redirect();
            return [
                'status_code' => $code,
                'response_time_ms' => $ms,
                'is_up' => $isUp,
            ];
            
        } catch (\Exception $e) {
            // Timeout, DNS failure, refused connection
            return [
                'status_code' => 0,
                'response_time_ms' => null,
                'is_up' => false,
            ];
        }
    }
}
