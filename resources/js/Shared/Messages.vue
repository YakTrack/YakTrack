<template>
    <div id="notificaton">

        <alert v-for="(alert, index) in alerts" :key="index" :alert="alert">
        </alert>

    </div>
</template>

<script>
    import Alert from '@/components/Alert.vue';

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

            window.events.on('notify', (notification) => {
                this.alerts.push(notification);
            });  
        },
        methods: {
            showFlashMessages() {
                // Clear existing alerts
                this.alerts = [];
                
                // Show flash messages
                [
                    {
                        type: 'success',
                        message: this.$page.props.flash?.success,
                    },
                    {
                        type: 'error',
                        message: this.$page.props.flash?.error,
                    },
                ].filter(alert => alert.message)
                .forEach(alert => this.alerts.push(alert));

                // Show validation errors
                if (this.$page.props.errors && typeof this.$page.props.errors === 'object') {
                    Object.keys(this.$page.props.errors).forEach(key => {
                        const errorMessages = this.$page.props.errors[key];
                        if (Array.isArray(errorMessages)) {
                            this.alerts.push({
                                type: 'error',
                                message: errorMessages.join("\n"),
                            });
                        } else if (typeof errorMessages === 'string') {
                            this.alerts.push({
                                type: 'error',
                                message: errorMessages,
                            });
                        }
                    });
                }
            }
        },
        watch: {
            '$page.props.flash': {
                handler() {
                    this.showFlashMessages();
                },
            },
            '$page.props.errors': {
                handler() {
                    this.showFlashMessages();
                },
            }
        }
    }
</script>