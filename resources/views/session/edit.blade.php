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

{!! Form::open(['method' => 'put', 'url' => route('session.update', ['session' => $session])]) !!}
    <div class="form-group">
        {!! Form::label('name', 'Started At') !!}
        {!! Form::text('started_at', $session->localStartedAt, ['class' => 'form-control', 'placeholder' => 'YYYY-MM-DD HH:MM:SS']) !!}
    </div>
    <div class="form-group">
        {!! Form::label('name', 'Ended At') !!}
        {!! Form::text('ended_at', $session->localEndedAt, ['class' => 'form-control', 'placeholder' => 'YYYY-MM-DD HH:MM:SS']) !!}
    </div>
    <div class="form-group">
        {!! Form::label('task_id', 'Task') !!}
        <task-select :tasks="{{ $tasks->toJson() }}" task="{{ $session->task_id }}"></task-select>
    </div>
    <div class="form-group">
        <label for="sprint_id"> Sprint </label>
        <sprint-select :sprints="{{ $sprints->toJson() }}" sprint="{{ $session->sprint_id }}"></sprint-select>
    </div>
    <div class="form-group">
        {!! Form::label('invoice_id', 'Invoice') !!}
        <invoice-select :invoices="{{ $invoices->toJson() }}" invoice="{{ $session->invoice_id }}"></invoice-select>
    </div>
    <div class="flex mt-4">
        <div class="flex-1 mt-2">
            <a href="{{ route('session.index') }}" class="btn btn-default"> Cancel </a>
        </div>
        <div class="flex-1 float-right">
            {!! Form::submit('Update', ['class' => 'btn btn-blue float-right']) !!}
        </div>
    </div>
{!! Form::close() !!}
</div>

@endsection
