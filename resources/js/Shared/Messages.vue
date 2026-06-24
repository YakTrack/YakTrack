<template>
    <Teleport to="body">
        <div
            class="pointer-events-none fixed inset-x-0 top-[4.5rem] z-50 flex flex-col items-end gap-2 px-4 sm:px-6 lg:left-72 lg:px-8"
            aria-live="polite"
            aria-atomic="true"
        >
            <TransitionGroup
                name="toast"
                tag="div"
                class="flex w-full max-w-sm flex-col items-end gap-2"
            >
                <alert
                    v-for="(alert, index) in alerts"
                    :key="alert.key"
                    :alert="alert"
                    @dismiss="dismissAlert(index)"
                />
            </TransitionGroup>
        </div>
    </Teleport>
</template>

<script>
    import Alert from '@/components/Alert.vue';

    let alertKey = 0;

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

            this._onNotify = (notification) => {
                this.pushAlert(notification);
            };

            window.events.on('notify', this._onNotify);
        },
        beforeUnmount() {
            window.events.off('notify', this._onNotify);
        },
        methods: {
            pushAlert(alert) {
                this.alerts.push({
                    ...alert,
                    key: ++alertKey,
                });
            },
            dismissAlert(index) {
                this.alerts.splice(index, 1);
            },
            showFlashMessages() {
                this.alerts = [];

                [
                    {
                        type: 'success',
                        message: this.$page.props.flash?.success,
                    },
                    {
                        type: 'error',
                        message: this.$page.props.flash?.error,
                    },
                ]
                    .filter((alert) => alert.message)
                    .forEach((alert) => this.pushAlert(alert));

                if (this.$page.props.errors && typeof this.$page.props.errors === 'object') {
                    Object.keys(this.$page.props.errors).forEach((key) => {
                        const errorMessages = this.$page.props.errors[key];

                        if (Array.isArray(errorMessages)) {
                            this.pushAlert({
                                type: 'error',
                                message: errorMessages.join("\n"),
                            });
                        } else if (typeof errorMessages === 'string') {
                            this.pushAlert({
                                type: 'error',
                                message: errorMessages,
                            });
                        }
                    });
                }
            },
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
            },
        },
    }
</script>

<style scoped>
.toast-enter-active,
.toast-leave-active {
    transition: all 0.3s ease;
}

.toast-enter-from,
.toast-leave-to {
    opacity: 0;
    transform: translateX(1rem);
}

.toast-move {
    transition: transform 0.3s ease;
}
</style>
