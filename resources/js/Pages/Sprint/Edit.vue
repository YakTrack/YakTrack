<template>
    <layout>
        <template slot="breadcrumbs">
            <breadcrumbs
                :breadcrumbs="[
                    {title: 'Home',         url: route('home')},
                    {title: 'Sprints',     url: route('sprint.index')},
                    {title: (isCreateForm ? 'Create' : 'Edit') + ' Sprint'},
                ]"
            ></breadcrumbs>
        </template>
        <template slot="title"> {{ form.id ? 'Update' : 'Create' }} Sprint </template>
        <template slot="top-right-toolbar"> 
        </template>
        <form @submit.prevent="submit" class="mt-2">
            <div class="form-group">
                <label for="name"> Name </label>
                <input type="text" class="form-control" v-model="form.name" placeholder="Sprint name" />
            </div>
            <div class="form-group">
                <label for="project_id"> Project </label>
                <project-select
                    :projects="projects"
                    v-model="form.project_id"
                />
            </div>
            <div class="form-group">
                <label for="is_open"> Is Open </label>
                <input type="checkbox" name="is_open" v-model="form.is_open"/>
            </div>
            <div class="flex mt-4">
                <div class="flex-1 mt-2">
                    <inertia-link :href="route('sprint.index')" class="btn btn-default"> Cancel </inertia-link>
                </div>
                <div class="flex-1 float-right">
                    <button class="btn btn-blue float-right"> {{ isCreateForm ? 'Create' : 'Update' }} </button>
                </div>
            </div>
        </form>
    </layout>
</template>

<script>
    import breadcrumbs from '@/Shared/Breadcrumbs';
    import layout from '@/Shared/Layout';
    import projectSelect from '@/Shared/ProjectSelect';

    export default {
        props: [
            'sprint',
            'projects',
        ],
        components: {
            breadcrumbs: breadcrumbs,
            layout: layout,
            projectSelect: projectSelect,
        },
        data() {
            return {
                form: this.sprint || {},
            };
        },
        computed: {
            isCreateForm() {
                return this.form.id == null;
            },
        },
        methods: {
            submit() {
                let verb = this.isCreateForm ? 'post' : 'patch';
                let url = this.isCreateForm ? route('sprint.store') : route('sprint.update', this.sprint.id);

                this.$inertia[verb](url, this.form);
            },
        }
    }
</script>

