<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GroupByController extends QueryController
{
    // need to do routing
    public function processGroupBy(Request $request) {

        // doesnt take in variables
        $result = $this->get_average_income_by_occupation();
        return view('pages.group-by-result', ['result' => $result]);
    }

    public function processGroupByHaving(Request $request) {
        $x = (int)$request->input('x');
        $result = $this->get_education_earned_by_more_than($x);
        return view('pages.group-by-having-result', ['result' => $result]);
    }

    public function processNestedGroupBy(Request $request) {
        $x = (int)$request->input('x');
        $result = $this->get_average_x_bedroom_rents_in_areas_more_expensive_than_average($x);
        return view('pages.nested-group-by-result', ['result' => $result]);
    }
}