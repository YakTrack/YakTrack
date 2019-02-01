<template>
    <div class="p-4 bg-white rounded shadow" v-if="days.length > 0">
        <div class="card">
        </div>
        <table class="table table-hover bg-white">
            <thead>
                <tr>
                    <th class="pl-2"> <input v-model="selectAll" type="checkbox"/> </th>
                    <th class="text-right"> Start Time </th>
                    <th class="text-right"> End Time </th>
                    <th class="text-right"> Total Time </th>
                    <th class="text-center"> Linked To </th>
                    <th> Sprint </th>
                    <th> Invoice </th>
                    <th class="text-right pr-0">
                        <dropdown :options="actionsDropdown"></dropdown>
                    </th>
                </tr>
            </thead>
            <tbody v-for="(sessionsInDay, dayIndex) in days" :key="dayIndex">
                <tr class="bg-blue-lightest text-grey-dark font-light text-xs uppercase">
                    <td class="px-3 py-1 rounded" colspan="10"> {{ sessionsInDay[0].localStartedAtDateForHumans }} </td>
                </tr>
                <tr v-for="(session, sessionIndex) in sessionsInDay" :key="sessionIndex" :class="session.rowClasses">
                    <td class="pl-2">
                        <input type="checkbox" v-model="session.isSelected" :value="session.id"/>
                    </td>
                    <td class="min-w-1 text-right">
                        {{ session.localStartedAtTimeForHumans }}
                    </td>
                    <td class="min-w-1 text-center">
                        <span v-if="session.isRunning" class="text-grey-light"> --:--:-- </span>
                        <span v-else> {{ session.localEndedAtTimeForHumans }} </span>
                    </td>
                    <td class="min-w-1 text-right pr-4">
                        <timer :started-at="new Date(session.started_at.date + ' UTC').getTime()" v-if="session.isRunning"></timer>
                        <span v-else class="p-2 text-grey-dark font-light"> {{ session.durationForHumans }} </span>
                    </td>
                    <td class="pl-4 max-w-3">
                        <div v-if="session.task" class="inline-flex">
                            <div class="mr-3 flex my-auto">
                                <a v-if="session.isRunning" class="btn" :href="session.stopUrl"><i class="fa fa-stop fa-xs text-red"></i></a>
                                <button v-else class="btn" @click="createSessionForTask(session.task)"><i class="fas fa-play fa-xs text-grey"></i></button>
                            </div>
                            <div>
                                <div>
                                    <span class="text-grey text-sm"> #{{ session.task.id }}</span>
                                    {{ session.task.name }}
                                </div>
                                <div v-if="session.task.project">
                                    <span v-if="session.task.project.client" class="text-xs text-grey flex-1">
                                        {{ session.task.project.client.name }} >
                                    </span>
                                    <span class="text-xs text-grey-darker flex-1">
                                        {{ session.task.project.name }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <a v-if="session.sprint != null" class="no-underline text-xs" :href="session.sprint.showUrl"> {{ session.sprint.name }} </a>
                    </td>
                    <td>
                        <a v-if="session.invoice != null" class="no-underline text-xs" :href="session.invoice.showUrl"> {{ session.invoice.number }} </a>
                    </td>
                    <td class="text-right inline-flex pb-2 @if($key == 0) pt-2 @endif float-right">
                        <form :action="session.destroyUrl" method="post">
                            <csrf-input></csrf-input>
                            <div class="btn-group float-right">
                                <a
                                    :href="session.editUrl"
                                    class="btn btn-default"
                                >
                                    <i class="fa fa-edit"></i>
                                </a>
                                <input type="hidden" name="_method" value="DELETE">
                                <button class="btn delete-item-button">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </div>
                        </form>
                    </td>
                </tr>
            </tbody>
        </table>

        <modal
            open-on="sessions.link-to-invoice"
            close-on="sessions.linked-to-invoice"
            :on-submit="linkSelectedSessionsToInvoice"
        >
            <template slot-scope="modal">
                <h3 class="text-center"> Link Selected Sessions To Invoice </h3>
                <div class="form-group mt-8">
                    {{ modal.payload }}
                    <label for="invoice_id"> Select Invoice </label>
                    <invoice-select
                        :invoices="invoices"
                        :on-change="selectInvoice"
                        v-model="modal.payload"
                    ></invoice-select>
                </div>
            </template>
        </modal>

    </div>
    <div v-else>
        You have not created any sessions yet.
    </div>
</template>

<script>
    import axios from 'axios';
    import Dropdown from './Dropdown';
    import InvoiceSelect from './InvoiceSelect';
    import Modal from './Modal';
    import Timer from './Timer';

    export default {
        props: [
            'days',
            'invoices',
            'third-party-applications',
        ],
        components: {
            dropdown: Dropdown,
            invoiceSelect: InvoiceSelect,
            modal: Modal,
            timer: Timer,
        },
        data() {
            return {
                sessions: [].concat(...this.days).map(function (session) {
                   session.rowClasses = '';
                   return session;  
                }),
                selectAll: false,
                selectedInvoiceId: null,
                actionsDropdown: [
                    {
                        name: 'Link to invoice',
                        event: 'sessions.link-to-invoice',
                        disabled: () => this.selectedSessions.length > 0,
                    },
                ],
            };
        },
        computed: {
            selectedSessions() {
                return this.sessions.filter(function (session) {
                    return session.isSelected;
                });
            },
            selectedSessionIds() {
                return this.selectedSessions.map((session) => session.id);
            },
        },
        methods: {
            toggleAllCheckboxes(event) {
                this.sessions.forEach((session) => {
                    session.isSelected = event.target.isChecked;
                });
            },
            linkSelectedSessionsToInvoice() {
                window.axios.patch(`invoice/${this.selectedInvoiceId}/`, {
                    sessions: this.selectedSessionIds,
                }).then((response) => {
                    this.selectedSessionIds.forEach((sessionId) => {
                        this.sessions[
                            this.sessions.indexOf(this.sessions.find((session) => session.id == sessionId))
                        ].invoice = response.data.invoice;
                    });

                    window.events.$emit('notify', {
                        type: "success",
                        message: "Sessions linked to invoice "+this.invoices.find((invoice) => invoice.id == this.selectedInvoiceId).number,
                    });

                    window.events.$emit('sessions.linked-to-invoice');
                });
            },
            selectInvoice(invoiceId) {
                this.selectedInvoiceId = invoiceId;
            },
            rowClasses(session) {
                var classes = [];
                
                if (session.isRunning) {
                    classes.push('bg-grey-lightest');
                };

                if (session.isSelected) {
                    classes.push('bg-green-lightest');
                }
                
                return classes.join(' ');
            },
            createSessionForTask(task) {
               window.axios.post(`task/${task.id}/session`, {
                    started_at: (new Date),
               }).then((response) => {
                   window.location.reload();
               });
            }
        },
        watch: {
            selectAll(newValue) {
                this.sessions.forEach((session) => {
                    session.isSelected = newValue;
                });
            },
            sessions: {
                deep: true,
                handler(newValue) {
                    newValue.forEach(session => {
                        session.rowClasses = this.rowClasses(session);
                    });
                }
            }
        },
    }
</script>
