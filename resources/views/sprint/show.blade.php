@extends('layouts.app')

@section('title')
    {{ $sprint->name }}
@endsection

@section('subtitle')
@endsection

@section('breadcrumbs')
    {!! Breadcrumbs::render('sprint.show', $sprint) !!}
@endsection

@section('content')

@include('layouts.show-resource-table', [
    'resource' => [
        'Name' => $sprint->name ,
        'Project' =>
            link_to(
                route('project.show', ['project' => $sprint->project]),
                $sprint->project->name
            )
    ]])

@endsection
