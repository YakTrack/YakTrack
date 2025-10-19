<template>
    <layout>
        <template #breadcrumbs>
            <breadcrumbs
                :breadcrumbs="[
                    {title: 'Home', url: route('home')},
                    {title: 'Client Users', url: route('client-users.index')},
                    {title: 'Create Client User'},
                ]"
            ></breadcrumbs>
        </template>
        <template #title>Create Client User</template>

        <form-layout
            :cancel-url="route('client-users.index')"
            :submit-text="'Create Client User'"
            :submit-loading-text="'Creating...'"
            :processing="processing"
            @submit="submit"
        >
            <form-field
                v-model="form.name"
                type="text"
                label="Name"
                placeholder="Enter client user name"
                :error="errors.name"
                :required="true"
            />

            <form-field
                v-model="form.email"
                type="email"
                label="Email"
                placeholder="Enter email address"
                :error="errors.email"
                :required="true"
            />

            <form-field
                v-model="form.password"
                type="password"
                label="Password"
                placeholder="Enter password"
                :error="errors.password"
                :required="true"
            />

            <form-field
                v-model="form.password_confirmation"
                type="password"
                label="Confirm Password"
                placeholder="Confirm password"
                :error="errors.password_confirmation"
                :required="true"
            />

            <form-field
                v-model="form.client_id"
                type="select"
                label="Client"
                placeholder="Select a client"
                :options="clients"
                :error="errors.client_id"
                :required="true"
            />

            <form-field
                v-model="form.is_active"
                type="checkbox"
                checkbox-label="Active"
                help="Active users can log in to the client portal"
            />
        </form-layout>
    </layout>
</template>

<script setup>
import { Link, router } from '@inertiajs/vue3'
import { reactive, computed } from 'vue'
import layout from '@/Shared/Layout.vue'
import breadcrumbs from '@/Shared/Breadcrumbs.vue'
import formLayout from '@/Shared/FormLayout.vue'
import formField from '@/Shared/FormField.vue'

const props = defineProps({
    clients: Array,
    errors: Object,
})

const form = reactive({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    client_id: '',
    is_active: true,
})

const submit = () => {
    router.post(route('client-users.store'), form)
}

const processing = computed(() => {
    return router.processing
})
</script>
