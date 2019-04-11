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
                <div class="flex-1 text-grey-dark"> {{ $dayOfWeek['dateNoYearForHumans'] }} </div>
                <div class="flex-1 text-center {{ $dayOfWeek['totalTimeWorked'] != '0:00:00' ? 'text-grey-darkest' : '' }}">
                    @if($dayOfWeek['totalTimeWorked'] != '0:00:00')
                        @if ($currentlyWorking && $dayOfWeek['date']->isToday())
                            <timer :initial-time="{{ $dayOfWeek['totalSecondsWorked'] }}"></timer>
                        @else
                            {{ $dayOfWeek['totalTimeWorked'] }}
                        @endif
                    @else 
                        <span class="text-grey-light">-</span>
                    @endif
                </div>
            </div>
        @endforeach
        <div class="flex mt-4">
            <div class="flex-1 text-xl"> Total </div>
            <div class="flex-1 text-center">
                @if($currentlyWorking)
                    <timer :initial-time="{{ $totalSecondsThisWeek }}"></timer>
                @else
                    <strong> {{ $thisWeeksTotal }} </strong>
                @endif
            </div>
        </div>
    </div>

    <div class="grid pt-4">
        @foreach($clients as $key => $client)
            <div class="rounded shadow bg-white">
                <div class="p-4">
                    <h3> {{ $client->name }} </h3>
                    <h4 class="mt-4"> This Week </h4>
                    <div class="flex mt-3">
                        <div class="flex-1"> Total Time </div>
                        <div class="flex-1 text-center">
                            @if($currentlyWorking && $currentClientName == $client->name)
                                <timer :initial-time="{{ $client->sessionsThisWeek->totalDurationInSeconds() }}"></timer>
                            @else
                                {{ $client->sessionsThisWeek->totalDurationForHumans() }}
                            @endif
                        </div>
                    </div>
                    @foreach($client->openSprints as $sprint)
                        <h4 class="mt-4"> Sprint: {{ $sprint->name }} </h4>
                        <div class="flex mt-3">
                            <div class="flex-1"> Total Time </div>
                            <div class="flex-1 text-center">
                                @if($currentlyWorking && $currentSession->sprint_id == $sprint->id)
                                    <timer :initial-time="{{ $sprint->sessions->totalDurationInSeconds() }}"></timer>
                                @else
                                    {{ $sprint->sessions->totalDurationForHumans() }}
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>

@endsection
