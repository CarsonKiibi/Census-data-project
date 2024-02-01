@extends('layouts.default')

@section('container')
    The "Group By Having" query takes in a count, and returns the education achievements that have more people than your input.
    <form action="{{ route('process.groupbyhaving') }}" method="GET">
        <input type="number" min="0" max="2232" value="" name="x">
        <button type="submit" class="btn">Confirm</button>
    </form>
@stop