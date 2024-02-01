<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DivisionController extends QueryController
{
    public function processDivision(Request $request) {

        $result = $this->get_careers_with_all_educational_fields();
        return view('pages.division-result', compact('result'));
    }
}