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

@javascript([
    'projects' => $projects,
    'sprints' => $sprints
])

<div class="card box-default">
    <div class="card-body">
    {!! Form::open(['method' => 'post', 'url' => route('task.store')]) !!}
        <div class="form-group">
            {!! Form::label('name', 'Name') !!}
            {!! Form::text('name', null, ['class' => 'form-control', 'placeholder' => 'Project name']) !!}
        </div>
        <div class="form-group">
            {!! Form::label('description,', 'Description') !!}
            {!! Form::textarea('description', null, ['class' => 'form-control', 'placeholder' => 'Describe this task', ]) !!}
        </div>
        <div class="form-group">
            {!! Form::label('project_id', 'Project') !!}
            <v-select :value.sync="selectedProject" :options="projects" label="name" name="project_id">
        </div>
        <div class="form-group">
            {!! Form::label('sprint_id', 'Sprint') !!}
            <v-select :value.sync="selectedSprint" :options="sprints" label="name" name="sprint_id">
        </div>
        <div class="form-group">
            {!! Form::label('parent_id', 'Parent Task') !!}
            <v-select :value.sync="selectedParentTask" :options="[]" label="name" name="parent_id">
        </div>
        <div class="form-group">
            {!! Form::submit('Create', ['class' => 'btn btn-default']) !!}
        </div>
    {!! Form::close() !!}
    </div>
</div>

@endsection
