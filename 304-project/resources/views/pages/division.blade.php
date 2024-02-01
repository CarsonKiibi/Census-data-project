@extends('layouts.default')

@section('container')
The "Division" query returns careers that contain all educational fields.
<form action="{{ route('process.division') }}" method="GET">
        <button type="submit" class="btn">Confirm</button>
    </form>
@stop
