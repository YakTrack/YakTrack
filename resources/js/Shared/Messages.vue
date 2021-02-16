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
            [
                {
                    type: 'success',
                    message: this.$page.props.flash.success,
                },
                {
                    type: 'error',
                    message: this.$page.props.flash.error,
                },
            ].filter(alert => alert.message)
            .forEach(alert => this.alerts.push(alert));

            Object.keys(this.$page.props.errors).forEach(key => {
                this.alerts.push({
                    type: 'error',
                    message: this.$page.errors[key].join("\n"),
                });
            })

            window.events.$on('notify', (notification) => {
                this.alerts.push(notification);
            });  
        },
    }
</script>