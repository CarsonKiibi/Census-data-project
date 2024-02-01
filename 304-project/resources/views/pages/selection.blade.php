@extends('layouts.default')

@section('container')
<style>
    label {
        display: inline-block;
        width: 120px;
        text-align: right;
    }
    .not-visible {
        visibility: hidden;
    }
</style>

<caption class="top-caption"> Choose the filters to apply on the Person table</caption>
<form action="{{ route('process.performSelection') }}" method="GET">
    <table class="table-bordered rounded-3 overflow-hidden">
    @foreach($columns as $column)
        <?php if ($column !== $columns[0]): ?>
        <select name="logic[{{$column}}]" id="logic[{{$column}}]">
            <option value="AND">AND</option>
            <option value="OR">OR</option>
        </select>
        <?php else: ?>
            <select class="not-visible" name="logic[{{$column}}]" id="logic[{{$column}}]">
            <option value="AND">AND</option>
            <option value="OR">OR</option>
            </select>
        <?php endif; ?>
        <label>{{$column}}</label>
        <select name="operator[{{$column}}]" id="operator[{{$column}}]">
            <option value="N/A">N/A</option>
            <option value="=">=</option>
            <option value=">">></option>
            <option value="<"><</option>
            <option value=">=">>=</option>
            <option value="<="><=</option>
            <option value="!=">!=</option>
        </select>
        <input type="text" list="datalist[{{$column}}]" name="value[{{$column}}]" id="value[{{$column}}]">
        <datalist name="datalist[{{$column}}]" id="datalist[{{$column}}]">
            @foreach (${$column} as $c)
                <option value="{{$c}}">{{$c}}</option>
            @endforeach
        </datalist>
        <br>
    @endforeach
    <button type="submit" class="btn">Confirm</button>
    </table>
</form>
    
@stop