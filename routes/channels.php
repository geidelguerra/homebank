<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('users.{id}', function ($user, $id) {
    if ((int) $user->id === (int) $id) {
        return ['id' => $user->id, 'name' => $user->name, 'ably-capability' => ["subscribe"]];
    }

    return false;
});
