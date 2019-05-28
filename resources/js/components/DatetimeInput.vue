<template>
    <div>
        {{ datetime | toDateTimeForHumans }}
        <button class="btn"><i class="fa fa-edit"></i></button>
    </div>
</template>

<script>

export default {
    data() {
        return {
            datetime: new Date,
        }
    }
}


</script>