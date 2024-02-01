<?php

namespace App\Models;

use Exception;

class Condition {
    public string $logic;       // one of "AND" or "OR", ignored for first 
    public string $operator;    // one of ">", "<", ">=", "<=", "=", "<>"
    public string $column;      // column to compare with value
    public $value;              // value to compare to

    public function __construct($logic, $operator, $column, $value) {
        $this->logic = $logic;
        $this->operator = $operator;
        $this->column = $column;
        $this->value = $value;
        $this->validate();
    }

    private function validate() {
        if (!in_array($this->logic, array("AND", "OR"))) {
            throw new Exception("Invalid logic operator. Must be one of AND or OR");
        }
        if (!in_array($this->operator, array(">", "<", "<=", ">=", "=", "<>"))) {
            throw new Exception("Invalid comparison operator. Must be one of >, <, <=, >=, =, or <>");
        }
        $this->column = Formater::format_column($this->column);
        $this->value = Formater::format_value($this->value);
    }
}