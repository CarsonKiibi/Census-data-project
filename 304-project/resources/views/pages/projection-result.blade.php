@extends('layouts.default')

@section('container')
<table class="table-bordered rounded-3 overflow-hidden">
    <caption class="top-caption"> Projection Query with {{sizeof($result)}} resulting rows from the {{$table_name}} table</caption>
    <thead>
        <tr>
        
            @php
            foreach($result as $row) {
                $row_arr = json_decode(json_encode($row), true);
            }
            @endphp
            @foreach(array_keys($row_arr) as $header)
            <th> {{ ucfirst($header) }} </td>
            @endforeach
        </tr>
    </thead>
        <tbody>
            @foreach($result as $row)
            @php
                $row_arr = json_decode(json_encode($row), true);
            @endphp
                <tr>
                    @foreach($row_arr as $val)
                    @if (isset($val))
                        <td> {{$val}} </td>
                    @else 
                        <td> N/A or DNE </td>
                    @endif
                     @endforeach

                </tr>
            @endforeach
        </tbody>
    </table>
@stop