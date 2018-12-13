@extends('layouts.app')

@section('title')
    Sprints
@endsection

@section('subtitle')
@endsection

@section('breadcrumbs')
    {!! Breadcrumbs::render('sprint.index') !!}
@endsection

@section('top-right-toolbar')
    <a href="{{ route('sprint.create') }}" class="btn btn-blue">
        <i class="fa fa-plus"></i>
        Create Sprint
    </a>
@endsection

@section('content')

<div class="card item-type-container" data-item-type="sprint">
    @if($sprints->count())
        <table class="table card-body">
            <tr>
                <th> Name </th>
                <th> Project </th>
                <th> Status </th>
                <th> <span class="float-right"> Actions </span> </th>
                @foreach($sprints as $sprint)
                <tr
                    class="item-container"
                    data-item-name="{{ $sprint->name }}"
                    data-item-destroy-route="{{ route('sprint.destroy', ['sprint' => $sprint]) }}"
                >
                    <td>
                        <a href="{{ route('sprint.show', ['sprint' => $sprint]) }}">
                            {{ $sprint->name }}
                        </a>
                    </td>
                    <td>
                        <a href="{{ route('project.show', ['client' => $sprint->project]) }}">
                            {{ $sprint->project->name }}
                        </a>
                    </td>
                    <td>
                        @if($sprint->is_open) 
                            <div class="text-green"> Open </div>
                        @endif
                    </td>
                    <td>
                        <div class="mx-auto btn-group float-right">
                            <a
                                href="{{ route('sprint.edit', ['sprint' => $sprint]) }}"
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
            You have not created any sprints yet.
        </div>
    @endif
</div>

@endsection
