<template>
    <layout>
        <template #breadcrumbs>
            <breadcrumbs
                :breadcrumbs="[
                    {title: 'Home',       url: route('home')},
                    {title: 'API Tokens', url: route('api-tokens.index')},
                    {title: 'Create'},
                ]"
            ></breadcrumbs>
        </template>
        <template #title> Create API Token </template>

        <form-layout
            :cancel-url="route('api-tokens.index')"
            submit-text="Create"
            submit-loading-text="Creating..."
            :processing="processing"
            @submit="submit"
        >
            <form-field
                v-model="form.name"
                type="text"
                label="Token Name"
                placeholder="e.g. CLI, CI/CD, Mobile App"
                :required="true"
                help="Give your token a descriptive name so you can identify it later."
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
        data() {
            return {
                form: {
                    name: '',
                }
            }
        },
        computed: {
            processing() {
                return this.$inertia.processing;
            },
        },
        methods: {
            submit() {
                this.$inertia.post(route('api-tokens.store'), this.form);
            }
        }
    }
</script>
