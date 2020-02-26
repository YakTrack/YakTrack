<template>
    <layout>
        <template slot="breadcrumbs">
            <breadcrumbs
                :breadcrumbs="[
                    {title: 'Home',         url: route('home')},
                    {title: 'Invoices',     url: route('invoice.index')},
                    {title: (isCreateForm ? 'Create' : 'Edit') + ' Invoice'},
                ]"
            ></breadcrumbs>
        </template>
        <template slot="title"> {{ form.id ? 'Update' : 'Create' }} Invoice </template>
        <form :action="route('invoice.store')" method="post" @submit.prevent="submit">
            <div class="form-group">
                <label for="client_id"> Client </label>
                <client-select
                    :clients="clients"
                    v-model="form.client_id"
                />
            </div>
            <div class="form-group">
                <label for="date"> Date </label>
                <input type="date" v-model="form.date" class="form-control"/>
            </div>
            <div class="form-group">
                <label for="due_date"> Due Date </label>
                <input type="date" name="due_date" v-model="form.due_date" class="form-control"/>
            </div>
            <div class="form-group">
                <label for="number"> Number </label>
                <input type="text" class="form-control" v-model="form.number" placeholder="Invoice name" />
            </div>

            <div class="form-group">
                <label for="amount"> Amount </label>
                <input type="text" placeholder="123.45" class="form-control" name="amount" v-model="form.amount"> 
            </div>
            <div class="form-group">
                <label for="description"> Description </label>
                <textarea name="description" class="form-control" placeholder="Enter a description for this invoice (optional)" v-model="form.description"/>
            </div>
            <div class="flex mt-4">
                <div class="flex-1 mt-2">
                    <inertia-link :href="route('invoice.index')" class="btn btn-default"> Cancel </inertia-link>
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
            'invoice',
            'clients',
        ],
        components: {
            layout: layout,
            clientSelect: clientSelect,
            breadcrumbs: breadcrumbs,
        },
        data() {
            return {
                form: this.invoice || {},
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
                let url = this.isCreateForm ? route('invoice.store') : route('invoice.update', this.invoice.id);

                this.$inertia[verb](url, this.form);
            },
        }
    }
</script>
