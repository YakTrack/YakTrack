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
    <sessions-component :applications="{{ $thirdPartyApplications }}"></sessions-component>
@else
    <div>
        You have not created any sessions yet.
    </div>
@endif

@endsection