<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class Populate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:populate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Populates database using populate.sql';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $path = __DIR__ . '/../../SQL/populate.sql';
        $sql = file_get_contents($path);
        DB::unprepared($sql);

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
