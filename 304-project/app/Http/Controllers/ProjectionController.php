<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
//use Illuminate\Support\Facades\DB;
use App\Resources\queries;


class ProjectionController extends QueryController
{
   // option 
    // public function showForm() {

    //         $tables = DB::connection('sqlite')->getDoctrineSchemaManager()->listTableNames();
    //         return view('projection', compact('tables'));

    //     return view('projection', compact('tables'));
    // }
    //protected string $table_name;

    public function projectionSelectTable(Request $request) {
        $tables = $this->get_table_names();
        return view('pages.projection', compact('tables'));
    }

    public function projectionSelectColumn(Request $request) {
        $table_name = (string)$request->input('table-name');
        $columns = $this->get_column_names($table_name);
        return view('pages.projection-columns', ['columns' => $columns, 'table_name' => $table_name]);
    }

    public function processProjection(Request $request) {
        //$selected_table = (string)$request->input('selected-table');
        $table_name = (string)$request->input('table_name');
        $columns = $request->input('cols');
        if ($columns == NULL) {
            return redirect()->back()->with('error', 'Please check at least 1 box.');
        }
        //dd(array_keys($columns));
        // $col_str = implode(', ', $columns);
        $result = $this->projection($table_name, array_keys($columns));
        return view('pages.projection-result', ['result' => $result, 'columns' => $columns, 'table_name' => $table_name]);
    }
}
