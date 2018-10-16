@extends('layouts.app')

@section('title')
    {{ $project->name }}
@endsection

@section('subtitle')
@endsection

@section('breadcrumbs')
    {!! Breadcrumbs::render('project.show', $project) !!}
@endsection

@section('content')
    @include('layouts.show-resource-table', [
        'resource' => [
            'Name' => $project->name,
            'Description' => $project->description
        ]])
@endsection
