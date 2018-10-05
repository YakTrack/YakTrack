@extends('layouts.app')

@section('title')
    Edit Project
@endsection

@section('subtitle')
@endsection

@section('breadcrumbs')
    {!! Breadcrumbs::render('project.edit', $project) !!}
@endsection

@section('content')

<div class="card box-default">
    <div class="card-body">
        {!! Form::open(['method' => 'put', 'url' => route('project.update', ['project' => $project])]) !!}
        <div class="form-group">
            {!! Form::label('name', 'Name') !!}
            {!! Form::text('name', $project->name, ['class' => 'form-control', 'placeholder' => 'Project name']) !!}
        </div>
        <div class="form-group">
            {!! Form::label('description', 'Description') !!}
            {!! Form::textarea('description', $project->description, ['class' => 'form-control', 'placeholder' => 'Project description']) !!}
        </div>
        <div class="form-group">
            {!! Form::label('client_id', 'Client') !!}
            {!! Form::select('client_id', $clients->pluck('name', 'id'), $project->client_id, ['class' => 'form-control']) !!}
        </div>
        <div class="form-group">
            {!! Form::submit('Update', ['class' => 'btn btn-default']) !!}
        </div>
    {!! Form::close() !!}
    </div>
</div>

@endsection
