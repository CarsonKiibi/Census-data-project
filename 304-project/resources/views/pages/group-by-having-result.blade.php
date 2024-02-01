@extends('layouts.default')

@section('container')

<table class="table-bordered rounded-3 overflow-hidden">
    <caption class="top-caption"> Group By Having Query with {{sizeof($result)}} resulting rows</caption>
        <thead>
            <tr>
                <th>Highest Attainment</th>
                <th>Field of Study</th>
                <th>Count</th>
            </tr>
        </thead>
        <tbody>
            @foreach($result as $row)
                <tr>
                    @if (isset($row->highest_attainment))
                        <td>{{ $row->highest_attainment }}</td>
                    @else 
                        <td> N/A or DNE </td>
                    @endif
                    @if (isset($row->field_of_study))
                        <td> {{$row->field_of_study}} </td>
                    @else 
                        <td> N/A </td>
                    @endif
                    
                    <td>{{ $row->count}}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@stop