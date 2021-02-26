<template>
    <span>
        <inertia-link
            :href="url"
            method="delete"
            v-if="confirmIsActive"
        >
            <button class="btn btn-red">
                Delete
            </button>
        </inertia-link>
        <button class="btn btn-default" v-if="confirmIsActive" @click="toggleConfirm()">
            Cancel
        </button>
        <button
            class="btn btn-default"
            @click="toggleConfirm()"
            v-if="!confirmIsActive"
            :disabled="isDisabled"
        >
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
        'isDisabled',
    ],
    methods: {
        toggleConfirm() {
            this.confirmIsActive = !this.confirmIsActive;
        }
    }
}

</script>
