@extends('layouts.app')

@section('title')
    Sprints
@endsection

@section('subtitle')
@endsection

@section('breadcrumbs')
    {!! Breadcrumbs::render('sprint.index') !!}
@endsection

@section('content')

@include('partials.modals.delete_item_modal')

<div class="card item-type-container" data-item-type="sprint">
    <div class="card-header with-border">
        <a href="{{ route('sprint.create') }}" class="btn btn-primary btn-sm pull-right">
            <i class="fa fa-plus"></i>
            Add Sprint
        </a>
    </div>
    @if($sprints->count())
        <table class="table card-body">
            <tr>
                <th> Name </th>
                <th> Project </th>
                <th> <span class="pull-right"> Actions </span> </th>
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
                        <div class="btn-group pull-right">
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
