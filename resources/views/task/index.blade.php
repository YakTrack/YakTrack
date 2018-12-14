@extends('layouts.app')

@section('title')
    Tasks
@endsection

@section('subtitle')
@endsection

@section('breadcrumbs')
    {!! Breadcrumbs::render('task.index') !!}
@endsection

@section('top-right-toolbar')
    <a href="{{ route('task.create') }}" class="btn btn-blue">
        <i class="fa fa-plus"></i>
        Create Task
    </a>
@endsection

@section('content')

<div class="card">
    @if($tasks->count())
        <table class="table card-body">
            <tr>
                <th> Name </th>
                <th> Parent </th>
                <th> Sprint </th>
                <th> Project </th>
                <th> Client </th>
                <th> <span class="float-right"> Actions </span> </th>
                @foreach($tasks as $task)
                <tr
                    class="item-container"
                    data-item-name="{{ $task->name }}"
                    data-item-destroy-route="{{ route('task.destroy', ['task' => $task]) }}"
                >
                <td>
                    <a href="{{ route('task.show', ['task' => $task]) }}">
                        {{ $task->shortName }}
                    </a>
                </td>
                <td>
                    <a href="{{ route('task.show', ['task' => $task->getParent()]) }}">
                        {{ $task->getParent()->shortName }}
                    </a>
                </td>
                <td>
                    <a href="{{ route('sprint.show', ['sprint' => $task->getSprint()]) }}">
                        {{ $task->getSprint()->name }}
                    </a>
                </td>
                <td>
                    <a href="{{ route('project.show', ['project' => $task->getProject()]) }}">
                        {{ $task->getProject()->name }}
                    </a>
                </td>
                <td>
                    <a href="{{ route('client.show', ['client' => $task->getClient()]) }}">
                        {{ $task->getClient()->name }}
                    </a>
                </td>
                    <td>
                        <div class="btn-group float-right">
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
        <div class="card-body">
            You have not created any tasks yet.
        </div>
    @endif
</div>

@endsection
