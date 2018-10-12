<template>
    <input type="hidden" name="_token" :value="token"/>
</template>

<script>
    export default {
        computed: {
            token() {
                return document.head.querySelector('meta[name="csrf-token"]').content;
            }
        }
    }
</script>