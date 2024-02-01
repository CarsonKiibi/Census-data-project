<?php

namespace App\Http\Controllers;

use App\Models\Formater;
use Illuminate\Support\Facades\DB;
use Exception;

class QueryController extends Controller {
    /**
     * @return string[]
     */
    public function get_table_names(): array {
        $results = DB::select("SELECT name FROM sqlite_schema WHERE type ='table' AND name NOT LIKE 'sqlite_%'");
        $names = [];
        foreach ($results as $result) {
            array_push($names, $result->name);
        }
        return $names;
    }

    private function validate_table($table) {
        if (!in_array($table, $this->get_table_names())) {
            throw new Exception("Invalid table name $table");
        }
    }

    public function get_column_names($table) {
        $this->validate_table($table);
        $results = DB::select("PRAGMA table_info($table)");
        $columns = [];
        foreach ($results as $result) {
            array_push($columns, $result->name);
        }
        return $columns;
    }


    public function projection(string $table, array $columns, $distinct=false, $orderby=NULL) {
        // $table should be a string of the table name
        // $columns should be an array of strings
        //  if $columns is [], return all columns

        $this->validate_table($table);

        if (count($columns) == 0) {
            $columns_str = "*";
        } else {
            $columns_str = "";
            foreach ($columns as $column) {
                if (strlen($columns_str) > 0) {
                    $columns_str .= ", ";
                }
                $column = Formater::format_column($column);
                $columns_str .= $column;
            }
        }
        $query = "SELECT ";
        if ($distinct) {
            $query .= "DISTINCT ";
        }
        $query .= "$columns_str FROM $table";
        if ($orderby !== NULL) {
            $query .= " ORDER BY $orderby";
        }
        return DB::select($query);
    }

    public function insert(string $table, array $columns, array $values) {
        // $table should be a string of the table name
        // $columns should be an array of strings
        //  e.g. ["pid", "age"]
        // $values should be an array of arrays
        //  e.g. [[1,50], [2, 60]]

        $this->validate_table($table);

        $columns_str = "";
        foreach ($columns as $column) {
            if (strlen($columns_str) > 0) {
                $columns_str .= ", ";
            }
            $column = Formater::format_column($column);
            $columns_str .= $column;
        }

        $values_str = "";
        foreach ($values as $value_set) {
            if (strlen($values_str) > 0) {
                $values_str .= "," . PHP_EOL;
            }
            $value_set_str = "(";
            foreach ($value_set as $value) {
                if (strlen($value_set_str) > 1) {
                    $value_set_str .= ", ";
                }
                $value = Formater::format_value($value);
                $value_set_str .= $value;
            }
            $value_set_str .= ")";
            $values_str .= $value_set_str;
        }
        $query = "INSERT INTO $table($columns_str) VALUES $values_str";
        DB::statement($query);
    }

    public function delete(string $table, array $values) {
        // $table should be a string of the table name
        // $values should be an array with column string keys and values for the primary keys
        //  e.g. ["pid" => 1]

        $this->validate_table($table);

        $conditions = "";
        foreach ($values as $column => $value) {
            if (strlen($conditions) > 0) {
                $conditions .= " AND ";
            }
            $value = Formater::format_value($value);
            $column = Formater::format_column($column);
            $conditions .= $column . " = " . $value;
        }
        $query = "DELETE FROM $table WHERE $conditions";
        DB::statement($query);
    }

    public function update(string $table, array $keys, array $to_update) {
        // $table should be a string of the table name
        // $keys should be an array with column string keys and values for all primary key columns
        //  e.g. ["pid" => 1]
        // $to_update should be an array with column string keys and values to update with
        //  e.g. ["age" => 50]
        // Unspecified columns remain unchanged

        $this->validate_table($table);

        $conditions = "";
        foreach ($keys as $column => $value) {
            if (strlen($conditions) > 0) {
                $conditions .= " AND ";
            }
            $value = Formater::format_value($value);
            $column = Formater::format_column($column);
            $conditions .= $column . " = " . $value;
        }

        $update_string = "";
        foreach ($to_update as $column => $value) {
            if (strlen($update_string) > 1) {
                $update_string .= ", ";
            }
            $value = Formater::format_value($value);
            $column = Formater::format_column($column);
            $update_string .= $column . " = " . $value;
        }

        $query = "UPDATE $table SET $update_string WHERE $conditions";
        DB::statement($query);
    }

