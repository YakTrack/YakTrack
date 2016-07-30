@extends('layouts.app')

@section('title')
    Edit Client
@endsection

@section('subtitle')
@endsection

@section('breadcrumbs')
    {!! Breadcrumbs::render('client.edit', $client) !!}
@endsection

@section('content')

<div class="box box-default">
    <div class="box-body">
        {!! Form::open(['method' => 'put', 'url' => route('client.update', ['client' => $client])]) !!}
        <div class="form-group">
            {!! Form::label('name', 'Name') !!}
            {!! Form::text('name', $client->name, ['class' => 'form-control', 'placeholder' => 'name']) !!}
        </div>
        <div class="form-group">
            {!! Form::label('email,', 'Email') !!}
            {!! Form::text('email', $client->email, ['class' => 'form-control', 'placeholder' => 'email']) !!}
        </div>
        <div class="form-group">
            {!! Form::submit('Update', ['class' => 'btn btn-default']) !!}
        </div>
    {!! Form::close() !!}
    </div>
</div>

@endsection
