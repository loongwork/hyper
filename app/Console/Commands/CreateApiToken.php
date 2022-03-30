<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class CreateApiToken extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'token:create';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new API token';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Creating a new API token...');
        $name = $this->ask('What is the name of the token?');
        $abilities = $this->ask('What abilities does the token have? (comma separated)');
        $abilities = explode(',', $abilities);
        $abilities = array_map('trim', $abilities);
        $token = User::first()->createToken($name, $abilities);
        $this->info('New token created successfully!');
        $this->info('Token: ' . $token->plainTextToken);
        return self::SUCCESS;
    }
}
