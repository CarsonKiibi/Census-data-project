<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class Initialize extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:initialize';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Initializes application database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $path = __DIR__ . '/../../SQL/initialize.sql';
        $sql = file_get_contents($path);
        DB::unprepared($sql);
        print_r(DB::select("SELECT name FROM sqlite_schema WHERE type='table' ORDER BY name"));
    }
}
