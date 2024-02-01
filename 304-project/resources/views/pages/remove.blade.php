@extends('layouts.default')
@section('container')
    <form action="/remove" method="POST">
            @csrf
            <div class="form-group">
                <h5>Choose Person ID</h5>
                <label for="pid">Person ID</label><input type="number" class="form-control" list="pids" name="pid" id="pid">
                <datalist id="pids">
                    @foreach ($pids as $pid)
                        <option value={{$pid}}>{{$pid}}</option>
                    @endforeach
                </datalist>
                <input type="submit" class="btn btn-primary" value="Delete">
            </div>
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
