@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="box">
            <div class="box-header">
                <div class="box-header">
                    Time Tracking Summary
                </div>
            </div>
            <div class="box-body">
                <table class="table table-responsive table-hover box-body">
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
            </div>
        </div>
    </div>
@endsection
