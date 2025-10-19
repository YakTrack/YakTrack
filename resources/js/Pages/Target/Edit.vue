<template>
    <layout>
        <template #breadcrumbs>
            <breadcrumbs
                :breadcrumbs="[
                    {title: 'Home',         url: route('home')},
                    {title: 'Targets',     url: route('target.index')},
                    {title: (isCreateForm ? 'Create' : 'Edit') + ' Target'},
                ]"
            ></breadcrumbs>
        </template>
        <template #title>{{ form.id ? 'Update' : 'Create' }} Daily Target</template>

        <form-layout
            :cancel-url="route('target.index')"
            :submit-text="isCreateForm ? 'Create' : 'Update'"
            :submit-loading-text="'Saving...'"
            :processing="processing"
            @submit="submit"
        >
            <form-field
                v-model="form.starts_at"
                type="date"
                label="Date"
                :required="true"
            />

            <form-field
                v-model="form.value"
                type="number"
                label="Target Value (hours)"
                placeholder="Enter target hours"
                :required="true"
                help="Enter the target number of hours for this day"
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
            'target',
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
                form: this.target || {},
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
