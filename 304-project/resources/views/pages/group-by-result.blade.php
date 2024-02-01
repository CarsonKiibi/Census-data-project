@extends('layouts.default')

@section('container')

<table class="table-bordered rounded-3 overflow-hidden">
    <caption class="top-caption"> Group By Query with {{sizeof($result)}} resulting rows</caption>
        <thead>
            <tr>
                <th>Occupation</th>
                <th>Average Income</th>
            </tr>
        </thead>
        <tbody>
            @foreach($result as $row)
                <tr>
                    <td>{{ $row->occupation }}</td>
                    <td>${{ round($row->avg_income, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@stop