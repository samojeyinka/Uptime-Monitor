<?php

namespace App\Notifications;

use App\Enums\MonitorStatus;
use App\Models\Monitor;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Queue\SerializesModels;

class MonitorStatusChangedNotification extends Notification implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * Create a new notification instance.
     */
    public function __construct(public Monitor $monitor, public MonitorStatus $newStatus) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {

        $isDown = $this->newStatus === MonitorStatus::DOWN;
        $subject = $isDown ? 'ALERT: '.$this->monitor->url.' is DOWN'
            : 'RESOLVED: '.$this->monitor->url.' is back UP';

        return (new MailMessage)
            ->subject($subject)
            ->line($isDown
                ? 'Your monitored site has gone DOWN.'
                : 'Your monitored site has recovered and is back UP.')
            ->line('URL: '.$this->monitor->url)
            ->line('Detected at: '.now()->toDateTimeString());
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [

            'monitor_id' => $this->monitor->id,
            'url' => $this->monitor->url,
            'status' => $this->newStatus->value,
            'changed_at' => now()->toISOString(),
        ];

    }
}
