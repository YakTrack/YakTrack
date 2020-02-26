<template>
    <layout>
        <template slot="breadcrumbs">
            <breadcrumbs
                :breadcrumbs="[
                    {title: 'Home',         url: route('home')},
                    {title: 'Projects',     url: route('project.index')},
                    {title: (isCreateForm ? 'Create' : 'Edit') + ' Project'},
                ]"
            ></breadcrumbs>
        </template>
        <form @submit.prevent="submit" class="mt-2">
            <div class="form-group">
                <label for="name"> Name </label>
                <input type="text" name="name" v-model="form.name" class="form-control" placeholder="Project name"/>
            </div>
            <div class="form-group">
                <label for="description"> Description </label>
                <textarea name="description" v-model="form.description" class="form-control" placeholder="Project description"/>
            </div>
            <div class="form-group">
                <label for="client_id"> Client </label>
                <client-select
                    :clients="clients"
                    v-model="form.client_id"
                />
            </div>
            <div class="form-group mt-2 flex">
                <div class="flex-1">
                </div>
                <div class="text-right flex-1">
                </div>
            </div>
            <div class="flex">
                <div class="flex-1 mt-2">
                    <inertia-link :href="route('project.index')" class="btn btn-default"> Cancel </inertia-link>
                </div>
                <div class="flex-1 float-right">
                    <button class="btn btn-blue float-right"> {{ isCreateForm ? 'Create' : 'Update' }} </button>
                </div>
            </div>
        </form>
    </layout>
</template>

<script>

    import clientSelect from '@/Shared/ClientSelect';
    import breadcrumbs from '@/Shared/Breadcrumbs';
    import layout from '@/Shared/Layout';

    export default {
        props: [
            'project',
            'clients',
        ],
        data() {
            return {
                form: this.project || {},
            }
        },
        components: {
            clientSelect: clientSelect,
            breadcrumbs: breadcrumbs,
            layout: layout,
        },
        methods: {
            submit() {
                this.$inertia[this.isCreateForm ? 'post' : 'patch'](
                    this.isCreateForm ? route('project.store') : route('project.update', this.project.id),
                    this.form
                );
            }
        },
        computed: {
            isCreateForm() {
                return this.project == null;
            },
        }
    }

</script>