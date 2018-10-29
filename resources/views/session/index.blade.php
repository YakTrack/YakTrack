@extends('layouts.app')

@section('title')
    Sessions
@endsection

@section('subtitle')
@endsection

@section('breadcrumbs')
    {!! Breadcrumbs::render('session.index') !!}
@endsection

@section('content')

@section('top-right-toolbar')
<a href="{{ route('session.create') }}" class="btn btn-primary btn-sm mr-2">
    <i class="fa fa-plus pr-2"></i>
    Create Session
</a>
<a href="{{ route('session.start') }}" class="btn btn-green">
    <i class="fa fa-clock pr-2"></i>
    Start Session
</a>
@endsection

@if($days->count())
<div class="p-4 bg-white rounded shadow">
    <table class="table table-hover bg-white">
        <thead>
            <tr>
                <th class="pl-0"> <input type="checkbox"/> </th>
                <th> Start Time </th>
                <th> End Time </th>
                <th> Total Time </th>
                <th> Linked To </th>
                @foreach($thirdPartyApplications as $app)
                    <th> Linked to {{ $app->name }} </th>
                @endforeach
                <th> Invoice </th>
                <th class="text-right pr-0">
                    <div class="relative float-right text-right">
                        <button class="btn float-right">
                            <span>Actions</span>
                            <i class="fas fa-caret-down"></i>
                            <div class="rounded shadow-md mt-2 absolute mt-12 pin-t pin-l min-w-full bg-white dropdown">
                                <ul class="list-reset">
                                    <li><a href="#" class="px-4 py-2 block text-black hover:bg-grey-light no-underline"> Link to invoice </a></li>
                                </ul>
                            </div>
                        </button>

                    </div>
                </th>
            </tr>
        </thead>
        <tbody>
            @foreach($days as $sessionsInDay)
                <tr class="bg-blue-lightest text-grey-dark font-light text-xs uppercase">
                    <td class="px-3 py-1 rounded" colspan="10"> {{ $sessionsInDay->first()->localStartedAt->format('l jS F Y') }} </td>
                </tr>
                @foreach($sessionsInDay as $key => $session)
                    <tr
                    >
                        <td class=""> <input type="checkbox"/></td>
                        <td class="min-w-1">
                            {{ $session->localStartedAtTimeForHumans }}
                        </td>
                        <td class="min-w-1">
                            @if ($session->isRunning())
                                <a class="btn" href="{{ route('session.stop', ['session' => $session]) }}"><i class="fa fa-stop text-red"></i></a>
                            @else
                                {{ $session->localEndedAtTimeForHumans }}
                            @endif
                        </td>
                        <td class="min-w-1">
                            {{ $session->durationForHumans }}
                        </td>
                        <td class="max-w-3">
                            @include('partials.session.session-task', ['session' => $session])
                        </td>
                        @foreach($thirdPartyApplications as $app)
                            <td>
                                
                                @include('partials.session.third-party-application')
                            </td>
                        @endforeach
                        <td>
                            @if($session->invoice)
                                <a class="no-underline text-xs" href="{{ route('invoice.show', ['invoice' => $session->invoice] )}}"> {{ $session->invoice->number }} </a>
                            @endif
                        </td>
                        <td class="text-right inline-flex pb-2 @if($key == 0) pt-2 @endif float-right">
                            <form action="{{ route('session.destroy', $session->id) }}" method="post">
                                <div class="btn-group pull-right">
                                    <a
                                        href="{{ route('session.edit', ['session' => $session]) }}"
                                        class="btn btn-default"
                                    >
                                        <i class="fa fa-edit"></i>
                                    </a>
                                    {{ csrf_field() }}
                                    <input type="hidden" name="_method" value="DELETE">
                                    <button class="btn delete-item-button">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </div>
                            </form>
                        </td>
                    </tr>
                @endforeach
            @endforeach
        </tbody>
    </table>
</div>
@else
    <div>
        You have not created any sessions yet.
    </div>
@endif

@endsection
