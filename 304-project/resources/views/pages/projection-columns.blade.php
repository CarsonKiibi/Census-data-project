@extends('layouts.default')

@section('container')
@if(session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
@endif
        <form action="{{ route('process.performprojection') }}" method="GET">
            @csrf
            <label for="table">Select columns you would like to project:</label>
            </br>
            <input type="hidden" name="table_name" value="{{ $table_name }}">

            @foreach($columns as $column)
            <label for="cols[{{$column}}]">
            <input type="checkbox" name="cols[{{$column}}]">{{$column}} </input>
            </br>
            @endforeach

            <button type="submit" class="btn">Confirm</button>
        </form>

@stop