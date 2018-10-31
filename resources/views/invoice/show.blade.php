@extends('layouts.app')

@section('title')
    Invoice #{{ $invoice->id }}
@endsection

@section('subtitle')
@endsection

@section('breadcrumbs')
    {!! Breadcrumbs::render('invoice.show', $invoice) !!}
@endsection

@section('content')

@include('layouts.show-resource-table', [
    'resource' => [
        'Client' => $invoice->getClient()->name,
        'Invoice Number' => $invoice->number,
        'Invoice Date' => $invoice->date,
        'Due Date' => $invoice->due_date,
        'Amount' => $invoice->amountForHumans,
        'Total Session Hours' => $invoice->sessions->totalDurationForHumans(),
        'Total Invoiced Hours' => $invoice->totalHours,
        'Is Sent' => $invoice->is_sent ? 'Yes' : 'No',
        'Is Paid' => $invoice->is_paid ? 'Yes' : 'No',
    ]])

    <h2 class="mb-4"> Linked Sessions </h2>
    @include('partials.session.session-table', ['sessions' => $invoice->sessions])

@endsection

