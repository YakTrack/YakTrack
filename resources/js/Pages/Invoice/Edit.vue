<template>
    <layout>
        <template slot="title"> {{ form.id ? 'Update' : 'Create' }} Invoice </template>
        <template slot="top-right-toolbar"> 
        </template>
        <div>
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
            <div class="form-group">
                <button type="submit" class="btn btn-primary" @click="submit()"> {{ form.id ? 'Update' : 'Create' }} </button>
            </div>
        </div>
    </layout>
</template>

<script>
    import layout from '@/Shared/Layout';
    import clientSelect from '@/Shared/ClientSelect';

    export default {
        props: [
            'invoice',
            'clients',
        ],
        components: {
            layout: layout,
            clientSelect: clientSelect,
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
