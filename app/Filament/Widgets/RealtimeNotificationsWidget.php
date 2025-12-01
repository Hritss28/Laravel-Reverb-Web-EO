<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use Livewire\Attributes\On;

class RealtimeNotificationsWidget extends Widget
{
    protected static string $view = 'filament.widgets.realtime-notifications';
    
    protected static ?int $sort = 0;
    
    protected int | string | array $columnSpan = 'full';

    public array $notifications = [];
    public int $maxNotifications = 5;

    #[On('echo-private:admin-notifications,admin.notification')]
    public function handleNotification($event): void
    {
        // Add new notification to the beginning of the array
        array_unshift($this->notifications, [
            'id' => $event['id'] ?? uniqid(),
            'title' => $event['title'] ?? 'Notifikasi',
            'message' => $event['message'] ?? '',
            'type' => $event['type'] ?? 'info',
            'data' => $event['data'] ?? [],
            'timestamp' => $event['timestamp'] ?? now()->format('Y-m-d H:i:s'),
            'created_at' => now(),
            'read' => false
        ]);

        // Keep only the latest notifications
        $this->notifications = array_slice($this->notifications, 0, $this->maxNotifications);

        // Auto-remove notification after 10 seconds
        $this->dispatch('show-notification', $this->notifications[0]);
    }

    public function markAsRead($notificationId): void
    {
        foreach ($this->notifications as &$notification) {
            if ($notification['id'] === $notificationId) {
                $notification['read'] = true;
                break;
            }
        }
    }

    public function removeNotification($notificationId): void
    {
        $this->notifications = array_filter($this->notifications, function ($notification) use ($notificationId) {
            return $notification['id'] !== $notificationId;
        });
    }

    public function clearAllNotifications(): void
    {
        $this->notifications = [];
    }

    public function getUnreadCount(): int
    {
        return count(array_filter($this->notifications, function ($notification) {
            return !$notification['read'];
        }));
    }
}
