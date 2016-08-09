@extends('layouts.app')

@section('title')
    Add Project
@endsection

@section('subtitle')
@endsection

@section('breadcrumbs')
    {!! Breadcrumbs::render('project.create') !!}
@endsection

@section('content')

<div class="box box-default">
    <div class="box-body">
    {!! Form::open(['method' => 'post', 'url' => route('project.store')]) !!}
        <div class="form-group">
            {!! Form::label('name', 'Name') !!}
            {!! Form::text('name', null, ['class' => 'form-control', 'placeholder' => 'Project name']) !!}
        </div>
        <div class="form-group">
            {!! Form::label('description,', 'Description') !!}
            {!! Form::textarea('description', null, ['class' => 'form-control', 'placeholder' => 'Describe this project']) !!}
        </div>
        <div class="form-group">
            {!! Form::label('client_id', 'Client') !!}
            {!! Form::select('client_id', $clients->pluck('name', 'id'), null, ['class' => 'form-control']) !!}
        </div>
        <div class="form-group">
            {!! Form::submit('Create', ['class' => 'btn btn-default']) !!}
        </div>
    {!! Form::close() !!}
    </div>
</div>

@endsection
