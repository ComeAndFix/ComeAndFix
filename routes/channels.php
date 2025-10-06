<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('chat.{conversationId}', function ($user, $conversationId) {
    // Allow access if user is part of this conversation
    return true; // You can add more specific authorization logic here
});
