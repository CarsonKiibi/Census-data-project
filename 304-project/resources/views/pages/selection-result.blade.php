@extends('layouts.default')

@section('container')

<table class="table-bordered rounded-3 overflow-hidden">
    <caption class="top-caption"> Results of selection query: {{sizeof($result)}} rows </caption>
    <thead>
        <tr>
            @foreach($columns as $column)
                <th>{{$column}}</th>
            @endforeach
        </tr>
    </thead>
    <tbody>
        @foreach($result as $row)
            <tr>
                @foreach($columns as $column)
                    <td>{{ $row->$column }}</td>
                @endforeach
            </tr>
        @endforeach
    </tbody>
</table>
@stop