@extends('layouts.app')

@section('title')
    {{ $client->name }}
@endsection

@section('subtitle')
@endsection

@section('breadcrumbs')
    {!! Breadcrumbs::render('client.show', $client) !!}
@endsection

@section('content')

<div class="card box-default">
    <div class="card-body">
        <div class="row">
            <div class="col-xs-3">
                <h4> Name: </h4>
            </div>
            <div class="col-xs-9">
                <h4> {{ $client->name }} </h4>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-3">
                <h4> Email: </h4>
            </div>
            <div class="col-xs-9">
                <h4> {{ $client->email }} </h4>
            </div>
        </div>
    </div>
</div>

@endsection
