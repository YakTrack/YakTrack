@extends('layouts.app')

@section('title')
    Sessions
@endsection

@section('subtitle')
@endsection

@section('breadcrumbs')
    {!! Breadcrumbs::render('session.index') !!}
@endsection

@section('top-right-toolbar')
    <a href="{{ route('session.create') }}" class="btn btn-primary btn-sm mr-2">
        <i class="fa fa-stopwatch"></i>
    </a>
    <a href="{{ route('session.start') }}" class="btn btn-green">
        <i class="fa fa-play pr-2"></i>
        Start Session
    </a>
@endsection

@section('content')

    <index-session-table
        :third-party-applications="{{ $thirdPartyApplications->toJson() }}"
        :invoices="{{ $invoices->toJson() }}"
    ></index-session-table>

@endsection
