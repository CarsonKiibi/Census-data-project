<?php

namespace App\Models;

use Exception;

class Formater {
    private static function format_string_value(string $value) {
        $value = str_replace("'", "''", $value);
        $value = "'" . $value . "'";
        return $value;
    }
    
    public static function format_column($column) {
        if (!is_string($column)) {
            throw new Exception("Invalid column name. Column name must be a string.");
        }
        if (!ctype_alnum(str_replace("_", "", $column))) {
            throw new Exception("Invalid column name. Column name must be alphanumeric.");
        }
        return $column;
    }
    
    public static function format_value($value) {
        if (is_string($value)) {
            $value = Formater::format_string_value($value);
        } else if (is_null($value)) {
            $value = 'NULL';
        } else if (!is_integer($value) && !is_float($value)) {
            throw new Exception("Invalid value entry. Must be string, integer, float, or null.");
        }
        return $value;
    }
}