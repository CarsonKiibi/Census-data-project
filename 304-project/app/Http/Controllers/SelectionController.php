<?php

namespace App\Http\Controllers;

use App\Models\Condition;
use Illuminate\Http\Request;


class SelectionController extends QueryController
{
    public function selectionSetUp()
    {
        $columns = $this->get_column_names('Person');
        $data = ['columns' => $columns];
        foreach ($columns as $column) {
            $column_values = $this->projection('Person', [$column], true, $column);
            $data[$column] = [];
            foreach($column_values as $value) {
                if ($value->$column !== NULL) {
                    array_push($data[$column], $value->$column);
                }
            }
        }
        return view('pages.selection', $data);
    }

    public function performSelection(Request $request)
    {
        $logics = $request->input('logic');
        $operators = $request->input('operator');
        $values = $request->input('value');

        $columns = [];
        $conditions = [];
        foreach ($logics as $column => $logic) {
            array_push($columns, $column);
            if ($operators[$column] === 'N/A') {
                continue;
            } else if ($operators[$column] === '!=') {
                $operators[$column] = '<>';
            }
            array_push($conditions, new Condition($logic, $operators[$column], $column, $values[$column]));
        }

        $result = $this->selection('Person', $columns, $conditions);
        return view('pages.selection-result', ['result' => $result, 'columns' => $columns]);
    }
}
