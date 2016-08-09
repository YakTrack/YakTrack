@extends('layouts.app')

@section('title')
    {{ $project->name }}
@endsection

@section('subtitle')
@endsection

@section('breadcrumbs')
    {!! Breadcrumbs::render('project.show', $project) !!}
@endsection

@section('content')

<div class="box box-default">
    <div class="box-body">
        <div class="row">
            <div class="col-xs-3">
                <h4> Name: </h4>
            </div>
            <div class="col-xs-9">
                <h4> {{ $project->name }} </h4>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-3">
                <h4> Description </h4>
            </div>
            <div class="col-xs-9">
                <h4> {{ $project->description }} </h4>
            </div>
        </div>
    </div>
</div>

@endsection
