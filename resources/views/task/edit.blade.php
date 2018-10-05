@extends('layouts.app')

@section('title')
    Edit Task
@endsection

@section('subtitle')
@endsection

@section('breadcrumbs')
    {!! Breadcrumbs::render('task.edit', $task) !!}
@endsection

@section('content')

<div class="card box-default">
    <div class="card-body">
        {!! Form::open(['method' => 'put', 'url' => route('task.update', ['task' => $task])]) !!}
        <div class="form-group">
            {!! Form::label('name', 'Name') !!}
            {!! Form::text('name', $task->name, ['class' => 'form-control', 'placeholder' => 'Task name']) !!}
        </div>
        <div class="form-group">
            {!! Form::label('description', 'Description') !!}
            {!! Form::textarea('description', $task->description, ['class' => 'form-control', 'placeholder' => 'Task description']) !!}
        </div>
        <div class="form-group">
            {!! Form::label('project_id', 'Project') !!}
            {!! Form::select('project_id', $projects->pluck('name', 'id'), $task->project_id, ['class' => 'form-control']) !!}
        </div>
        <div class="form-group">
            {!! Form::submit('Update', ['class' => 'btn btn-default']) !!}
        </div>
    {!! Form::close() !!}
    </div>
</div>

@endsection
