<template>
    <layout>
        <template #breadcrumbs>
            <breadcrumbs
                :breadcrumbs="[
                    {title: 'Home', url: route('home')},
                    {title: 'Session Categories', url: route('session-category.index')},
                    {title: (isCreateForm ? 'Create' : 'Edit') + ' Session Category'},
                ]"
            ></breadcrumbs>
        </template>
        <template #title>{{ isCreateForm ? 'Create' : 'Edit' }} Session Category</template>

        <form-layout
            :cancel-url="route('session-category.index')"
            :submit-text="isCreateForm ? 'Create' : 'Update'"
            :submit-loading-text="'Saving...'"
            :processing="form.processing"
            @submit="submit"
        >
            <form-field
                v-model="form.name"
                type="text"
                label="Category Name"
                placeholder="Enter category name"
                :required="true"
            />

            <form-field
                v-model="form.description"
                type="textarea"
                label="Description"
                placeholder="Describe this session category"
                :rows="3"
            />
        </form-layout>
    </layout>
</template>

<script>
import Layout from '@/Shared/Layout.vue'
import Breadcrumbs from '@/Shared/Breadcrumbs.vue'
import formLayout from '@/Shared/FormLayout.vue'
import formField from '@/Shared/FormField.vue'

export default {
    components: {
        Layout,
        Breadcrumbs,
        formLayout,
        formField,
    },
    props: {
        sessionCategory: {
            type: Object,
            default: null
        },
    },
    data() {
        return {
            form: this.$inertia.form({
                name: this.sessionCategory?.name || '',
                description: this.sessionCategory?.description || '',
            })
        }
    },
    computed: {
        isCreateForm() {
            return !this.sessionCategory;
        }
    },
    methods: {
        submit() {
            if (this.isCreateForm) {
                this.form.post(route('session-category.store'));
            } else {
                this.form.patch(route('session-category.update', this.sessionCategory?.id));
            }
        }
    }
}
</script>