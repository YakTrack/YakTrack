@extends('layouts.app')

@section('title')
    Add Client
@endsection

@section('subtitle')
@endsection

@section('breadcrumbs')
@endsection

@section('content')

<div class="box box-default">
    <div class="box-body">
    {!! Form::open(['method' => 'post', 'url' => route('client.store')]) !!}
        <div class="form-group">
            {!! Form::label('name', 'Name') !!}
            {!! Form::text('name', null, ['class' => 'form-control', 'placeholder' => 'name']) !!}
        </div>
        <div class="form-group">
            {!! Form::label('email,', 'Email') !!}
            {!! Form::text('email', null, ['class' => 'form-control', 'placeholder' => 'email']) !!}
        </div>
        <div class="form-group">
            {!! Form::submit('Add', ['class' => 'btn btn-default']) !!}
        </div>
    {!! Form::close() !!}
    </div>
</div>

@endsection
