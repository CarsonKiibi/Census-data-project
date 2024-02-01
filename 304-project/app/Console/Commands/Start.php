<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class Start extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:start';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Initializes and populates application database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $path = __DIR__ . '/../../SQL/start.sql';
        $sql = file_get_contents($path);
        DB::unprepared($sql);
        print_r(DB::select("SELECT name FROM sqlite_schema WHERE type='table' ORDER BY name"));
        $this->print_table_count("Region");
        $this->print_table_count("InternalRegion");
        $this->print_table_count("ExternalRegion");
        $this->print_table_count("Country");
        $this->print_table_count("CountryIn");
        $this->print_table_count("Career");
        $this->print_table_count("Education");
        $this->print_table_count("Area");
        $this->print_table_count("Residence");
        $this->print_table_count("Household");
        $this->print_table_count("Person");
        $this->print_table_count("Immigrant");
        $this->print_table_count("Income");
    }

    private function print_table_count($table) {
        echo $table . " count: " . DB::scalar("SELECT COUNT(*) FROM $table") . PHP_EOL;
    }
}
