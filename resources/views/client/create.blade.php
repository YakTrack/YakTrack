@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <span class="h1"> Add Client </span>
        </div>
        <br/>
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-body">
                    {!! Form::open(['method' => 'post', 'url' => route('client.store')]) !!}
                        <div class="form-group">
                            {!! Form::label('name', 'Name') !!}
                            {!! Form::text('name', null, ['class' => 'form-control', 'placeholder' => 'name']) !!}
                        </div>
                        <div class="form-group">
                            {!! Form::label('email,', 'Email') !!}
                            {!! Form::text('email', null, ['class' => 'form-control', 'placeholder' => 'email']) !!}
                        </div>
                        <div class="form-group">
                            {!! Form::submit('Add', ['class' => 'btn btn-default']) !!}
                        </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
