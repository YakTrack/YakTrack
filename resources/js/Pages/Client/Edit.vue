<template>
    <layout>
        <template slot="breadcrumbs">
            <breadcrumbs
                :breadcrumbs="[
                    {title: 'Home',         url: route('home')},
                    {title: 'Clients',     url: route('client.index')},
                    {title: (isCreateForm ? 'Create' : 'Edit') + ' Client'},
                ]"
            ></breadcrumbs>
        </template>
        <template slot="title"> {{ isCreateForm ? 'Create' : 'Edit' }} Client </template>
        <form :action="route('client.store')" method="post" @submit.prevent="submit">
            <div class="form-group">
                <label for="name"> Name </label>
                <input type="text" name="name" v-model="form.name" placeholder="Name of client" class="form-control" />
            </div>
            <div class="form-group">
                <label for="email"> Email </label>
                <input type="email" name="email" v-model="form.email" placeholder="Email address of client" class="form-control" />
            </div>
            <div class="flex mt-4">
                <div class="flex-1 mt-2">
                    <inertia-link :href="route('client.index')" class="btn btn-default"> Cancel </inertia-link>
                </div>
                <div class="flex-1 float-right">
                    <button class="btn btn-blue float-right"> {{ isCreateForm ? 'Create' : 'Update' }} </button>
                </div>
            </div>
        </form>
    </layout>
</template>

<script>
    import breadcrumbs from '@/Shared/Breadcrumbs';
    import layout from '@/Shared/Layout';

    export default {
        components: {
            breadcrumbs: breadcrumbs,
            layout: layout,
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