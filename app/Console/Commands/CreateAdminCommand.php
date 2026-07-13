<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CreateAdminCommand extends Command
{
    protected $signature = 'elixe:create-admin {email?} {--name=Admin Elixe} {--password=}';

    protected $description = 'Create or update an initial Elixe admin user.';

    public function handle(): int
    {
        $email = $this->argument('email') ?: $this->ask('Email del admin');
        $password = $this->option('password') ?: Str::password(16);

        $user = User::updateOrCreate(
            ['email' => $email],
            [
                'name' => $this->option('name'),
                'password' => Hash::make($password),
            ],
        );

        $this->info(($user->wasRecentlyCreated ? 'Admin creado' : 'Admin actualizado').": {$user->email}");

        if (! $this->option('password')) {
            $this->warn("Password temporal: {$password}");
        }

        return self::SUCCESS;
    }
}
