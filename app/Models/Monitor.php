<?php

namespace App\Models;

use App\Enums\MonitorStatus;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Monitor extends Model
{

    use Notifiable, HasUlids, HasFactory;

    protected $fillable = [
        'url',
        'check_interval',
        'threshold',
        'status',
        'last_checked_at',
        'consecutive_failures',
        'uptime_percentage',
        'notified_down_at',
    ];
    protected $casts = [
        'status' => MonitorStatus::class,
        'last_checked_at' => 'datetime',
        'notified_down_at' => 'datetime',
        'uptime_percentage' => 'float',
    ];


     protected $keyType = 'string';  
    public $incrementing = false; 
  
    public function routeNotificationForMail(): string
    {
        return config('mail.from.address');
    }
    public function checks()
    {
        return $this->hasMany(MonitorCheck::class);
    }

}
