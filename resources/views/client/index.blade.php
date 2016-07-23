@extends('layouts.app')

@section('content')
    
<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header"> Clients </h1>
    </div>
    
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading clearfix">
                <a href="{{ route('client.create') }}" class="btn btn-default pull-right">
                    Add Client
                </a>
            </div>
            @if($clients->count())
            <table class="table panel-body">
                @foreach($clients as $client)
                    <tr>
                        <td> {{ $client->name }} </td>
                        <td></td>
                    </tr>
                @endforeach
            </table>
            @else
                <div class="panel-body">
                    You have not created any clients yet.
                </div>
            @endif
        </div>
    </div>
</div>

@endsection
