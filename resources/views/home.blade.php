@extends('layouts.app')

@section('title') Home @endsection

@section('content')

<div class="card">
    <div class="card-body">
        <h5 class="card-title"> Time Tracking Summary </h5>
        <table class="table table-hover table-borderless">
            <thead>
                <tr>
                    <th colspan="{{ $totalColumns = 2 - 1 }}"> THIS WEEK </th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($thisWeeksWorkSessions as $dayOfWeek)
                    <tr class="@if($dayOfWeek['date']->isToday()) table-primary @endif">
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
    </div>
</div>

<br>

<div class="row">
    @foreach($clients as $client)
        <div class="col">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">
                        {{ $client->name }}
                    </h5>
                    <div class="row">
                        <div class="col"> Total Time This Week </div>
                        <div class="col"> {{ $client->sessionsThisWeek->totalDurationForHumans() }} </div>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>

@endsection
