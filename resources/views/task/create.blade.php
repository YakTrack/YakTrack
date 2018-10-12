@extends('layouts.app')

@section('title')
    Create Task
@endsection

@section('subtitle')
@endsection

@section('breadcrumbs')
    {!! Breadcrumbs::render('task.create') !!}
@endsection

@section('content')

    <create-task-form
        url="{{ route('task.store') }}"
        :projects='{{ $projects->toJson() }}'
        :sprints='{{ $sprints->toJson() }}'
        :tasks='{{ $tasks->toJson() }}'
    ></create-task-form>

@endsection
