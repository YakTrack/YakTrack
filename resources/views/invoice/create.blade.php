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

@javascript([
])

<div class="card box-default">
    <div class="card-body">
    {!! Form::open(['method' => 'post', 'url' => route('invoice.store')]) !!}
        <div class="form-group">
            {!! Form::label('number', 'Number') !!}
            {!! Form::text('number', null, ['class' => 'form-control', 'placeholder' => 'INV-001']) !!}
        </div>
        <div class="form-group">
            {!! Form::label('amount', 'Amount') !!}
            {!! Form::text('amount', null, ['class' => 'form-control', 'placeholder' => '123.45']) !!}
        </div>
        <div class="form-group">
            {!! Form::label('date', 'Date') !!}
            {!! Form::date('date', null, ['class' => 'form-control']) !!}
        </div>
        <div class="form-group">
            {!! Form::submit('Create', ['class' => 'btn btn-default']) !!}
        </div>
    {!! Form::close() !!}
    </div>
</div>

@endsection
