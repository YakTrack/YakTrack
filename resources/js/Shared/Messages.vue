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
            ['success', 'error'].map(type => {
                return {
                    type: type,
                    message: this.$page.flash[type],
                };
            }).filter(alert => alert.message)
            .forEach(alert => this.alerts.push(alert));

            window.events.$on('notify', (notification) => {
                this.alerts.push(notification);
            });  
        },
    }
</script>