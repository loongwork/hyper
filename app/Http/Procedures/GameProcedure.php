<?php

declare(strict_types=1);

namespace App\Http\Procedures;

use Illuminate\Support\Facades\Redis;
use Sajya\Server\Procedure;

class GameProcedure extends Procedure
{
    /**
     * The name of the procedure that will be
     * displayed and taken into account in the search
     */
    public static string $name = 'game';

    /**
     * 列出所有在线玩家
     *
     * @throws \JsonException
     */
    public function listPlayers(): array
    {
        $db = Redis::connection('game');
        $proxies = $db->hkeys('heartbeats');

        $players = [];
        foreach ($proxies as $proxy) {
            $proxy_players_uuid = $db->smembers("proxy:{$proxy}:usersOnline");
            if (empty($proxy_players_uuid)) {
                continue;
            }
            $proxy_players_cache = $db->hmget('uuid-cache', $proxy_players_uuid);
            foreach ($proxy_players_cache as $cache) {
                $cache = json_decode($cache, true, 512, JSON_THROW_ON_ERROR);
                // performance leak
//                $player_server = $db->hget("player:$cache[uuid]", 'server');
                $players[] = [
                    'uuid' => $cache['uuid'],
                    'username' => $cache['name'],
                    'proxy' => $proxy,
                    //                    'server' => $player_server,
                ];
            }
        }

        return $players;
    }
}
