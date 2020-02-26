<template>
    <input
        class="form-control"
        type="datetime-local"
        v-model="datetime"
        pattern="[0-9]{4}-[0-9]{2}-[0-9]{2}T[0-9]{2}:[0-9]{2}:[0-9]{2}"
        step="1"
    ></input>
</template>

<script>

export default {
    props: [
        'value',
    ],
    data() {
        return {
            datetime: this.value,
        }
    },
    watch: {
        datetime(newValue) {
            this.$emit('input', newValue);
        },
        value(newValue) {
            this.datetime = newValue;
        },
    },
}


</script>