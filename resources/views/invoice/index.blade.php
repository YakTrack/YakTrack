@extends('layouts.app')

@section('title')
    Invoices
@endsection

@section('subtitle')
@endsection

@section('breadcrumbs')
    {!! Breadcrumbs::render('invoice.index') !!}
@endsection

@section('top-right-toolbar')
    <a href="{{ route('invoice.create') }}" class="btn btn-blue">
        <i class="fa fa-plus"></i>
        Create Invoice
    </a>
@endsection

@section('content')

<div class="card">
    @if($invoices->count())
        <table class="table card-body table-hover">
            <thead>
                <tr>
                    <th> Number </th>
                    <th> Date </th>
                    <th> Due Date </th>
                    <th> Client </th>
                    <th> Session Hours </th>
                    <th> Invoiced Hours </th>
                    <th> Amount </th>
                    <th> <span class="float-right"> Actions </span> </th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoices as $invoice)
                    <tr
                        class="item-container"
                        data-item-name="{{ $invoice->number }}"
                        data-item-destroy-route="{{ route('invoice.destroy', ['invoice' => $invoice]) }}"
                    >
                        <td>
                            <a href="{{ route('invoice.show', ['invoice' => $invoice]) }}">
                                {{ $invoice->number }}
                            </a>
                        </td>
                        <td> {{ $invoice->date }} </td>
                        <td> {{ $invoice->due_date }} </td>
                        <td>
                            <a href="{{ route('client.show', ['client' => $invoice->getClient()]) }}">
                                {{ $invoice->getClient()->name }}
                            </a>
                        </td>
                        <td> {{ $invoice->sessions->totalDurationForHumans() == '0:00:00' ? '-' : $invoice->sessions->totalDurationForHumans() }} </td>
                        <td> {{ $invoice->totalHours ?? '-' }} </td>
                        <td> {{ $invoice->amountForHumans }} </td>
                        <td>
                            <div class="btn-group float-right">
                                <a
                                    href="{{ route('invoice.edit', ['invoice' => $invoice]) }}"
                                    class="btn btn-default"
                                >
                                    <i class="fa fa-edit"></i>
                                </a>
                                <form action="{{ route('invoice.destroy', $invoice->id) }}" method="post">
                                    {{ csrf_field() }}
                                    <input type="hidden" name="_method" value="DELETE">
                                    <button class="btn btn-default delete-item-button">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="">
            You have not created any invoices yet.
        </div>
    @endif
</div>
@endsection
