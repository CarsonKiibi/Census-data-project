@extends('layouts.default')

@section('container')
<table class="table-bordered rounded-3 overflow-hidden">
    <caption class="top-caption"> Nested Group By Query with {{sizeof($result)}} resulting rows</caption>
        <thead>
            <tr>
                <th>Area</th>
                <th>Region</th>
                <th>Average Cost</th>
            </tr>
        </thead>
        <tbody>
            @foreach($result as $row)
                <tr>
                    <td>{{ $row->aname }}</td>
                    <td>{{ $row->rname }}</td>
                    <td>${{ round($row->avg_cost, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@stop