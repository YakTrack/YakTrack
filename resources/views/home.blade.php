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
                        <tr>
                            <td colspan=2> TOTAL </td>
                            <td> {{ $todaysTotal }} </td>
                        </tr>
                        @foreach($todaysWorkSessions as $workSession)
                            <tr>
                                <td> {{ $workSession->startedAtTimeForHumans }} </td>
                                <td> {{ $workSession->endedAtTimeForHumans }} </td>
                                <td> 1 </td>
                            </tr>
                        @endforeach
                        <tr><td colspan={{ $totalColumns }}></td></tr>
                    </tbody>
                    <thead>
                        <tr>
                            <th> THIS WEEK </th>
                            <th> </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($thisWeeksWorkSessions as $dayOfWeek => $totalTime)
                            <tr>
                                <td colspan="{{ $totalColumns - 1 }}"> {{ $dayOfWeek }} </td>
                                <td> {{ $totalTime }} </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
