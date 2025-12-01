<?php

namespace App\Observers;

use App\Models\User;
use App\Events\UserUpdated;
use App\Events\AdminNotification;

class UserObserver
{
    public function created(User $user): void
    {
        // Broadcast event for real-time updates
        broadcast(new UserUpdated($user, 'created'));

        // Send admin notification
        broadcast(new AdminNotification(
            'User Baru Terdaftar',
            "User baru '{$user->name}' dengan role '{$user->role}' telah terdaftar dalam sistem.",
            'success',
            ['user_id' => $user->id, 'action' => 'created']
        ));
    }

    public function updated(User $user): void
    {
        // Broadcast event for real-time updates
        broadcast(new UserUpdated($user, 'updated'));

        // Check for role changes
        if ($user->wasChanged('role')) {
            $originalRole = $user->getOriginal('role');
            $newRole = $user->role;
            
            broadcast(new AdminNotification(
                'Role User Berubah',
                "Role user '{$user->name}' berubah dari '{$originalRole}' menjadi '{$newRole}'.",
                'info',
                ['user_id' => $user->id, 'action' => 'role_changed', 'role' => $newRole]
            ));
        }
    }

    public function deleted(User $user): void
    {
        // Broadcast event for real-time updates
        broadcast(new UserUpdated($user, 'deleted'));

        // Send admin notification
        broadcast(new AdminNotification(
            'User Dihapus',
            "User '{$user->name}' telah dihapus dari sistem.",
            'warning',
            ['user_id' => $user->id, 'action' => 'deleted']
        ));
    }
}
