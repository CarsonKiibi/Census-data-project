<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PersonController;
use App\Http\Controllers\SelectionController;
use App\Http\Controllers\ProjectionController;
use App\Http\Controllers\DivisionController;
use App\Http\Controllers\GroupByController;
use App\Http\Controllers\JoinController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return View::make('pages.about');
});

Route::get('/about', function () {
    return View::make('pages.about');
});

/* ADD */
Route::get('/add',  [PersonController::class, 'getAddView']);

Route::post('/add', [PersonController::class, 'add']);

/* DELETE */
Route::get('/remove', [PersonController::class, 'getDeleteView']);

Route::post('/remove',  [PersonController::class, 'deletePerson']);

/* UPDATE */
Route::get('/update', [PersonController::class, 'getUpdateView']);

Route::post('/update', [PersonController::class, 'updatePerson']);


// Route::get('/projection', function () {
//     return View::make('pages.projection');
// });

/* PROJECTION */

/*SELECT TABLE*/
Route::get('/projection', [ProjectionController::class, 'projectionSelectTable'])->name('process.projectionselecttable');

Route::get('/projection-columns', [ProjectionController::class, 'projectionSelectColumn'])->name('process.projectionselectcolumn');

Route::get('/projection-result', [ProjectionController::class, 'processProjection'])->name('process.performprojection');


/* SELECTION */
Route::get('/selection', [SelectionController::class, 'selectionSetUp'])->name('process.selectionSetUp');
Route::get('/selection-result', [SelectionController::class, 'performSelection'])->name('process.performSelection');



/* DIVISION */
Route::get('/division', function () {
    return View::make('pages.division');
});
Route::get('/division-result', [DivisionController::class, 'processDivision'])->name('process.division');
/* END DIVISION */



/* GROUP BY */
Route::get('/group-by', function() {
    return View::make('pages.group-by');
});
Route::get('/group-by-result', [GroupByController::class, 'processGroupBy'])->name('process.groupby');
/* END GROUP BY */



/* GROUP BY HAVING */
Route::get('/group-by-having', function() {
    return View::make('pages.group-by-having');
});
Route::get('/group-by-having-result', [GroupByController::class, 'processGroupByHaving'])->name('process.groupbyhaving');
/* END GROUP BY HAVING*/



/* NESTED GROUP BY */
Route::get('/nested-group-by', function() {
    return View::make('pages.nested-group-by');
});
Route::get('/nested-group-by-result', [GroupByController::class, 'processNestedGroupBy'])->name('process.nestedgroupby');
/* END NEST GROUP BY*/



/* JOIN */
Route::get('/join', function () {
    return View::make('pages.join');
});
Route::get('/join-result', [JoinController::class, 'processJoin'])->name('process.join');
/* END JOIN */


