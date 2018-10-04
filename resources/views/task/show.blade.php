@extends('layouts.app')

@section('title')
    {{ $task->name }}
@endsection

@section('subtitle')
@endsection

@section('breadcrumbs')
    {!! Breadcrumbs::render('task.show', $task) !!}
@endsection

@section('content')

<div class="box box-default">
    <div class="box-body">
        <div class="row">
            <div class="col-xs-3">
                <h4> Name: </h4>
            </div>
            <div class="col-xs-9">
                <h4> {{ $task->name }} </h4>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-3">
                <h4> Description </h4>
            </div>
            <div class="col-xs-9">
                <h4> {{ $task->description }} </h4>
            </div>
        </div>
    </div>
</div>

<div class="box box-default">
    <div class="box-header">
        <div class="box-title"> Sessions </div>
        <div class="box-tools"> Total Time Spent: {{ ($task->sessions->totalDurationForHumans()) }} </div>
    </div>
    <table class="table box-body">
            <tr>
                <th> Id </th>
                <th colspan="2"> Started At </th>
                <th colspan="2"> Ended At </th>
            </tr>
        @foreach($task->sessions as $session)
            <tr>
                <td> <a href="{{ route('session.show', ['session' => $session]) }}"> {{ $session->id }} </a></td>
                <td> {{ $session->localStartedAtDateForHumans }} </td>
                <td> {{ $session->localStartedAtTimeForHumans }} </td>
                <td> {{ $session->localEndedAtDateForHumans }} </td>
                <td> {{ $session->localEndedAtTimeForHumans }} </td>
            </tr>
        @endforeach
    </table>
</div>

@endsection
