<?php

declare(strict_types=1);

namespace App\Server;

use Illuminate\Support\Facades\Redis;
use Swlib\Saber\WebSocketFrame;

class VelvetWebsocketServer
{
    /**
     * Listen to the connected event
     */
    public function onConnected(): bool
    {
        $this->push('good morning');
        return true;
    }

    /**
     * Listen to the disconnected event
     */
    public function onDisconnected(): void
    {
    }

    /**
     * Listen to the message event
     */
    public function onReceived(WebSocketFrame $data): void
    {
        $this->push($data->getData());
    }

    /**
     * Push data to server
     */
    private function push(string $data): void
    {
        Redis::publish('velvet', $data);
    }
}
