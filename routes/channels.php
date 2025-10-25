<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('events.{eventId}', function ($user, $eventId) {
    return [
        'id' => $user->id,
        'name' => $user->name
    ];
});
