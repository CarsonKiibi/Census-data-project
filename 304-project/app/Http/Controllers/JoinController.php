<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class JoinController extends QueryController
{
    public function processJoin(Request $request) {
        $x = (int)$request->input('x');
        $result = $this->get_persons_with_capital_gains_over($x);
        return view('pages.join-result', compact('result'));
    }
}