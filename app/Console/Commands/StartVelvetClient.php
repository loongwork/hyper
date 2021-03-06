<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Server\VelvetWebsocketServer;
use Illuminate\Console\Command;
use Swlib\SaberGM;
use Swoole\Atomic;
use Swoole\Coroutine;
use Swoole\Coroutine\Channel;
use Swoole\Coroutine\Redis;
use function Swoole\Coroutine\run;

class StartVelvetClient extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'velvet:client';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Start the Velvet client';

    private int $redisCid = -1;

    private int $wsCid = -1;

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $status = new Atomic(0);

        $this->info('Starting the Velvet client...');

        run(function () use ($status) {
            $chan = new Channel(5);

            $this->wsCid = Coroutine::create(function () use ($chan, $status) {
                Coroutine::sleep(1);
                $this->info('Starting the Websocket client...');
                $server = new VelvetWebsocketServer();
                $ws = SaberGM::websocket('ws://echo.websocket.events');
                while ($status->get() === 0) {
                    $data = $chan->pop(0.2);
                    if ($data) {
                        $ws->push($data);
                        $this->info('Sent: ' . $data);
                    }

                    $frame = $ws->recv(0.2);
                    if ($frame === false) {
                        continue;
                    }

                    switch ($frame) {
                        case 'echo.websocket.events sponsored by Lob.com':
                            if ($server->onConnected() === false) {
                                $this->error('Failed to connect to the server');
                                $ws->close();
                                $status->set(-1);
                            } else {
                                $this->info('Connected to the server');
                            }
                            break;
                        default:
                            $server->onReceived($frame);
                            $this->info('Received: ' . $frame);
                    }
                    Coroutine::sleep(0.5);
                }
            });

            $this->redisCid = Coroutine::create(function () use ($chan, $status) {
                Coroutine::sleep(1);
                $this->info('Starting the Redis client...');

                $redis = new Redis();
                $redis->connect('redis', 6379);
                $redis->select(0);
                if ($redis->subscribe(['lwhyper_database_velvet'])) {
                    while ($status->get() === 0 && $msg = $redis->recv()) {
                        // msg???????????????, ??????????????????
                        // $type # ???????????????????????????????????????
                        // $name # ????????????????????? ??? ??????????????????
                        // $info  # ?????????????????????????????? ??? ????????????
                        [$type, $name, $info] = $msg;
                        if ($type === 'subscribe') { // ???psubscribe
                            $this->info("Subscribed to {$name}");
                        // ?????????????????????????????????????????????????????????
                        } elseif ($type === 'unsubscribe' && $info === 0) { // ???punsubscribe
                            break; // ????????????????????????????????????????????????????????????0??????????????????????????????
                        } elseif ($type === 'message') {  // ??????psubscribe????????????pmessage
                            $chan->push($info);
                        }
                        Coroutine::sleep(0.5);
                    }
                }
                $redis->close();
            });
        });
        $status->set(-1);

        return self::SUCCESS;
    }

    /**
     * Write a string as standard output.
     *
     * @param string          $string
     * @param null|string     $style
     * @param null|int|string $verbosity
     */
    public function line($string, $style = null, $verbosity = null): void
    {
        $cid = Coroutine::getCid();
        if ($cid !== -1) {
            if ($cid === $this->wsCid) {
                $string = '<fg=#005A9C>[WS]</> ' . $string;
            } elseif ($cid === $this->redisCid) {
                $string = '<fg=#DC382D>[Redis]</> ' . $string;
            }
        }
        parent::line($string, $style, $verbosity);
    }
}
