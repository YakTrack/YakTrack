@extends('layouts.app')

@section('title')
    Edit Invoice
@endsection

@section('subtitle')
@endsection

@section('breadcrumbs')
    {!! Breadcrumbs::render('invoice.edit', $invoices) !!}
@endsection

@section('content')

{!! Form::open(['method' => 'put', 'url' => route('invoice.update', ['invoice' => $invoices])]) !!}
    <div class="form-group">
        {!! Form::label('client_id', 'Client') !!}
        {!! Form::select('client_id', $clients->pluck('name', 'id'), $invoices->client_id, ['class' => 'form-control']) !!}
    </div>
    <div class="form-group">
        {!! Form::label('date', 'Date') !!}
        {!! Form::date('date', $invoices->date, ['class' => 'form-control']) !!}
    </div>
    <div class="form-group">
        {!! Form::label('due_date', 'Due Date') !!}
        {!! Form::date('due_date', $invoices->due_date, ['class' => 'form-control']) !!}
    </div>
    <div class="form-group">
        {!! Form::label('number', 'Number') !!}
        {!! Form::text('number', $invoices->number, ['class' => 'form-control', 'placeholder' => 'invoice name']) !!}
    </div>

    <div class="form-group">
        {!! Form::label('amount', 'Amount') !!}
        {!! Form::text('amount', $invoices->amount, ['class' => 'form-control', 'placeholder' => '123.45']) !!}
    </div>
    <div class="form-group">
        {!! Form::submit('Update', ['class' => 'btn btn-primary']) !!}
    </div>
    <div class="form-group">
        {!! Form::label('description', 'Description') !!}
        {!! Form::textarea('description', $invoices->description, ['class' => 'form-control', 'placeholder' => 'Enter a description for this invoice (optional)']) !!}
    </div>
{!! Form::close() !!}

@endsection
