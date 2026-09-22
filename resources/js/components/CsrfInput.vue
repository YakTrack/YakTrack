<template>
    <input type="hidden" name="_token" :value="csrfToken"/>
</template>

<script setup>
    import { csrfToken } from '@/csrfRefresh'
</script>
