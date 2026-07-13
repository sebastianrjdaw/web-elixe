<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DebugEnvironmentCommand extends Command
{
    protected $signature = 'elixe:debug-env';

    protected $description = 'Print non-secret runtime environment details for local troubleshooting.';

    public function handle(): int
    {
        $this->line('APP_ENV='.config('app.env'));
        $this->line('APP_URL='.config('app.url'));
        $this->line('DB_CONNECTION='.config('database.default'));
        $this->line('DB_HOST='.config('database.connections.mysql.host'));
        $this->line('DB_PORT='.config('database.connections.mysql.port'));
        $this->line('DB_DATABASE='.config('database.connections.mysql.database'));
        $this->line('SESSION_DRIVER='.config('session.driver'));
        $this->line('CURRENT_DATABASE='.DB::selectOne('select database() as db')->db);
        $this->line('USER_COUNT='.DB::table('users')->count());

        return self::SUCCESS;
    }
}
