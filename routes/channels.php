<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('chat.{conversationId}', function ($user, $conversationId) {
    // Allow access if user is part of this conversation
    return true; // You can add more specific authorization logic here
});

Broadcast::channel('tukang.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id && $user instanceof \App\Models\Tukang;
});
