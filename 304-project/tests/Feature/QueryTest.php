<?php

namespace Tests\Feature;

use Exception;
use Tests\TestCase;
use App\Http\Controllers\QueryController;
use App\Models\Condition;

class QueryTest extends TestCase
{
    protected static $initialized = false;
    protected $controller;

    protected function setUp(): void
    {
        parent::setUp();
        $this->controller = new QueryController();
        if (!self::$initialized) {
            $this->artisan('app:start')->assertExitCode(0); 
            self::$initialized = true;
        }
    }

    public function test_get_table_names()
    {
        $this->assertEquals(
            ['Region', 'InternalRegion', 'ExternalRegion', 'Country', 'CountryIn', 'Area', 'Residence', 'Household', 'Education', 'Career', 'Person', 'Immigrant', 'Income'], 
            $this->controller->get_table_names()
        );
    }

    public function test_column_names()
    {
        $this->assertEquals(
            ['pid', 'marital_status', 'gender', 'age', 'hid', 'crid', 'eid'],
            $this->controller->get_column_names('Person')
        );
    }

    public function test_projection_all_columns()
    {
        $results = $this->controller->projection('Person', []);
        $this->assertEquals(10000, count($results));
        $this->assertEquals(
            ['pid' => 1, 'marital_status' => 'Married', 'gender' => 'Man+', 'age' => 45, 'hid' => 1, 'crid' => 5488, 'eid' => 1707071],
            get_object_vars($results[0])
        );
    }

    public function test_projection_specific_columns()
    {
        $results = $this->controller->projection('Income', ['net_capital_gains', 'total_income']);
        $this->assertEquals(7957, count($results));
        $this->assertEquals(
            ['net_capital_gains' => null, 'total_income' => 63000],
            get_object_vars($results[20])
        );
    }

    public function test_projection_invalid_column()
    {
        try {
            $this->controller->projection('Income', ['invalid']);
            $this->fail();
        } catch (Exception $e) {
            $this->assertEquals(0, 0);
        }
    }

    public function test_projection_invalid_table()
    {
        try {
            $this->controller->projection('Invalid', []);
            $this->fail();
        } catch (Exception $e) {
            $this->assertEquals(0, 0);
        }
    }

    public function test_insert_and_delete()
    {
        $this->controller->insert('Person', ['gender', 'hid'], [['Woman+', 42]]);
        $results = $this->controller->projection('Person', []);
        $this->assertEquals(10001, count($results));
        $this->assertEquals(
            ['pid' => 10001, 'gender' => 'Woman+', 'hid' => 42, 'crid' => null, 'eid' => null, 'age' => null, 'marital_status' => null], 
            get_object_vars($results[10000])
        );
        
        $this->controller->delete('Person', ['pid' => 10001]);
        $results = $this->controller->projection('Person', []);
        $this->assertEquals(10000, count($results));
    }

    public function test_delete_cascade()
    {
        $this->controller->insert('Person', ['gender', 'hid'], [['Woman+', 42]]);
        $results = $this->controller->projection('Person', []);
        $this->assertEquals(10001, count($results));
        $this->assertEquals(
            ['pid' => 10001, 'gender' => 'Woman+', 'hid' => 42, 'crid' => null, 'eid' => null, 'age' => null, 'marital_status' => null], 
            get_object_vars($results[10000])
        );
        $number_of_incomes = count($this->controller->projection('Income', []));
        $this->controller->insert('Income', ['pid', 'total_income', 'year'], [[10001, 42000, 2021]]);
        $results = $this->controller->projection('Income', []);
        $this->assertEquals($number_of_incomes + 1, count($results));
        
        $this->controller->delete('Person', ['pid' => 10001]);
        $results = $this->controller->projection('Person', []);
        $this->assertEquals(10000, count($results));
        $results = $this->controller->projection('Income', []);
        $this->assertEquals($number_of_incomes, count($results));
    }

    public function test_insert_and_delete_literal()
    {
        $s = '\'\\;';
        $this->controller->insert('Person', ['gender', 'hid'], [[$s, 42]]);
        $results = $this->controller->projection('Person', []);
        $this->assertEquals(10001, count($results));
        $this->assertEquals(
            ['pid' => 10001, 'gender' => $s, 'hid' => 42, 'crid' => null, 'eid' => null, 'age' => null, 'marital_status' => null], 
            get_object_vars($results[10000])
        );
        
        $this->controller->delete('Person', ['pid' => 10001]);
        $results = $this->controller->projection('Person', []);
        $this->assertEquals(10000, count($results));
    }

