<?php

/** @noinspection ALL */

namespace App\Console\Commands;

use App\Messages\Actions\CreateMessage;
use App\Messages\Processors\TwitchMessageProcessor;
use App\Models\Chatroom;
use App\Models\Message;
use App\Models\Messages\PingMessage;
use App\Models\Messages\PrivateMessage;
use App\Models\Messages\UnknownMessage;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Stringable;
use Throwable;

class TwitchRelayCommand extends Command
{
    protected $signature = 'twitch:relay {--channel=} {--dry-run}';

    protected $description = 'Connect to Twitch IRC and relay chat messages into the app';

    protected $socket;

    /**
     * Execute the console command.
     */
    public function handle(TwitchMessageProcessor $processor): int
    {
        $channel = $this->option('channel') ?? config('services.twitch.channel');
        $chatroom = Chatroom::firstOrCreate(['name' => $channel]);

        try {
            $this->connect($chatroom->name);

            while ($this->isRunning()) {
                $message = $processor->process($this->getNextLine());

                match ($message::class) {
                    PingMessage::class => $this->send($message->getResponse()),
                    UnknownMessage::class => $this->line("[DEBUG] Unknown message: {$message->message}"),
                    PrivateMessage::class => $this->handlePrivateMessage($message, $chatroom),
                };
            }
        } catch (Throwable $e) {
            $this->fail($e->getMessage());
        } finally {
            $this->disconnect();
        }

        return self::SUCCESS;
    }

    private function handlePrivateMessage(PrivateMessage $message, Chatroom $chatroom): void
    {
        $this->line("<{$message->displayName}> {$message->content}");

        $chatMessage = new Message([
            'username' => $message->username,
            'display_name' => $message->displayName,
            'badges' => $message->badges,
            'message' => $message->content,
            'platform' => 'twitch',
            'timestamp' => Carbon::now()->toISOString(),
        ]);

        if (! $this->option('dry-run')) {
            app(CreateMessage::class)->handle($chatroom, $chatMessage);
        }
    }

    /**
     * @throws Throwable
     */
    protected function connect(string $channel): void
    {
        $host = config('services.twitch.irc_host');
        $port = (int) config('services.twitch.irc_port');

        $this->info("[twitch:relay] connecting to {$host}:{$port}, channel #{$channel}");

        $socket = @stream_socket_client("tcp://{$host}:{$port}", $errno, $errstr, 30);
        if (! $socket) {
            $this->fail("Failed to connect: {$errstr} ({$errno})");
        }

        $this->socket = $socket;
        stream_set_timeout($this->socket, 10);

        // Request tags & membership for badges/display names
        $this->send('CAP REQ :twitch.tv/tags twitch.tv/commands twitch.tv/membership');
        $this->send('PASS '.config('services.twitch.oauth'));
        $this->send('NICK '.config('services.twitch.nick'));
        $this->send("JOIN #{$channel}");
    }

    protected function send(string $line): void
    {
        fwrite($this->socket, $line."\r\n");
    }

    private function isRunning(): bool
    {
        return ! feof($this->socket);
    }

    private function getNextLine(): Stringable
    {
        return str(fgets($this->socket))->trim();
    }

    private function disconnect(): void
    {
        fclose($this->socket);
    }
}
