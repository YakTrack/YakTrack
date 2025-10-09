<template>
    <div>
        <div class="p-4 bg-white rounded shadow" v-if="sessions.length">
            <table class="table table-hover bg-white">
                <thead>
                    <tr>
                        <th class="pl-0"> <input type="checkbox" class="form-checkbox"/> </th>
                        <th class="text-right pr-0"> Start Time </th>
                        <th class="text-right pr-0"> End Time </th>
                        <th class="text-right pr-0"> Total Time </th>
                        <th v-if="!hideLinkedToColumn"> Linked To </th>
                        <th v-if="!hideInvoiceColumn"> Invoice </th>
                        <th> Session Category </th>
                        <th class="text-right pr-0">
                            <dropdown :options="[
                                {
                                    name: 'Link to invoice',
                                    event: 'sessions.link-to-invoice',
                                }
                            ]"></dropdown>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="session in sessions" :key="session.id"
                    >
                        <td class=""> <input type="checkbox" class="form-checkbox"/></td>
                        <td class="min-w-1 text-right font-mono">
                            {{ session.localStartedAtTimeForHumans }}
                        </td>
                        <td class="min-w-1 text-right font-mono">
                            <a v-if="session.isRunning" class="btn" :href="route('session.stop', session.id)">
                                <i class="fa fa-stop text-red"></i>
                            </a>
                            <span v-else> {{ session.localEndedAtTimeForHumans }} </span>
                        </td>
                        <td class="min-w-1 text-right font-mono">
                            {{ session.durationForHumans }}
                        </td>
                        <td class="max-w-3" v-if="!hideLinkedToColumn">
                            <session-task :session="session"></session-task>
                        </td>
                        <td v-if="!hideInvoiceColumn">
                            <a v-if="session.invoice" class="no-underline text-xs" :href="route('invoice.show', session.invoice.id)"> {{ session.invoice.number }} </a>
                        </td>
                        <td class="px-6 py-3">
                            <div>{{ session.session_category ? session.session_category.name : '' }}</div>
                            <div class="text-sm font-light">{{ session.comment }}</div>
                        </td>
                        <td class="text-right inline-flex pb-2 @if($key == 0) pt-2 @endif float-right">
                            <dropdown 
                                :options="getSessionActions(session)"
                                name="Actions"
                            ></dropdown>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div v-else>
            No sessions found
        </div>
        
        <!-- Delete Confirmation Modal -->
        <modal
            open-on="confirm-delete-session"
            close-on="close-delete-modal"
            primary-button-text="Delete"
            cancel-button-text="Cancel"
            :on-submit="confirmDeleteSession"
        >
            <template #default="{ payload }">
                <div v-if="payload" class="mb-4">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Delete Session</h3>
                    <p class="text-sm text-gray-500 mb-4">
                        Are you sure you want to delete this session? This action cannot be undone.
                    </p>
                    <div class="bg-gray-50 p-3 rounded text-sm">
                        <div v-if="payload.task_name" class="mb-2">
                            <strong>Task:</strong> {{ payload.task_name }}
                        </div>
                        <div class="mb-2">
                            <strong>Duration:</strong> {{ payload.durationForHumans }}
                        </div>
                        <div v-if="payload.comment" class="mb-2">
                            <strong>Comment:</strong> {{ payload.comment }}
                        </div>
                    </div>
                </div>
            </template>
        </modal>
    </div>
</template>

<script>
    import deleteButton from '@/Shared/DeleteButton.vue';
    import dropdown from '@/Shared/Dropdown.vue';
    import sessionTask from '@/Shared/SessionTask.vue';
    import modal from '@/components/LegacyModal.vue';

    export default {
        components: {
            deleteButton: deleteButton,
            dropdown: dropdown,
            sessionTask: sessionTask,
            modal: modal,
        },
        props: [
            'sessions',
            'hideInvoiceColumn',
            'hideLinkedToColumn',
        ],
        data() {
            return {
                sessionToDelete: null,
            }
        },
        mounted() {
            // Session action events
            events.on('edit-session', (session) => this.$inertia.visit(route('session.edit', session.id)))
            events.on('confirm-delete-session', (session) => {
                this.sessionToDelete = session;
            })
        },
        methods: {
            getSessionActions(session) {
                return [
                    {
                        name: 'Edit Session',
                        event: {
                            name: 'edit-session',
                            args: session
                        }
                    },
                    {
                        name: 'Delete Session',
                        event: {
                            name: 'confirm-delete-session',
                            args: session
                        }
                    }
                ];
            },
            confirmDeleteSession(session) {
                if (session) {
                    this.$inertia.delete(route('session.destroy', session.id));
                }
                events.emit('close-delete-modal');
            }
        }
    }
</script>