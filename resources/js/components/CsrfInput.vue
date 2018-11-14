<template>
    <input type="hidden" name="_token" :value="token"/>
</template>

<script>
    export default {
        computed: {
            token() {
                return window.token;
            }
        }
    }
</script>