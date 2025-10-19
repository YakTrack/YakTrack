<template>
    <layout>
        <template #breadcrumbs>
            <breadcrumbs
                :breadcrumbs="[
                    {title: 'Home',         url: route('home')},
                    {title: 'Clients',     url: route('client.index')},
                    {title: (isCreateForm ? 'Create' : 'Edit') + ' Client'},
                ]"
            ></breadcrumbs>
        </template>
        <template #title> {{ isCreateForm ? 'Create' : 'Edit' }} Client </template>

        <form-layout
            :cancel-url="route('client.index')"
            :submit-text="isCreateForm ? 'Create' : 'Update'"
            :submit-loading-text="'Saving...'"
            :processing="processing"
            @submit="submit"
        >
            <form-field
                v-model="form.name"
                type="text"
                label="Name"
                placeholder="Name of client"
                :required="true"
            />

            <form-field
                v-model="form.email"
                type="email"
                label="Email"
                placeholder="Email address of client"
                :required="true"
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
        components: {
            breadcrumbs: breadcrumbs,
            layout: layout,
            formLayout: formLayout,
            formField: formField,
        },
        props: ['client'],
        data() {
            return {
                form: this.client || {}
            }
        },
        computed: {
            isCreateForm() {
                return this.client == null;
            },
            processing() {
                return this.$inertia.processing;
            },
        },
        methods: {
            submit() {
                let verb = this.isCreateForm ? 'post' : 'patch';
                let url = this.isCreateForm ? route('client.store') : route('client.update', this.client.id);

                this.$inertia[verb](url, this.form);
            }
        }
    }

</script>