@extends('layouts.app')

@section('title')
    Edit Session
@endsection

@section('subtitle')
@endsection

@section('breadcrumbs')
    {!! Breadcrumbs::render('session.edit', $session) !!}
@endsection

@section('content')

<div class="box box-default">
    <div class="box-body">
        {!! Form::open(['method' => 'put', 'url' => route('session.update', ['session' => $session])]) !!}
        <div class="form-group">
            {!! Form::label('name', 'Started At') !!}
            {!! Form::text('started_at', $session->started_at, ['class' => 'form-control', 'placeholder' => 'YYYY-MM-DD HH:MM:SS']) !!}
        </div>
        <div class="form-group">
            {!! Form::label('name', 'Ended At') !!}
            {!! Form::text('ended_at', $session->ended_at, ['class' => 'form-control', 'placeholder' => 'YYYY-MM-DD HH:MM:SS']) !!}
        </div>
        <div class="form-group">
            {!! Form::label('task_id', 'Task') !!}
            {!! Form::select('task_id', $tasks->pluck('name', 'id'), $session->task_id, ['class' => 'form-control']) !!}
        </div>
        <div class="form-group">
            {!! Form::submit('Update', ['class' => 'btn btn-default']) !!}
        </div>
    {!! Form::close() !!}
    </div>
</div>

@endsection
