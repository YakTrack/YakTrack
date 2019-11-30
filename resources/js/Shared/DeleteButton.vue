<template>
    <span class="p-1">
        <inertia-link :href="url" method="delete" class="btn btn-default bg-red-500 clickable" v-if="confirmIsActive">
            Delete
        </inertia-link>
        <button class="btn btn-default" v-if="confirmIsActive">
            Cancel
        </button>
        <button class="btn btn-default" @click="toggleConfirm()" v-if="!confirmIsActive">
            <slot></slot>
        </button>
    </span>
</template>

<script>

export default {
    data() {
        return {
            confirmIsActive: false,
        }
    },
    props: [
        'url',
    ],
    methods: {
        toggleConfirm() {
            this.confirmIsActive = !this.confirmIsActive;
        }
    }
}

</script>