@extends('layouts.default')

@section('container')

<table class="table-bordered rounded-3 overflow-hidden">
        <caption class="top-caption"> Join Query with {{sizeof($result)}} resulting rows </caption>
        <thead>
            <tr>
                <th>Person ID</th>
                <th>Gender</th>
                <th>Age</th>
                <th>Net Capital Gains</th>
                
            </tr>
        </thead>
        <tbody>
            @foreach($result as $row)
                <tr>
                    <td>{{ $row->pid }}</td>
                    <td>{{ $row->gender }}</td>
                    <td>{{ $row->age }}</td>
                    <td>${{ $row->net_capital_gains }}
                </tr>
            @endforeach
        </tbody>
    </table>
@stop