@extends('layouts.default')

@section('container')
The "Join" query takes in a $ amount, and returns anonymized individuals with more than the input amount of capital gains.
    <form action="{{ route('process.join') }}" method="GET">
        <input type="number" min="0" max="1757441"value="" name="x">
        <button type="submit" class="btn">Confirm</button>
    </form>

@stop