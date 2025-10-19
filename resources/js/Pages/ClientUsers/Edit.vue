<template>
    <layout>
        <template #breadcrumbs>
            <breadcrumbs
                :breadcrumbs="[
                    {title: 'Home', url: route('home')},
                    {title: 'Client Users', url: route('client-users.index')},
                    {title: clientUser.name, url: route('client-users.show', clientUser.id)},
                    {title: 'Edit'},
                ]"
            ></breadcrumbs>
        </template>
        <template #title>Edit Client User</template>

        <form-layout
            :cancel-url="route('client-users.show', clientUser.id)"
            :submit-text="'Update Client User'"
            :submit-loading-text="'Updating...'"
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
                placeholder="Leave blank to keep current password"
                :error="errors.password"
                help="Leave blank to keep the current password"
            />

            <form-field
                v-model="form.password_confirmation"
                type="password"
                label="Confirm Password"
                placeholder="Confirm new password"
                :error="errors.password_confirmation"
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
    clientUser: Object,
    clients: Array,
    errors: Object,
})

const form = reactive({
    name: props.clientUser.name,
    email: props.clientUser.email,
    password: '',
    password_confirmation: '',
    client_id: props.clientUser.client_id,
    is_active: props.clientUser.is_active,
})

const submit = () => {
    router.put(route('client-users.update', props.clientUser.id), form)
}

const processing = computed(() => {
    return router.processing
})
</script>
