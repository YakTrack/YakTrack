@extends('layouts.app')

@section('title')
    Clients
@endsection

@section('subtitle')
@endsection

@section('breadcrumbs')
    {!! Breadcrumbs::render('client.index') !!}
@endsection

@section('top-right-toolbar')
    <a href="{{ route('client.create') }}" class="btn btn-blue">
        <i class="fa fa-plus"></i>
        Create Client
    </a>
@endsection

@section('content')

<div class="card">
    @if($clients->count())
        <table class="table card-body">
            <tr>
                <th> Name </th>
                <th> Email </th>
                <th> <span class="float-right"> Actions </span> </th>
                @foreach($clients as $client)
                <tr
                    class="item-container"
                    data-item-name="{{ $client->name }}"
                    data-item-destroy-route="{{ route('client.destroy', ['client' => $client]) }}"
                >
                <td>
                    <a href="{{ route('client.show', ['client' => $client]) }}">
                        {{ $client->name }}
                    </a>
                </td>
                    <td> {{ $client->email }} </td>
                    <td>
                        <div class="btn-group float-right">
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
        <div class="card-body">
            You have not created any clients yet.
        </div>
    @endif
</div>

@endsection
