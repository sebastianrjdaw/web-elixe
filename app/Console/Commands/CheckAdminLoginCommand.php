<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class CheckAdminLoginCommand extends Command
{
    protected $signature = 'elixe:check-admin-login {email} {password}';

    protected $description = 'Check whether an admin email/password pair is valid without printing secrets.';

    public function handle(): int
    {
        $email = (string) $this->argument('email');
        $password = (string) $this->argument('password');
        $user = User::where('email', $email)->first();

        if (! $user) {
            $this->error('Admin user not found.');

            return self::FAILURE;
        }

        if (! Hash::check($password, $user->password)) {
            $this->error('Password does not match stored hash.');

            return self::FAILURE;
        }

        if (! Auth::attempt(['email' => $email, 'password' => $password])) {
            $this->error('Auth provider rejected the credentials.');

            return self::FAILURE;
        }

        $this->info('Admin credentials are valid.');

        return self::SUCCESS;
    }
}
