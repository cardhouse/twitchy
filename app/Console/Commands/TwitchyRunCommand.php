<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

class TwitchyRunCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'twitchy:run {--channel=} {--no-frontend} {--no-relay} {--dry-run}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run the Twitchy stack: IRC relay and frontend dev server concurrently';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $runFrontend = ! $this->option('no-frontend');
        $runRelay = ! $this->option('no-relay');

        $processes = [];

        if ($runRelay) {
            $channel = $this->option('channel') ?? env('TWITCH_CHANNEL');
            $args = ['php', 'artisan', 'twitch:relay'];
            if ($channel) {
                $args[] = "--channel={$channel}";
            }
            if ($this->option('dry-run')) {
                $args[] = '--dry-run';
            }
            $processes['relay'] = new Process($args, base_path());
            $processes['relay']->setTimeout(null);
        }

        if ($runFrontend) {
            // Prefer dev if available; fallback to build preview
            $npm = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN' ? 'npm.cmd' : 'npm';
            $processes['frontend'] = new Process([$npm, 'run', 'dev'], base_path());
            $processes['frontend']->setTimeout(null);
        }

        if (empty($processes)) {
            $this->warn('Nothing to run. Enable at least one process.');

            return self::INVALID;
        }

        foreach ($processes as $name => $proc) {
            $this->info("Starting {$name}...");
            $proc->start();
        }

        // Stream output prefixed by process name
        while (true) {
            $alive = false;
            foreach ($processes as $name => $proc) {
                if ($proc->isRunning()) {
                    $alive = true;
                }
                $out = $proc->getIncrementalOutput();
                if ($out !== '') {
                    foreach (explode("\n", rtrim($out)) as $line) {
                        if ($line !== '') {
                            $this->line("[{$name}] {$line}");
                        }
                    }
                }
                $err = $proc->getIncrementalErrorOutput();
                if ($err !== '') {
                    foreach (explode("\n", rtrim($err)) as $line) {
                        if ($line !== '') {
                            $this->error("[{$name}] {$line}");
                        }
                    }
                }
            }

            if (! $alive) {
                break;
            }

            usleep(100000); // 100ms
        }

        return self::SUCCESS;
    }
}
