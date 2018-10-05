@extends('layouts.app')

@section('title') Home @endsection

@section('content')

<h5> Time Tracking Summary </h5>
<table class="table table-hover table-borderless">
    <thead>
        <tr>
            <th colspan={{ $totalColumns = 3 }}> TODAY </th>
        </tr>
    </thead>
    <tbody>
        @foreach($todaysWorkSessions as $workSession)
            <tr>
                <td> {{ $workSession->localStartedAtTimeForHumans }} </td>
                <td> {{ $workSession->localEndedAtTimeForHumans }} </td>
                <td> {{ $workSession->durationForHumans }} </td>
            </tr>
        @endforeach
        <tr class="active">
            <th colspan="{{ $totalColumns - 1}}"> TOTAL </th>
            <th> {{ $todaysTotal }} </th>
        </tr>
        <tr><td colspan="{{ $totalColumns }}"></td></tr>
    </tbody>
    <thead>
        <tr>
            <th colspan="{{ $totalColumns - 1 }}"> THIS WEEK </th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        @foreach($thisWeeksWorkSessions as $dayOfWeek)
            <tr>
                <td colspan="{{ $totalColumns - 1 }}"> {{ $dayOfWeek['dateForHumans'] }} </td>
                <td> {{ $dayOfWeek['totalTimeWorked'] }} </td>
            </tr>
        @endforeach
        <tr class="active">
            <th colspan="{{ $totalColumns - 1}}"> TOTAL </th>
            <th> {{ $thisWeeksTotal }} </th>
        </tr>
    </tbody>
</table>
@endsection
