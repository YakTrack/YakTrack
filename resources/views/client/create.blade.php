@extends('layouts.app')

@section('title')
    Create Client
@endsection

@section('subtitle')
@endsection

@section('breadcrumbs')
    {!! Breadcrumbs::render('client.create') !!}
@endsection

@section('content')

{!! Form::open(['method' => 'post', 'url' => route('client.store')]) !!}
    <div class="form-group">
        {!! Form::label('name', 'Name') !!}
        {!! Form::text('name', null, ['class' => 'form-control', 'placeholder' => 'name']) !!}
    </div>
    <div class="form-group">
        {!! Form::label('email,', 'Email') !!}
        {!! Form::text('email', null, ['class' => 'form-control', 'placeholder' => 'email']) !!}
    </div>
    <div class="form-group float-right">
        {!! Form::submit('Create', ['class' => 'btn btn-blue']) !!}
    </div>
{!! Form::close() !!}

@endsection
