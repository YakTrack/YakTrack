<template>
    <button :disabled="isLoading" class="flex items-center">
        <div v-if="isLoading" class="btn-spinner mr-2" />
        <slot />
    </button>
</template>

<script>

export default {
    props: {
        isLoading: Boolean,
    },
}

</script>