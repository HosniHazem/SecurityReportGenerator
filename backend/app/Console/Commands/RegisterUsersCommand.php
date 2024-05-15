<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class RegisterUsersCommand extends Command
{
    protected $signature = 'users:register';

    protected $description = 'Register users';

    public function handle()
    {
        // Your register function logic here
        $this->call('register');
    }
}
