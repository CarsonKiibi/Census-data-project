@extends('layouts.default')

@section('container')

    <table class="table-bordered rounded-3 overflow-hidden">
        <caption class="top-caption"> Division Query with {{sizeof($result)}} resulting rows</caption>
        <thead>
            <tr>
                <th>ID</th>
                <th>Industry</th>
                <th>Occupation</th>
                
            </tr>
        </thead>
        <tbody>
            @foreach($result as $row)
                <tr>
                    <td>{{ $row->crid }}</td>
                    <td>{{ $row->industry }}</td>
                    <td>{{ $row->occupation }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@stop