<?php

namespace App\Helpers;

use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

class SocketIo
{
    public static function trigger($event, $data, $room = '')
    {
        if (!$room) $room = static::publicRoom();
        $redisChannel = env('SOCKET_IO_CHANNEL', 'socket-io');
        return Redis::publish($redisChannel, json_encode([
            'event' => $event,
            'data' => $data,
            'room' => $room,
        ]));
    }

    public static function forUser($event, $data, User $user = null)
    {
        if (!$user) {
            Log::warning("Tried to trigger socket event for not logged in user: "
                . "\nEvent: " . json_encode($event)
                . "\nData: " . json_encode($data)
            );
            return 0;
        }
        $room = env('SOCKET_IO_USER_ROOM') . "-" . $user->id;

        return static::trigger($event, $data, $room);
    }

    public static function forCurrentUser($event, $data)
    {
        return static::forUser($event, $data, User::current());
    }

    public static function publicRoom()
    {
        return env('SOCKET_IO_PUBLIC_ROOM');
    }
}
