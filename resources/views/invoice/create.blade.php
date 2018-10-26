@extends('layouts.app')

@section('title')
    Create Invoice
@endsection

@section('subtitle')
@endsection

@section('breadcrumbs')
    {!! Breadcrumbs::render('invoice.create') !!}
@endsection

@section('content')

{!! Form::open(['method' => 'post', 'url' => route('invoice.store')]) !!}
    <div class="form-group">
        {!! Form::label('client_id', 'Client') !!}
        <client-select :clients='{{ $clients->toJson() }}'></client-select>
    </div>
    <div class="form-group">
        {!! Form::label('date', 'Date') !!}
        {!! Form::date('date', null, ['class' => 'form-control']) !!}
    </div>
    <div class="form-group">
        {!! Form::label('due_date', 'Due Date') !!}
        {!! Form::date('due_date', null, ['class' => 'form-control']) !!}
    </div>
    <div class="form-group">
        {!! Form::label('number', 'Number') !!}
        {!! Form::text('number', null, ['class' => 'form-control', 'placeholder' => 'INV-001']) !!}
    </div>

    <div class="form-group">
        {!! Form::label('amount', 'Amount') !!}
        {!! Form::text('amount', null, ['class' => 'form-control', 'placeholder' => '123.45']) !!}
    </div>
    <div class="form-group">
        {!! Form::label('description', 'Description') !!}
        {!! Form::textarea('description', null, ['class' => 'form-control', 'placeholder' => 'Enter a description for this invoice (optional)']) !!}
    </div>
    <div class="form-group float-right">
        {!! Form::submit('Create', ['class' => 'btn btn-blue']) !!}
    </div>
{!! Form::close() !!}

@endsection
