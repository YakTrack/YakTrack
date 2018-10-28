@extends('layouts.app')

@section('breadcrumbs')
    {!! Breadcrumbs::render('project.index') !!}
@endsection
@section('title')
    Home
@endsection

@section('content')

    <div class="bg-white rounded shadow p-4">
        <h2 class="pb-4"> Time Tracking Summary </h2>
        <h5 class="pt-4 pb-2 text-grey-darker"> THIS WEEK </h5>
        @foreach($thisWeeksWorkSessions as $dayOfWeek)
            <div class="flex pt-2 pb-2 @if($dayOfWeek['date']->isToday()) -ml-4 pl-4 -mr-4 pr-4 bg-blue-lightest @endif">
                <div class="flex-1 text-grey-dark"> {{ $dayOfWeek['dateForHumans'] }} </div>
                <div class="flex-1 {{ $dayOfWeek['totalTimeWorked'] != '0:00:00' ? 'text-grey-dark' : '' }}"> {{ $dayOfWeek['totalTimeWorked'] != '0:00:00' ? $dayOfWeek['totalTimeWorked']: '-' }} </div>
            </div>
        @endforeach
        <div class="active">
            <div colspan="" class=""> TOTAL </div>
            <div class=""> {{ $thisWeeksTotal }} </div>
        </div>
    </div>

    <div class="pt-6 -ml-2 -mr-2 flex">
        @foreach($clients as $client)
            <div class="rounded shadow ml-2 mr-2 flex-1 bg-white">
                <div class="p-4">
                    <h5 class="card-title">
                        {{ $client->name }}
                    </h5>
                    <div class="row">
                        <div class="col"> Total Time This Week </div>
                        <div class="col"> {{ $client->sessionsThisWeek->totalDurationForHumans() }} </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

@endsection
