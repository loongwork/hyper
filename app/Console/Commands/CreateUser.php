<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class CreateUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:create';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create an user';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $username = $this->ask('What is the username?');
        $password = $this->secret('What is the password?');
        $nickname = $this->ask('What is the nickname?');
        $qq = $this->ask('What is the qq?');
        $activated = $this->confirm('Is the user activated?');
        $is_member = $this->confirm('Is the user a member?');
        $is_whitelist = $this->confirm('Is the user in the whitelist?');
        $user = new User();
        $data = [
            'username' => $username,
            'password' => $password,
            'nickname' => $nickname ?: null,
            'qq' => $qq ?: null,
            'activated_at' => $activated ? now() : null,
            'become_member_at' => $is_member ? now() : null,
            'whitelisted_at' => $is_whitelist ? now() : null,
        ];
        $user->fill($data);
        $this->info('Please confirm the following information:');
        $this->table(['username', 'password', 'nickname', 'qq', 'activated_at', 'become_member_at', 'whitelisted_at'], [$data]);
        if ($this->confirm('Do you want to create the user?')) {
            $user->save();
            $this->info('User created successfully.');
            $this->info('User ID: ' . $user->id);
        } else {
            $this->info('User creation cancelled.');
        }
        return self::SUCCESS;
    }
}
