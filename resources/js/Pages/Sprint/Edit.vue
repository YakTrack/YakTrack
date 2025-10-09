<template>
    <layout>
        <template #breadcrumbs>
            <breadcrumbs
                :breadcrumbs="[
                    {title: 'Home',         url: route('home')},
                    {title: 'Sprints',     url: route('sprint.index')},
                    {title: (isCreateForm ? 'Create' : 'Edit') + ' Sprint'},
                ]"
            ></breadcrumbs>
        </template>
        <template #title> {{ form.id ? 'Update' : 'Create' }} Sprint </template>
        <template #top-right-toolbar> 
        </template>
        <form @submit.prevent="submit" class="mt-2">
            <div class="form-group">
                <label for="name"> Name </label>
                <input type="text" class="form-control" v-model="form.name" placeholder="Sprint name" />
            </div>
            <div class="form-group">
                <label for="project_id"> Project </label>
                <multi-select
                    :options="projects"
                    label="name"
                    track-by="id"
                    v-model="selectedProject"
                    placeholder="Select a project"
                ></multi-select>
            </div>
            <div class="form-group">
                <label for="is_open"> Is Open </label>
                <input type="checkbox" class="form-checkbox" name="is_open" v-model="form.is_open"/>
            </div>
            <div class="flex mt-4">
                <div class="flex-1 mt-2">
                    <button-link :href="route('sprint.index')"> Cancel </button-link>
                </div>
                <div class="flex-1 float-right">
                    <button class="btn btn-blue float-right"> {{ isCreateForm ? 'Create' : 'Update' }} </button>
                </div>
            </div>
        </form>
    </layout>
</template>

<script>
    import breadcrumbs from '@/Shared/Breadcrumbs.vue';
    import layout from '@/Shared/Layout.vue';
    import multiSelect from 'vue-multiselect';

    export default {
        props: [
            'sprint',
            'projects',
        ],
        components: {
            breadcrumbs: breadcrumbs,
            layout: layout,
            multiSelect: multiSelect,
        },
        data() {
            return {
                selectedProject: (this.sprint && this.sprint.project_id) ? this.projects.find(p => p.id == this.sprint.project_id) : null,
                form: this.sprint || {
                    name: '',
                    is_open: false,
                },
            };
        },
        computed: {
            isCreateForm() {
                return this.form.id == null;
            },
            selectedProjectId() {
                return this.selectedProject ? this.selectedProject.id : null;
            },
        },
        methods: {
            submit() {
                let verb = this.isCreateForm ? 'post' : 'patch';
                let url = this.isCreateForm ? route('sprint.store') : route('sprint.update', this.sprint.id);

                this.$inertia[verb](url, {
                    ...this.form,
                    project_id: this.selectedProjectId,
                });
            },
        }
    }
</script>

