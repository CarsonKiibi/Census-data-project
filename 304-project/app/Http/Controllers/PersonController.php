<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Mockery\Exception;
use Throwable;

class PersonController extends QueryController
{
    public function getUpdateView(): \Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\Foundation\Application
    {
        $hids = $this->getHids();
        $crids = $this->getCrids();
        $pids = $this->getPids();
        $eids = $this->getEids();
        $data = ['hids'=>$hids, 'crids'=>$crids, 'pids'=>$pids, 'eids'=>$eids];
        return view('pages.update',$data);
    }

    public function updatePerson(): \Illuminate\Http\RedirectResponse
    {
        $request = request();
        $pids = $this->getPids();
        $hids = $this->getHids();
        $crids = $this->getCrids();
        $eids = $this->getEids();
        $validated =$request->validate([
            'pid' => ['required', Rule::in($pids)],
            'hid' => ['required', Rule::in($hids)],
            'crid' => ['nullable', Rule::in($crids)],
            'eid' => ['nullable', Rule::in($eids)]
        ]);
        $table = 'Person';
        $collection = $request->collect()->forget('_token');
        $collection->transform(function ($value, $key) {
            if ($value == null) {
                return $value;
            }
            if ($key=='pid' || $key=='age' || $key=='hid' || $key=='crid' || $key=='eid' ) {
                return intval($value);
            }
            return $value;
        });
        $keys = $collection->filter(function ($value, $key) {
            return $key == 'pid';
        })->toArray();
        $collection= $collection->filter(function ($value, $key) {
            return $key != 'pid';
        });
        $array = $collection->toArray();
        $this->update($table, $keys, $array);
        return redirect()->back()->with('message',"Successfully updated");
    }
    public function getAddView(): \Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\Foundation\Application
    {
        $hids = $this->getHids();
        $crids = $this->getCrids();
        $pids = $this->getPids();
        $eids = $this->getEids();
        $data = ['hids'=>$hids, 'crids'=>$crids, 'pids'=>$pids, 'eids'=>$eids];
        return view('pages.add',$data);
    }
    public function add(): \Illuminate\Http\RedirectResponse
    {
        $request = request();
        $pids = $this->getPids();
        $hids = $this->getHids();
        $crids = $this->getCrids();
        $eids = $this->getEids();
        $validated =$request->validate([
            'pid' => ['nullable', Rule::notIn($pids)],
            'hid' => ['required', Rule::in($hids)],
            'crid' => ['nullable', Rule::in($crids)],
            'eid' => ['nullable', Rule::in($eids)]
        ]);
        $table = 'Person';
        $collection = $request->collect()->forget('_token');
        $collection->transform(function ($value, $key) {
            if ($key=='pid' || $key=='age' || $key=='hid' || $key=='crid' || $key=='eid') {
                return intval($value);
            }
            return $value;
        });
        $collection= $collection->filter(function ($value, $key) {
            return $value != null;
        });
        $columns = $collection->keys()->toArray();
        $values = [$collection->values()->toArray()];
        try {
            $this->insert($table, $columns, $values);
        } catch (Exception $caught) {
            abort(404, $caught);
        }
        return redirect()->back()->with('message',"Successfully added");
    }

    private function getPids() : array
    {
        $response = $this->projection('Person', ['pid']);
        $pids = [];
        foreach ($response as $value) {
            array_push($pids, $value->pid);
        }
        return $pids;
    }

    private function getHids() : array
    {
        $response = $this->projection('Household', ['hid']);
        $hids = [];
        foreach ($response as $value) {
            array_push($hids, $value->hid);
        }
        return $hids;
    }

    private function getCrids() : array
    {
        $response = $this->projection('Career', ['crid']);
        $crids = [];
        foreach ($response as $value) {
            array_push($crids, $value->crid);
        }
        return $crids;
    }

    private function getEids() : array
    {
        $response = $this->projection('Education', ['eid']);
        $eids = [];
        foreach ($response as $value) {
            array_push($eids, $value->eid);
        }
        return $eids;
    }

    public function getDeleteView(): \Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\Foundation\Application
    {
        $pids = $this->getPids();
        $data = ['pids'=>$pids];
        return view('pages.remove', $data);
    }

    public function deletePerson(): \Illuminate\Http\RedirectResponse
    {
        $request = request();
        $validated = $request->validate([
            'pid' => ['required', Rule::in($this->getPids())]
        ]);
        $table = 'Person';
        $pid = $request->pid;
        $values = array("pid" =>$pid);
        $this->delete($table, $values);
        return redirect()->back()->with('message',"Person with ID: {$pid} Deleted.");
    }
}
