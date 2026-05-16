<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;

class MonitorCheck extends Model
{
    use HasUlids;

    public $timestamps = false;

    protected $fillable = [
        'monitor_id',
        'status_code',
        'response_time_ms',
        'is_up',
        'checked_at',
    ];

    protected $keyType = 'string';

    public $incrementing = false;

    protected $casts = [
        'is_up' => 'boolean',
        'checked_at' => 'datetime',
    ];

    public function monitor()
    {
        return $this->belongsTo(Monitor::class);
    }
}
