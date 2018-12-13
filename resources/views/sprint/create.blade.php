@extends('layouts.app')

@section('title')
    Create Sprint
@endsection

@section('subtitle')
@endsection

@section('breadcrumbs')
    {!! Breadcrumbs::render('sprint.create') !!}
@endsection

@section('content')

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
        {!! Form::label('is_open', 'Is Open') !!}
        {!! Form::checkbox('is_open', 'is_open', false) !!}
    </div>
    <div class="form-group float-right">
        {!! Form::submit('Create', ['class' => 'btn btn-blue']) !!}
    </div>
{!! Form::close() !!}

@endsection
