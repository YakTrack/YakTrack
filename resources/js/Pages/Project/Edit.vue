<template>
    <layout>
        <template #breadcrumbs>
            <breadcrumbs
                :breadcrumbs="[
                    {title: 'Home',         url: route('home')},
                    {title: 'Projects',     url: route('project.index')},
                    {title: (isCreateForm ? 'Create' : 'Edit') + ' Project'},
                ]"
            ></breadcrumbs>
        </template>
        <template #title>{{ isCreateForm ? 'Create' : 'Edit' }} Project</template>

        <form-layout
            :cancel-url="route('project.index')"
            :submit-text="isCreateForm ? 'Create' : 'Update'"
            :submit-loading-text="'Saving...'"
            :processing="processing"
            @submit="submit"
        >
            <form-field
                v-model="form.name"
                type="text"
                label="Name"
                placeholder="Project name"
                :required="true"
            />

            <form-field
                v-model="form.description"
                type="textarea"
                label="Description"
                placeholder="Project description"
                :rows="3"
            />

            <form-field
                v-model="form.task_code_prefix"
                type="text"
                label="Task Code Prefix (Optional)"
                placeholder="e.g., ABCD"
            />

            <form-field
                v-model="form.client_id"
                type="select"
                label="Client"
                placeholder="Select a client"
                :options="clients"
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
            'project',
            'clients',
        ],
        data() {
            return {
                form: this.project || {},
            }
        },
        components: {
            breadcrumbs: breadcrumbs,
            layout: layout,
            formLayout: formLayout,
            formField: formField,
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
            processing() {
                return this.$inertia.processing;
            },
        }
    }

</script>