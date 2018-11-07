<template>
    <button v-if="session.third_party_application_sessions.includes(app)" class="btn btn-light"><i class="fas fa-check text-success"></i></button>
    <div v-else>
        <input type="hidden" name="session_id" :value="session.id">
        <input type="hidden" name="third_party_application_id" :value="app.id">
        <button @click="store" class="btn btn-light"> Link </button>
    </div>
</template>

<script>
    import axios from "axios";

    export default {
        props: [
          'session',
          'app',
        ],
        methods: {
            store() {
                axios.get('third-party-application/store')
                    .then(response => {
                        window.location.href = "third-party-application";
                    }).catch(error => {
                        console.log(error)
                    });
            },
        }
    }
</script>

<style scoped>

</style>