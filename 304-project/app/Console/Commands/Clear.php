<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class Clear extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:clear';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Drops all tables in the database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $path = __DIR__ . '/../../SQL/clear.sql';
        $sql = file_get_contents($path);
        DB::unprepared($sql);
        echo "Cleared tables in database." . PHP_EOL;
    }
}
