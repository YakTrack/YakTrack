@extends('layouts.app')

@section('title')
    {{ $sprint->name }}
@endsection

@section('subtitle')
@endsection

@section('breadcrumbs')
    {!! Breadcrumbs::render('sprint.show', $sprint) !!}
@endsection

@section('content')

<div class="card box-default">
    <div class="card-body">
        <div class="row">
            <div class="col-xs-3">
                <h4> Name: </h4>
            </div>
            <div class="col-xs-9">
                <h4> {{ $sprint->name }} </h4>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-3">
                <h4> Project </h4>
            </div>
            <div class="col-xs-9">
                <h4>
                    <a href="{{ route('project.show', ['project' => $sprint->project]) }}"> {{ $sprint->project->name }} </a>
                </h4>
            </div>
        </div>
    </div>
</div>

@endsection
