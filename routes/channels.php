<?php

use Illuminate\Support\Facades\Broadcast;
use App\Models\Chat;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// Chat channels
Broadcast::channel('chat.{chatId}', function ($user, $chatId) {
    // Check if user is part of the chat
    $chat = Chat::find($chatId);
    
    if (!$chat) {
        return false;
    }
    
    return $chat->users()->where('user_id', $user->id)->exists();
});

// Admin presence channel
Broadcast::channel('admin-presence', function ($user) {
    if ($user->role === 'admin') {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'role' => $user->role
        ];
    }
    return false;
});

// Real-time updates channels
Broadcast::channel('barang-updates', function ($user) {
    // All authenticated users can listen to barang updates
    return true;
});

Broadcast::channel('peminjaman-updates', function ($user) {
    // All authenticated users can listen to peminjaman updates
    return true;
});

Broadcast::channel('user-updates', function ($user) {
    // Only admins can listen to user updates
    return $user->role === 'admin';
});

Broadcast::channel('kategori-updates', function ($user) {
    // All authenticated users can listen to kategori updates
    return true;
});

// Admin notifications - only for admins
Broadcast::channel('admin-notifications', function ($user) {
    return $user->role === 'admin' ? [
        'id' => $user->id,
        'name' => $user->name,
        'role' => $user->role
    ] : false;
});

// Private user notifications
Broadcast::channel('user.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});
