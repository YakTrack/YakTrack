<template>
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
                    v-for="session in sessions"
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
                        <div class="btn-group float-right">
                            <inertia-link
                                :href="route('session.edit', session.id)"
                                class="btn btn-default"
                                    >
                                <i class="fa fa-edit"></i>
                            </inertia-link>
                            <delete-button
                                :url="route('session.destroy', session.id)"
                            >
                                <i class="fa fa-trash"></i>
                            </delete-button>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <div v-else>
        No sessions found
    </div>
</template>

<script>
    import deleteButton from '@/Shared/DeleteButton';
    import dropdown from '@/Shared/Dropdown';
    import sessionTask from '@/Shared/SessionTask';

    export default {
        components: {
            deleteButton: deleteButton,
            dropdown: dropdown,
            sessionTask: sessionTask,
        },
        props: [
            'sessions',
            'hideInvoiceColumn',
            'hideLinkedToColumn',
        ],
    }
</script>