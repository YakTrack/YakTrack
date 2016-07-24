@extends('layouts.app')

@section('title')
    Clients
@endsection

@section('subtitle')
@endsection

@section('breadcrumbs')
@endsection

@section('content')
    
<div class="box">
    <div class="box-header with-border">
        <a href="{{ route('client.create') }}" class="btn btn-default btn-sm">
            Add Client
        </a>
    </div>
    @if($clients->count())
        <table class="table box-body">
            <tr>
                <th> Name </th>
                <th> Email </th>
                <th> Actions </th>
                @foreach($clients as $client)
                <tr>
                    <td> {{ $client->name }} </td>
                    <td> {{ $client->email }} </td>
                    <td>
                        <a href="{{ route('client.edit', ['client' => $client]) }}" class="btn btn-default">
                            <i class="fa fa-edit"></i>
                        </a>
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
