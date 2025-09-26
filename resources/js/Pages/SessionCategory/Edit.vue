<template>
    <layout>
        <template slot="breadcrumbs">
            <breadcrumbs
                :breadcrumbs="[
                    {title: 'Home', url: route('home')},
                    {title: 'Session Categories', url: route('session-category.index')},
                    {title: (isCreateForm ? 'Create' : 'Edit') + ' Session Category'},
                ]"
            ></breadcrumbs>
        </template>
        <template slot="title">{{ isCreateForm ? 'Create' : 'Edit' }} Session Category</template>
        <form @submit.prevent="submit" class="mt-2">
            <div class="form-group">
                <label for="name">Category Name</label>
                <input
                    type="text"
                    name="name"
                    class="form-control"
                    placeholder="Enter category name"
                    v-model="form.name"
                    required
                />
            </div>

            <div class="form-group">
                <label for="description">Description</label>
                <textarea
                    name="description"
                    class="form-control"
                    placeholder="Describe this session category"
                    v-model="form.description"
                    rows="3"
                ></textarea>
            </div>

            <div class="flex mt-4">
                <div class="flex-1 mt-2">
                    <button-link :href="route('session-category.index')">Cancel</button-link>
                </div>
                <div class="flex-1 float-right">
                    <loading-button
                        :loading="form.processing"
                        class="btn btn-blue float-right"
                    >
                        {{ isCreateForm ? 'Create' : 'Update' }}
                    </loading-button>
                </div>
            </div>
        </form>
    </layout>
</template>

<script>
import Layout from '../../Shared/Layout.vue'
import Breadcrumbs from '../../Shared/Breadcrumbs.vue'
import ButtonLink from '../../Shared/ButtonLink.vue'
import LoadingButton from '../../Shared/LoadingButton.vue'

export default {
    components: {
        Layout,
        Breadcrumbs,
        ButtonLink,
        LoadingButton,
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