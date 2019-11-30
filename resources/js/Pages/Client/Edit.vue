<template>
    <layout>
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
            <div class="form-group float-right">
                <button class="btn btn-blue"> Submit </button>
            </div>
        </form>
    </layout>
</template>

<script>
    import layout from '@/Shared/Layout';

    export default {
        components: {
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