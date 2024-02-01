<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class Generate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Populates database with data stored on disk';

    private $data_path = __DIR__ . "/../../../data/";
    private $initialize_path = __DIR__ . "/../../SQL/initialize.sql";
    private $populate_path = __DIR__ . "/../../SQL/populate.sql";
    private $start_path = __DIR__ . "/../../SQL/start.sql";

    private $entries_num = 10000;

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $script = "";
        $career_industry = $this->get_pairs_from_csv("career_industry");
        $career_occupation = $this->get_pairs_from_csv("career_occupation");
        $cma_density = $this->get_pairs_from_csv("cma_density");
        $cma = $this->get_pairs_from_csv("cma");
        $education_attainment = $this->get_pairs_from_csv("education_attainment");
        $education_attendance = $this->get_pairs_from_csv("education_attendance");
        $education_field_of_study = $this->get_pairs_from_csv("education_field_of_study");
        $education_location = $this->get_pairs_from_csv("education_location");
        $household_ownership = $this->get_pairs_from_csv("household_ownership");
        $household_type = $this->get_pairs_from_csv("household_type");
        $immigrant_age_at_immigration = $this->get_pairs_from_csv("immigrant_age_at_immigration");
        $immigrant_status = $this->get_pairs_from_csv("immigrant_status");
        $immigrant_year_of_immigration = $this->get_pairs_from_csv("immigrant_year_of_immigration");
        $person_age = $this->get_pairs_from_csv("person_age");
        $person_gender = $this->get_pairs_from_csv("person_gender");
        $person_marital_status = $this->get_pairs_from_csv("person_marital_status");
        $residence_type = $this->get_pairs_from_csv("residence_type");
        $script .= $this->populate_external_region();
        $script .= $this->populate_internal_region();
        $script .= $this->populate_country();

        $added_crid = [];
        $added_eid = [];
        $added_aid = [];
        $career_rows = "";
        $education_rows = "";
        $area_rows = "";
        $person_rows = "";
        $residence_rows = "";
        $household_rows = "";
        $immigrant_rows = "";
        $income_rows = "";

        $file = fopen('zip://' . $this->data_path . 'data_donnees_2021_ind.zip#data_donnees_2021_ind.csv', 'r');
        if (!$file){
            echo "File not found";
        }
        $data = fgetcsv($file, 1000, ",");
        for ($i = 0; $i < $this->entries_num; $i++) {
            $data = fgetcsv($file, 1000, ",");

            $pid = $data[0];

            $crid = $data[84] * 100 + $data[85];
            if (!in_array($crid, $added_crid)) {
                $industry = $career_industry[$data[84]];
                $occupation = $career_occupation[$data[85]];
                if ($this->nullable([$industry, $occupation])) {
                    $crid = 'null';
                } else {
                    array_push($added_crid, $crid);
                    $career_rows .= "\n($crid, '$industry', '$occupation'),";
                }
            }

            $eid = $data[4] + $data[11] * 10 + $data[41] * 1000 + $data[64] * 100000;
            if (!in_array($eid, $added_eid)) {
                $field_of_study = $education_field_of_study[$data[11]];
                if ($data[11] == 13) {
                    $field_of_study = 'null';
                } 
                $attending = $education_attendance[$data[4]];
                $attainment = str_replace("'", "''", $education_attainment[$data[41]]);
                $erid = $education_location[$data[64]];
                if ($this->nullable([$field_of_study, $attainment, $attending, $erid])) {
                    $eid = 'null';
                } else {
                    array_push($added_eid, $eid);
                    $education_rows .= "\n($eid, '$field_of_study', '$attending', '$attainment', $erid),";
                }
            }

            $rid = $data[100];
            $aid = $data[13] + $rid * 1000;
            if (!in_array($aid, $added_aid)) {
                $aname = $cma[$data[13]];
                if (!array_key_exists($aname, $cma_density)) {
                    $population_density = 'null';
                } else {
                    $population_density = $cma_density[$aname];
                }

                array_push($added_aid, $aid);
                $area_rows .= "\n($aid, '$aname', $population_density, $rid),";
            }

            $rsid = $pid;
            $rtype = $residence_type[$data[25]];
            $bedrooms = $data[6];
            if ($bedrooms == 8) {
                $bedrooms = 'NULL';
            }
            $rooms = $data[112];
            $cost = $data[114];
            $residence_rows .= "\n($rsid, '$rtype', $rooms, $bedrooms, $cost, $aid),";

            $hid = $pid;
            $htype = $household_type[$data[46]];
            $size = $data[45];
            $ownership = $household_ownership[$data[118]];
            $household_rows .= "\n($hid, '$htype', $size, $rsid, '$ownership'),";

            $age = $person_age[$data[2]];
            $gender = $person_gender[$data[36]];
            $marital_status = $person_marital_status[$data[80]];
            $person_rows .= "\n($pid, '$marital_status', '$gender', $age, $hid, $crid, $eid),";

            $irid = $data[96];
            if ($irid == 1 || $irid == 88) {
                $irid = 'null';
            } else {
                $irid += 100;
                $age_at_immigration = $immigrant_age_at_immigration[$data[3]];
                $status = $immigrant_status[$data[54]];
                $year_of_immigration = $immigrant_year_of_immigration[$data[126]];
                $immigrant_rows .= "\n($pid, $irid, $age_at_immigration, $year_of_immigration, '$status'),";
            }

            $year = 2021;
            $total_income = $this->set_null_if_not_available($data[119]);
            $employment_income = $this->set_null_if_not_available($data[33]);
            $investment_income = $this->set_null_if_not_available($data[56]);
            $capital_gains = $this->set_null_if_not_available($data[18]);
            $income_tax = $this->set_null_if_not_available($data[55]);
            $government_transfers = $this->set_null_if_not_available($data[39]);
            if ($total_income != 'null' || $employment_income != 'null' || $investment_income != 'null' || $income_tax != 'null' || $government_transfers != 'null' || $capital_gains != 'null') {
                $income_rows .= "\n($pid, $year, $total_income, $investment_income, $capital_gains, $government_transfers, $employment_income, $income_tax),";
            }
        }
        fclose($file);
        
        $career_rows = $this->format_rows($career_rows);
        $education_rows = $this->format_rows($education_rows);
        $area_rows = $this->format_rows($area_rows);
        $residence_rows = $this->format_rows($residence_rows);
        $household_rows = $this->format_rows($household_rows);
        $person_rows = $this->format_rows($person_rows);
        $immigrant_rows = $this->format_rows($immigrant_rows);
        $income_rows = $this->format_rows($income_rows);
        $script .= "INSERT INTO Career(crid, industry, occupation) VALUES $career_rows";
        $script .= "INSERT INTO Education(eid, field_of_study, attending, highest_attainment, rid) VALUES $education_rows";
        $script .= "INSERT INTO Area(aid, aname, population_density, rid) VALUES $area_rows";
        $script .= "INSERT INTO Residence(rsid, rtype, rooms, bedrooms, cost, aid) VALUES $residence_rows";
        $script .= "INSERT INTO Household(hid, htype, size, rsid, ownership) VALUES $household_rows";
        $script .= "INSERT INTO Person(pid, marital_status, gender, age, hid, crid, eid) VALUES $person_rows";
        $script .= "INSERT INTO Immigrant(pid, rid, age_at_immigration, year_of_immigration, status) VALUES $immigrant_rows";
        $script .= "INSERT INTO Income(pid, year, total_income, investment_income, net_capital_gains, government_transfers, employment_income, income_tax) VALUES $income_rows";

        file_put_contents($this->populate_path, $script);
        echo "Generated populate.sql" . PHP_EOL;
        $script = file_get_contents($this->initialize_path) . PHP_EOL . PHP_EOL . $script;
        file_put_contents($this->start_path, $script);
        echo "Generated start.sql" . PHP_EOL;

    }

    private function nullable($array) {
        foreach ($array as $value) {
            if ($value != 'Not available' && $value != 'Not applicable') {
                return false;
            }
        }
        return true;
    }

    private function set_null_if_not_available($n) {
        if ($n == 88888888 || $n == 99999999) {
            return 'null';
        }
        return $n;
    }

    private function get_pairs_from_csv($filename)
    {
        $map = [];
        $file = fopen($this->data_path . $filename . ".csv", "r");
        while (($data = fgetcsv($file, 1000, ",")) !== FALSE) {
            if (count($data) != 2) throw new \Exception($filename . " has " . count($data) . " columns instead of 2.");
            $map[$data[0]] = $data[1];
        }
        
        fclose($file);
        return $map;
    }

    private function populate_country()
    {
        $country_file = fopen($this->data_path . "country.csv", "r");
        $country_rows = "";
        while (($data = fgetcsv($country_file, 1000, ",")) !== FALSE) {
            $cid = $data[0];
            $country_name = $data[1];
            $country_rows .= "\n($cid, '$country_name'),";
        }
        fclose($country_file);

        $country_in_file = fopen($this->data_path . "country_in.csv", "r");
        $country_in_rows = "";
        while (($data = fgetcsv($country_in_file, 1000, ",")) !== FALSE) {
            $rid = $data[0] + 100;
            $cid = $data[1];
            $country_in_rows .= "\n($rid, $cid),";
        }
        fclose($country_in_file);

        $country_rows = $this->format_rows($country_rows);
        $country_in_rows = $this->format_rows($country_in_rows);
        $statements = "";
        $statements .= "INSERT INTO Country(cid, cname) VALUES $country_rows";
        $statements .= "INSERT INTO CountryIn(rid, cid) VALUES $country_in_rows";
        return $statements;
    }

    private function populate_external_region()
    {
        $file = fopen($this->data_path . "external_region.csv", "r");
        $region_rows = "";
        $external_rows = "";
        while (($data = fgetcsv($file, 1000, ",")) !== FALSE) {
            $rid = $data[0] + 100;
            $region_rows .= "\n($rid, '$data[1]', $data[3]),";
            $external_rows .= "\n($rid, $data[2]),";
        }
        fclose($file);

        $region_rows = $this->format_rows($region_rows);
        $external_rows = $this->format_rows($external_rows);
        $statements = "";
        $statements .= "INSERT INTO Region(rid, rname, gdp_per_capita) VALUES $region_rows";
        $statements .= "INSERT INTO ExternalRegion(rid, distance) VALUES $external_rows";
        return $statements;
    }

    private function populate_internal_region()
    {
        $file = fopen($this->data_path . "internal_region.csv", "r");
        $region_rows = "";
        $internal_rows = "";
        while (($data = fgetcsv($file, 1000, ",")) !== FALSE) {
            $rid = $data[0];
            $name = $data[1];
            $gdp_goods = round(str_replace(',', '', $data[2]));
            $gdp_services = round(str_replace(',', '', $data[3])); 
            $gdp_per_capita = round(str_replace(',', '', $data[4]));
            $region_rows .= "\n($rid, '$name', $gdp_per_capita),";
            $internal_rows .= "\n($rid, $gdp_goods, $gdp_services),";
        }
        fclose($file);

        $region_rows = $this->format_rows($region_rows);
        $internal_rows = $this->format_rows($internal_rows);
        $statements = "";
        $statements .= "INSERT INTO Region(rid, rname, gdp_per_capita) VALUES $region_rows";
        $statements .= "INSERT INTO InternalRegion(rid, gdp_from_goods, gdp_from_services) VALUES $internal_rows";
        return $statements;
    }

    private function format_rows($rows) {
        $rows = str_replace("Not applicable", 'null', $rows);
        $rows = str_replace("Not available", 'null', $rows);
        $rows = str_replace("'null'", 'null', $rows);
        return substr($rows, 0, -1) . ";" . PHP_EOL . PHP_EOL;
    }
}
