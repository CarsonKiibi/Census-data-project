@extends('layouts.default')

@section('container')
The "Group By" query accesses the career table, and displays the average income by occupation.
<form action="{{ route('process.groupby') }}" method="GET">
        <button type="submit" class="btn">Confirm</button>
    </form>
@stop