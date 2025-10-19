<template>
    <layout>
        <template #breadcrumbs>
            <breadcrumbs
                :breadcrumbs="[
                    {title: 'Home',         url: route('home')},
                    {title: 'Invoices',     url: route('invoice.index')},
                    {title: (isCreateForm ? 'Create' : 'Edit') + ' Invoice'},
                ]"
            ></breadcrumbs>
        </template>
        <template #title>{{ form.id ? 'Update' : 'Create' }} Invoice</template>

        <form-layout
            :cancel-url="route('invoice.index')"
            :submit-text="isCreateForm ? 'Create' : 'Update'"
            :submit-loading-text="'Saving...'"
            :processing="processing"
            @submit="submit"
        >
            <form-field
                v-model="form.client_id"
                type="select"
                label="Client"
                placeholder="Select a client"
                :options="clients"
                :required="true"
            />

            <form-field
                v-model="form.date"
                type="date"
                label="Date"
            />

            <form-field
                v-model="form.due_date"
                type="date"
                label="Due Date"
            />

            <form-field
                v-model="form.number"
                type="text"
                label="Number"
                placeholder="Invoice number"
            />

            <form-field
                v-model="form.amount"
                type="text"
                label="Amount"
                placeholder="123.45"
                help="Enter the invoice amount"
            />

            <form-field
                v-model="form.description"
                type="textarea"
                label="Description"
                placeholder="Enter a description for this invoice (optional)"
                :rows="3"
            />
        </form-layout>
    </layout>
</template>

<script>
    import breadcrumbs from '@/Shared/Breadcrumbs.vue';
    import layout from '@/Shared/Layout.vue';
    import formLayout from '@/Shared/FormLayout.vue';
    import formField from '@/Shared/FormField.vue';

    export default {
        props: [
            'invoice',
            'clients',
        ],
        components: {
            layout: layout,
            breadcrumbs: breadcrumbs,
            formLayout: formLayout,
            formField: formField,
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
            processing() {
                return this.$inertia.processing;
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
