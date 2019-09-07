<template>
    <div id="notificaton">

        <alert v-for="(alert, index) in alerts" :key="index" :alert="alert">
        </alert>

    </div>
</template>

<script>
    import Alert from '@/components/Alert';

    export default {
        components: {
            alert: Alert,
        },
        data() {
            return {
                alerts: [],
            }
        },
        created() {
            window.events.$on('notify', (notification) => {
                this.alerts.push(notification);
            });  
        },
    }
</script>