    public function selection(string $table, array $columns, array $conditions) {
        // $table should be a string of the table name
        // $columns should be an array of strings
        // $conditions should be an array of Condition

        $this->validate_table($table);

        if (count($columns) == 0) {
            $columns_str = "*";
        } else {
            $columns_str = "";
            foreach ($columns as $column) {
                if (strlen($columns_str) > 0) {
                    $columns_str .= ", ";
                }
                $column = Formater::format_column($column);
                $columns_str .= $column;
            }
        }

        $conditions_str = "";
        foreach ($conditions as $condition) {
            // Note that the attributes of $condition are validated upon construction
            if (strlen($conditions_str) > 0) {
                $conditions_str .= " " . $condition->logic . " ";
            }
            if ($condition->value == 'NULL') {
                if ($condition->operator == '=') {
                    $conditions_str .= $condition->column . " IS " . $condition->value;
                } else if ($condition->operator == '<>') {
                    $conditions_str .= $condition->column . " IS NOT " . $condition->value;
                } else {
                    throw new Exception('Invalid operator for NULL');
                }
            } else {
                $conditions_str .= $condition->column . " " . $condition->operator . " " . $condition->value;
            }
        }

        $query = "SELECT $columns_str FROM $table";
        if (strlen($conditions_str) > 0) {
            $query .= " WHERE $conditions_str";
        }
        return DB::select($query);
    }

    // JOIN query
    public function get_persons_with_capital_gains_over($x) {
        $x = Formater::format_value($x);
        $query = 
            "SELECT i.net_capital_gains, p.pid, p.gender, p.age 
            FROM Person p, Income i 
            WHERE p.pid = i.pid AND i.net_capital_gains > $x
            ORDER BY i.net_capital_gains DESC";
        return DB::select($query);
    }

    // GROUP BY query
    public function get_average_income_by_occupation() {
        $query = 
            "SELECT c.occupation, AVG(i.employment_income) as avg_income
            FROM Career c, Person p, Income i
            WHERE p.pid = i.pid AND c.crid = p.crid AND i.year = 2021 AND c.occupation IS NOT NULL AND i.employment_income > 0
            GROUP BY c.occupation
            ORDER BY AVG(i.employment_income) DESC";
        return DB::select($query);
    }

    // GROUP BY HAVING query
    public function get_education_earned_by_more_than($x) {
        $x = Formater::format_value($x);
        $query = 
            "SELECT e.field_of_study, e.highest_attainment, COUNT(*) as count
            FROM Education e, Person p
            WHERE e.eid = p.eid
            GROUP BY e.field_of_study, e.highest_attainment
            HAVING COUNT(*) > $x
            ORDER BY COUNT(*) DESC";
        return DB::select($query);
    }

    // Nested GROUP BY 
    public function get_average_x_bedroom_rents_in_areas_more_expensive_than_average($x) {
        $x = Formater::format_value($x);
        $query = 
            "SELECT a.aname, p.rname, AVG(r.cost) as avg_cost
            FROM Area a, Residence r, Household h, Region p
            WHERE p.rid = a.rid AND h.rsid = r.rsid AND r.aid = a.aid AND h.ownership = 'Renter' AND r.bedrooms = $x
            GROUP BY a.aid
            HAVING COUNT(*) > 5 AND AVG(r.cost) > (
                SELECT AVG(r2.cost)
                FROM Residence r2, Household h2
                WHERE h2.rsid = r2.rsid AND h2.ownership = 'Renter' AND r2.bedrooms = $x
            )
            ORDER BY AVG(r.cost) DESC";
        return DB::select($query);
    }

    // DIVISION query
    public function get_careers_with_all_educational_fields() {
        $query =
            "SELECT c.crid, c.industry, c.occupation
            FROM Career c
            WHERE NOT EXISTS (
                SELECT DISTINCT e1.field_of_study FROM Education e1
                EXCEPT
                SELECT DISTINCT e2.field_of_study FROM Education e2, Career c2, Person p
                WHERE p.crid = c2.crid AND p.eid = e2.eid AND c2.crid = c.crid
            )";
        return DB::select($query);
    }


}