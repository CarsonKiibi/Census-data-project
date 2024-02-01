@extends('layouts.default')

@section('container')
    <form action="/update" method="post">
        @csrf
        @csrf
        <div class="form-group">
            <h3>Update Person </h3>
            <h4>1. Choose Person</h4>
            <label for="pid">Person ID (required)</label>
            <input type="number" class="form-control" list="pids" name="pid" id="pid">
            <datalist id="pids">
                @foreach ($pids as $pid)
                    <option value={{$pid}}>{{$pid}}</option>
                @endforeach
            </datalist>
        </div>
        <br>
        <h4>2. Update Values: </h4>
        <label>Marital Status</label>
        <div class="form-check">
            <label class="form-check-label" for="marital_status_null">Not available</label>
            <input type="radio" class="form-check-input" id="marital_status_null" name="marital_status" value='' checked>
        </div>
        <div class="form-check">
            <input type="radio" class="form-check-input" id="neverMarried" name="marital_status" value="Never married (not living common law)">
            <label class="form-check-label" for="neverMarried">Never married (not living common law)</label>
        </div>

        <div class="form-check">
            <input type="radio" class="form-check-input" id="married" name="marital_status" value="Married">
            <label class="form-check-label" for="married">Married</label>
        </div>

        <div class="form-check">
            <input type="radio" class="form-check-input" id="livingCommonLaw" name="marital_status"
                   value="Living common law">
            <label class="form-check-label" for="livingCommonLaw">Living common law</label>
        </div>

        <div class="form-check">
            <input type="radio" class="form-check-input" id="separated" name="marital_status" value="Separated (not living common law)">
            <label class="form-check-label" for="separated">Separated (not living common law)</label>
        </div>

        <div class="form-check">
            <input type="radio" class="form-check-input" id="divorced" name="marital_status" value="Divorced (not living common law)">
            <label class="form-check-label" for="divorced">Divorced (not living common law)</label>
        </div>
        <label>Gender</label>
        <div class="form-check">
            <label class="form-check-label" for="manPlus">Man+</label>
            <input type="radio" class="form-check-input" id="manPlus" name="gender" value="Man+" checked>
        </div>

        <div class="form-check">
            <input type="radio" class="form-check-input" id="womanPlus" name="gender" value="Woman+">
            <label class="form-check-label" for="womanPlus">Woman+</label>
        </div>
        <div class="form-group">
            <label for="age">Age</label><input type="number" class="form-control" id="age" name="age" min="0" max="150">
        </div>
        <div class="form-group">
            <label for="hid">Household ID (required)</label>
            <input type="number" class="form-control" list="hids" name="hid" id="hid">
            <datalist id="hids">
                @foreach ($hids as $hid)
                    <option value={{$hid}}>{{$hid}}</option>
                @endforeach
            </datalist>
        </div>
        <div class="form-group">
            <label for="crid">Career ID</label>
            <input type="number" class="form-control" list="crids" name="crid" id="crid">
            <datalist id="crids">
                @foreach ($crids as $crid)
                    <option value={{$crid}}>{{$crid}}</option>
                @endforeach
            </datalist>
        </div>
        <div class="form-group">
            <label for="eid">Education ID</label>
            <input type="number" class="form-control" list="eids" name="eid" id="eid">
            <datalist id="eids">
                @foreach ($eids as $eid)
                    <option value={{$eid}}>{{$eid}}</option>
                @endforeach
            </datalist>
        </div>        
        <br>
        <input type="submit" class="btn btn-primary" value="Submit">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            @if(session()->has('message'))
                <div class="alert alert-success">
                    {{ session()->get('message') }}
                </div>
    @endif
@stop
