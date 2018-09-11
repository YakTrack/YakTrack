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

@include('partials.modals.delete_item_modal')

<div class="box item-type-container" data-item-type="session">
    <div class="box-header with-border">
        <div class="pull-right">
            <a href="{{ route('session.start') }}" class="btn btn-success btn-sm">
                <i class="fa fa-clock"></i>
                Start Session
            </a>
            <a href="{{ route('session.create') }}" class="btn btn-primary btn-sm">
                <i class="fa fa-plus"></i>
                Add Session
            </a>
        </div>
    </div>
    @if($sessions->count())
        <table class="table box-body">
            <thead>
                <tr>
                    <th> Start Time </th>
                    <th> End Time </th>
                    <th> Total Time </th>
                    <th> <span class="pull-right"> Actions </span> </th>
                </tr>
            </thead>
            <tbody>
                @foreach($sessions as $session)
                <tr
                    class="item-container"
                    data-item-name="{{ $session->name }}"
                    data-item-destroy-route="{{ route('session.destroy', ['session' => $session]) }}"
                >
                    <td>
                        {{ $session->startedAtTimeForHumans }}
                    </td>
                    <td>
                        @if ($session->isRunning())
                            <a class="btn btn-danger" href="{{ route('session.stop', ['session' => $session]) }}"> Stop Now </a>
                        @else
                            {{ $session->endedAtTimeForHumans }}
                        @endif
                    </td>
                    <td>
                        {{ $session->totalTime }}
                    </td>
                    <td>
                        <div class="btn-group pull-right">
                            <a
                                href="{{ route('session.edit', ['session' => $session]) }}"
                                class="btn btn-default"
                            >
                                <i class="fa fa-edit"></i>
                            </a>
                            <button class="btn btn-default delete-item-button">
                                <i class="fa fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <div class="box-body">
        You have not created any sessions yet.
    </div>
    @endif
</div>

@endsection
