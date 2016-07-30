@extends('layouts.app')

@section('title')
    Clients
@endsection

@section('subtitle')
@endsection

@section('breadcrumbs')
    {!! Breadcrumbs::render('client.index') !!}
@endsection

@section('content')

@include('partials.modals.delete_item_modal')

<div class="box item-type-container" data-item-type="client">
    <div class="box-header with-border">
        <a href="{{ route('client.create') }}" class="btn btn-primary btn-sm pull-right">
            <i class="fa fa-plus"></i>
            Add Client
        </a>
    </div>
    @if($clients->count())
        <table class="table box-body">
            <tr>
                <th> Name </th>
                <th> Email </th>
                <th> <span class="pull-right"> Actions </span> </th>
                @foreach($clients as $client)
                <tr
                    class="item-container"
                    data-item-name="{{ $client->name }}"
                    data-item-destroy-route="{{ route('client.destroy', ['client' => $client]) }}"
                >
                    <td> {{ $client->name }} </td>
                    <td> {{ $client->email }} </td>
                    <td>
                        <div class="btn-group pull-right">
                            <a 
                                href="{{ route('client.edit', ['client' => $client]) }}" 
                                class="btn btn-default"
                            >
                                <i class="fa fa-edit"></i>
                            </a>
                            <button class="btn btn-default delete-item-button">
                                <i class="fa fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            @endforeach
        </table>
    @else
        <div class="box-body">
            You have not created any clients yet.
        </div>
    @endif
</div>

@endsection
