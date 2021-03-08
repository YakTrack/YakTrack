<template>
    <layout>
        <template slot="breadcrumbs">
            <breadcrumbs
                :breadcrumbs="[
                    {title: 'Home',         url: route('home')},
                    {title: 'Targets',     url: route('target.index')},
                    {title: (isCreateForm ? 'Create' : 'Edit') + ' Target'},
                ]"
            ></breadcrumbs>
        </template>
        <template slot="title"> {{ form.id ? 'Update' : 'Create' }} Daily Target </template>
        <form :action="route('target.store')" method="post" @submit.prevent="submit">
            <div class="form-group">
                <label for="starts_at"> Date </label>
                <input type="date" name="starts_at" v-model="form.starts_at" class="form-control" required/>
            </div>
            <div lass="form-group">
                <label for="due_date"> Target Value (hours) </label>
                <input type="number" name="due_date" v-model="form.value" class="form-control" step="0.25" required/>
            </div>
            <div class="flex mt-4">
                <div class="flex-1 mt-2">
                    <button-link :href="route('target.index')"> Cancel </button-link>
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
    import clientSelect from '@/Shared/ClientSelect';
    import layout from '@/Shared/Layout';

    export default {
        props: [
            'target',
            'clients',
        ],
        components: {
            layout: layout,
            clientSelect: clientSelect,
            breadcrumbs: breadcrumbs,
        },
        data() {
            return {
                form: this.target || {},
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
                let url = this.isCreateForm ? route('target.store') : route('target.update', this.target.id);

                this.$inertia[verb](url, {
                    ...{
                        duration_unit: 'days',
                        duration: 1,
                        value_unit: 'hours',
                        billable_only: 1,
                    },
                    ...this.form,
                });
            },
        }
    }
</script>
