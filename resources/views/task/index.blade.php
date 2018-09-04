@extends('layouts.app')

@section('title')
    Tasks
@endsection

@section('subtitle')
@endsection

@section('breadcrumbs')
    {!! Breadcrumbs::render('task.index') !!}
@endsection

@section('content')

@include('partials.modals.delete_item_modal')

<div class="box item-type-container" data-item-type="task">
    <div class="box-header with-border">
        <a href="{{ route('task.create') }}" class="btn btn-primary btn-sm pull-right">
            <i class="fa fa-plus"></i>
            Add Task
        </a>
    </div>
    @if($tasks->count())
        <table class="table box-body">
            <tr>
                <th> Name </th>
                <th> Email </th>
                <th> <span class="pull-right"> Actions </span> </th>
                @foreach($tasks as $task)
                <tr
                    class="item-container"
                    data-item-name="{{ $task->name }}"
                    data-item-destroy-route="{{ route('task.destroy', ['task' => $task]) }}"
                >
                <td>
                    <a href="{{ route('task.show', ['task' => $task]) }}">
                        {{ $task->name }}
                    </a>
                </td>
                <td>
                    <a href="{{ route('client.show', ['client' => $task->getClient()]) }}">
                        {{ $task->getClient()->name }}
                    </a>
                </td>
                    <td>
                        <div class="btn-group pull-right">
                            <a
                                href="{{ route('task.edit', ['task' => $task]) }}"
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
            You have not created any tasks yet.
        </div>
    @endif
</div>

@endsection
