<template>
    <div class="p-4 bg-white rounded shadow" v-if="sessions.length">
        <table class="table table-hover bg-white">
            <thead>
                <tr>
                    <th class="pl-0"> <input type="checkbox"/> </th>
                    <th> Start Time </th>
                    <th> End Time </th>
                    <th> Total Time </th>
                    <th> Linked To </th>
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
                    <td class=""> <input type="checkbox"/></td>
                    <td class="min-w-1">
                        {{ session.localStartedAtTimeForHumans }}
                    </td>
                    <td class="min-w-1">
                        <a v-if="session.isRunning" class="btn" :href="route('session.stop', session.id)">
                            <i class="fa fa-stop text-red"></i>
                        </a>
                        <span v-else> {{ session.localEndedAtTimeForHumans }} </span>
                    </td>
                    <td class="min-w-1">
                        {{ session.durationForHumans }}
                    </td>
                    <td class="max-w-3">
                        <session-task :session="session"></session-task>
                    </td>
                    <td v-if="!hideInvoiceColumn">
                        <a class="no-underline text-xs" :href="route('invoice.show', session.invoice.id)"> {{ session.invoice.number }} </a>
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
        ],
    }
</script>