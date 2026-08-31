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
        <template #title>{{ form.id ? 'Update' : 'Create' }} Sprint</template>

        <form-layout
            :cancel-url="route('sprint.index')"
            :submit-text="isCreateForm ? 'Create' : 'Update'"
            :submit-loading-text="'Saving...'"
            :processing="processing"
            @submit="submit"
        >
            <form-field
                v-model="form.name"
                type="text"
                label="Name"
                placeholder="Sprint name"
                :required="true"
            />

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Projects
                </label>
                <multi-select
                    :options="projects"
                    label="name"
                    track-by="id"
                    v-model="selectedProjects"
                    placeholder="Select projects"
                    :multiple="true"
                />
            </div>

            <form-field
                v-model="form.is_open"
                type="checkbox"
                checkbox-label="Is Open"
            />
        </form-layout>
    </layout>
</template>

<script>
    import breadcrumbs from '@/Shared/Breadcrumbs.vue';
    import layout from '@/Shared/Layout.vue';
    import multiSelect from 'vue-multiselect';
    import formLayout from '@/Shared/FormLayout.vue';
    import formField from '@/Shared/FormField.vue';

    export default {
        props: [
            'sprint',
            'projects',
            'focusedClientId',
        ],
        components: {
            breadcrumbs: breadcrumbs,
            layout: layout,
            multiSelect: multiSelect,
            formLayout: formLayout,
            formField: formField,
        },
        data() {
            return {
                selectedProjects: this.initialSelectedProjects(),
                form: this.sprint ? { ...this.sprint, is_open: this.sprint.is_open } : {
                    name: '',
                    is_open: false,
                },
            };
        },
        computed: {
            isCreateForm() {
                return this.form.id == null;
            },
            selectedProjectIds() {
                return (this.selectedProjects || []).map(p => p.id);
            },
            processing() {
                return this.$inertia.processing;
            },
        },
        methods: {
            initialSelectedProjects() {
                if (this.sprint && this.sprint.projects) {
                    return this.sprint.projects.map(p => ({ id: p.id, name: p.name }));
                }

                if (this.focusedClientId) {
                    return (this.projects || [])
                        .filter(p => p.client_id == this.focusedClientId)
                        .map(p => ({ id: p.id, name: p.name }));
                }

                return [];
            },
            submit() {
                const verb = this.isCreateForm ? 'post' : 'patch';
                const url = this.isCreateForm ? route('sprint.store') : route('sprint.update', this.sprint.id);

                this.$inertia[verb](url, {
                    ...this.form,
                    project_ids: this.selectedProjectIds,
                });
            },
        }
    }
</script>

