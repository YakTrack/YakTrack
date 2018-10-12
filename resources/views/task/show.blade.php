@extends('layouts.app')

@section('title')
    Task #{{ $task->id }}
@endsection

@section('subtitle')
@endsection

@section('breadcrumbs')
    {!! Breadcrumbs::render('task.show', $task) !!}
@endsection

@section('content')

<div class="row">
    <div class="col">
        <h4> Name: </h4>
    </div>
    <div class="col">
        <h4> {{ $task->name }} </h4>
    </div>
</div>
<div class="row">
    <div class="col">
        <h4> Description: </h4>
    </div>
    <div class="col">
        <h4> {{ $task->description }} </h4>
    </div>
</div>
<br>
<h4> Task Sessions </h4>
Total Time Spent: {{ ($task->sessions->totalDurationForHumans()) }}

<table class="table table-hover">
    <tbody>
        <tr>
            <th> Id </th>
            <th> Started At Date </th>
            <th> Started At Time </th>
            <th> Ended At Date </th>
            <th> Ended At Time </th>
            @foreach($thirdPartyApplications as $app)
                <th><div> Linked to {{ $app->name }} </div><small> {{ $app->totalLinkedSessionDurationForTaskForHumans($task) }} / {{ $task->sessions->totalDurationForHumans() }} </small></th>
            @endforeach
        </tr>
        @foreach($task->sessions as $session)
            <tr>
                <td> <a href="{{ route('session.show', ['session' => $session]) }}"> {{ $session->id }} </a></td>
                <td> {{ $session->localStartedAtDateForHumans }} </td>
                <td> {{ $session->localStartedAtTimeForHumans }} </td>
                <td> {{ $session->localEndedAtDateForHumans }} </td>
                <td> {{ $session->localEndedAtTimeForHumans }} </td>
                @foreach($thirdPartyApplications as $app)
                    <td>
                        @include('partials.session.third-party-application')
                    <td>
                @endforeach
            </tr>
        @endforeach
    </tbody>
</table>

@endsection
