@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <span class="h1"> Clients </span>
            <a href="" class="btn btn-default pull-right"> Add Button </a>
        </div>
        <br/>
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default">
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
</div>
@endsection
