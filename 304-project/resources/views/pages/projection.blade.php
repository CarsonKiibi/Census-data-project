
@extends('layouts.default')

@section('container')

        <a> The "Projection" query allows you to pick a table, and then the columns of that table to display.</p>
        <form action="{{ route('process.projectionselectcolumn') }}" method="GET">
            @csrf
            <label for="table">Select a Table:</label>
            <select name="table-name" id="table">
                @foreach($tables as $table)
                <option value="{{ $table }}" name="table-name">{{ $table }}</option>
                @endforeach
            </select>

            <button type="submit" class="btn">Confirm</button>
        </form>
@stop