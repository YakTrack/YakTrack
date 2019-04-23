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
                ),
            'Total Time' => $sprint->sessions->totalDurationForHumans()
        ]])

    <div class="rounded p-2 bg-white shadow mb-8">
        <div class="p-2">
            <h2 class="text-grey-darker"> Sessions </h2>
        </div>
        <table class="table rounded p-4 bg-white">
            <tr>
                <th class="text-grey"> Task </th>
                <th class="text-grey"> Project </th>
                <th class="text-grey"> Total Time </th>
            </tr>
            @foreach($tasks as $task)
            <tr>
                <td class="p-2 text-grey-dark"> {{ $task->getProject()->name }} </td>
                <td class="p-2 text-grey-dark"> {{ $task->name }} </td>
                <td class="p-2 text-grey-dark"> {{ $task->totalDurationInSprintForHumans }} </td>
            </tr>
            @endforeach
        </table>
    </div>

@endsection
