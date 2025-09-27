<template>
    <span>
        <Link
            :href="url"
            method="delete"
            as="button"
            class="btn btn-red"
            v-if="confirmIsActive"
        >
            Delete
        </Link>
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
import { Link } from '@inertiajs/vue3'

export default {
    components: {
        Link,
    },
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
