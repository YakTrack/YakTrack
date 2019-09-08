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
@endsection

@section('content')

<template>
<layout>
<template slot="title"> Sprints </template>
<template slot="top-right-toolbar"> 
    <inertia-link :href="route('sprint.create')" class="btn btn-blue">
        <i class="fa fa-plus"></i>
        Create Sprint
    </inertia-link>
</template>
<div class="card item-type-container" data-item-type="sprint">
    <table class="table card-body" v-if="sprints.length">
        <tr>
            <th> Name </th>
            <th> Project </th>
            <th> Status </th>
            <th> <span class="float-right"> Actions </span> </th>
            <tr
                v-for="sprint in sprints"
                class="item-container"
                :data-item-name="sprint.name"
                :data-item-destroy-route="route('sprint.destroy', sprint.id)"
            >
                <td>
                    <inertia-link :href="route('sprint.show', sprint.id)">
                        {{ sprint.name }}
                    </inertia-link>
                </td>
                <td>
                    <inertia-link :href="route('project.show', sprint.project_id)">
                        {{ sprint.project ? sprint.project.name : '' }}
                    </inertia-link>
                </td>
                <td>
                    <div class="text-green" v-if="sprint.is_open"> Open </div>
                </td>
                <td>
                    <div class="mx-auto btn-group float-right">
                        <inertia-link
                            :href="route('sprint.edit', sprint.id)"
                            class="btn btn-default"
                        >
                            <i class="fa fa-edit"></i>
                        </inertia-link>
                        <button class="btn btn-default delete-item-button">
                            <i class="fa fa-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
    </table>
    <div class="card-body" v-else>
        You have not created any sprints yet.
    </div>
</div>
</layout>
</template>

<script>
    import layout from '@/Shared/Layout';

    export default {
        props: [
            'sprints',
        ],
        components: {
            layout: layout,
        },
    }
</script>
