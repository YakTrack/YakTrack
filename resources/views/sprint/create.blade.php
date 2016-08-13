@extends('layouts.app')

@section('title')
    Add Sprint
@endsection

@section('subtitle')
@endsection

@section('breadcrumbs')
    {!! Breadcrumbs::render('sprint.create') !!}
@endsection

@section('content')

<div class="box box-default">
    <div class="box-body">
    {!! Form::open(['method' => 'post', 'url' => route('sprint.store')]) !!}
        <div class="form-group">
            {!! Form::label('name', 'Name') !!}
            {!! Form::text('name', null, ['class' => 'form-control', 'placeholder' => 'Sprint name']) !!}
        </div>
        <div class="form-group">
            {!! Form::label('project_id', 'Project') !!}
            {!! Form::select('project_id', $projects->pluck('name', 'id'), null, ['class' => 'form-control']) !!}
        </div>
        <div class="form-group">
            {!! Form::submit('Add', ['class' => 'btn btn-default']) !!}
        </div>
    {!! Form::close() !!}
    </div>
</div>

@endsection