    public function test_insert_foreign_key_does_not_exist()
    {
        try {
            $this->controller->insert('Person', ['gender', 'hid'], [['Woman+', 10001]]);
            $this->fail();
        } catch (Exception $e) {
            $this->assertEquals(10000, count($this->controller->projection('Person', [])));
        }
    }

    public function test_update()
    {
        $this->controller->update('Person', ['pid' => 666], ['age' => 5, 'hid' => 888]);
        $results = $this->controller->projection('Person', []);
        $this->assertEquals(5, $results[665]->age);
        $this->assertEquals(888, $results[665]->hid);
        $this->controller->update('Person', ['pid' => 666], ['age' => 0, 'hid' => 666]);
        $results = $this->controller->projection('Person', []);
        $this->assertEquals(0, $results[665]->age);
        $this->assertEquals(666, $results[665]->hid);
    }

    public function test_update_invalid_values()
    {
        try {
            $this->controller->update('Person', ['pid' => 666], ['age' => 'beep', 'hid' => 888]);
            $this->fail();
        } catch (Exception $e) {
            $results = $this->controller->projection('Person', []);
            $this->assertEquals(0, $results[665]->age);
            $this->assertEquals(666, $results[665]->hid);
        }
    }

    public function test_update_invalid_key()
    {
        try {
            $this->controller->update('Person', ['pid' => -1], ['age' => 5, 'hid' => 888]);
            $this->fail();
        } catch (Exception $e) {
            $results = $this->controller->projection('Person', []);
            $this->assertEquals(0, $results[665]->age);
            $this->assertEquals(666, $results[665]->hid);
        }
    }

    public function test_selection()
    {
        $results = $this->controller->selection('Person', [], [new Condition('AND', '<', 'age', 5)]);
        $this->assertEquals(553, count($results));
        $this->assertEquals(7, count(get_object_vars($results[0])));
    }

    public function test_selection_columns()
    {
        $results = $this->controller->selection('Person', ['pid', 'hid'], [new Condition('AND', '<', 'age', 5)]);
        $this->assertEquals(553, count($results));
        $this->assertEquals(2, count(get_object_vars($results[0])));
    }
    
    public function test_selection_many()
    {
        $results = $this->controller->selection('Person', [], [
            new Condition('AND', '<=', 'age', 30), 
            new Condition('AND', '>=', 'age', 20), 
            new Condition('AND', '=', 'marital_status', 'Divorced (not living common law)'),
            new Condition('OR', '=', 'marital_status', 'Widowed (not living common law)'),
            new Condition('AND', '<=', 'age', 30), 
            new Condition('AND', '>=', 'age', 20)
        ]);
        $this->assertEquals(8, count($results));
    }

    public function test_selection_not_null()
    {
        $results = $this->controller->selection('Person', [], [
            new Condition('AND', '<=', 'age', 30), 
            new Condition('AND', '>=', 'age', 20), 
            new Condition('AND', '<>', 'crid', null),
            new Condition('AND', '=', 'marital_status', 'Divorced (not living common law)'),
            new Condition('OR', '=', 'marital_status', 'Widowed (not living common law)'),
            new Condition('AND', '<=', 'age', 30), 
            new Condition('AND', '>=', 'age', 20),
            new Condition('AND', '<>', 'crid', null),
        ]);
        $this->assertEquals(6, count($results));
    }

    public function test_selection_null()
    {
        $results = $this->controller->selection('Person', [], [
            new Condition('AND', '<=', 'age', 30), 
            new Condition('AND', '>=', 'age', 20), 
            new Condition('AND', '=', 'crid', null),
            new Condition('AND', '=', 'marital_status', 'Divorced (not living common law)'),
            new Condition('OR', '=', 'marital_status', 'Widowed (not living common law)'),
            new Condition('AND', '<=', 'age', 30), 
            new Condition('AND', '>=', 'age', 20),
            new Condition('AND', '=', 'crid', null),
        ]);
        $this->assertEquals(2, count($results));
    }

    public function test_selection_null_invalid_operator()
    {
        try {
            $this->controller->selection('Person', [], [new Condition('AND', '>=', 'crid', null)]);
            $this->fail();
        } catch (Exception $e) {
            $this->assertEquals(10000, count($this->controller->projection('Person', [])));
        }
    }

    public function test_get_persons_with_capital_gains_over() 
    {
        $results = $this->controller->get_persons_with_capital_gains_over(10000);
        $this->assertEquals(171, count($results));
    }

    public function test_get_average_income_by_occupation() 
    {
        $results = $this->controller->get_average_income_by_occupation();
        $this->assertEquals(26, count($results));
    }
}