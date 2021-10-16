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
            this.showFlashMessages();

            window.events.$on('notify', (notification) => {
                this.alerts.push(notification);
            });  
        },
        methods: {
            showFlashMessages() {
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
            }
        },
        watch: {
            '$page.props.flash': {
                handler() {
                    this.showFlashMessages();
                },
            }
        }
    }
</script>