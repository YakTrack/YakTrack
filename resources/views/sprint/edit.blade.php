@extends('layouts.app')

@section('title')
    Edit Sprint
@endsection

@section('subtitle')
@endsection

@section('breadcrumbs')
    {!! Breadcrumbs::render('sprint.edit', $sprint) !!}
@endsection

@section('content')

<div class="box box-default">
    <div class="box-body">
        {!! Form::open(['method' => 'post', 'url' => route('sprint.update', ['sprint' => $sprint]), 'method' => 'patch']) !!}
        <div class="form-group">
            {!! Form::label('name', 'Name') !!}
            {!! Form::text('name', $sprint->name, ['class' => 'form-control', 'placeholder' => 'Sprint name']) !!}
        </div>
        <div class="form-group">
            {!! Form::label('project_id', 'Project') !!}
            {!! Form::select('project_id', $projects->pluck('name', 'id'), $sprint->project_id, ['class' => 'form-control']) !!}
        </div>
        <div class="form-group">
            {!! Form::submit('Update', ['class' => 'btn btn-default']) !!}
        </div>
    {!! Form::close() !!}
    </div>
</div>

@endsection
