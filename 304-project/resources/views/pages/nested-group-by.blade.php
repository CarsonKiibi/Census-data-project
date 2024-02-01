@extends('layouts.default')

@section('container')
The "Nested Group By" query takes a number of bedrooms, and displays a table with the area, region, and average cost of a home with that many bedrooms. 
    <form action="{{ route('process.nestedgroupby') }}" method="GET">
        <input type="number" min="1" max="5" value="" name="x">
        <button type="submit" class="btn">Confirm</button>
    </form>

@stop