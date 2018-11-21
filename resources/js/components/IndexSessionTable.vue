<template>
    <div class="p-4 bg-white rounded shadow" v-if="days.length > 0">
        <table class="table table-hover bg-white">
            <thead>
                <tr>
                    <th class="pl-0"> <input v-model="selectAll" type="checkbox"/> </th>
                    <th class="text-right"> Start Time </th>
                    <th class="text-right"> End Time </th>
                    <th class="text-right"> Total Time </th>
                    <th class="text-center"> Linked To </th>
                    <th v-for="app in thirdPartyApplications" :key="app.id"> Linked to {{ app.name }} </th>
                    <th> Invoice </th>
                    <th class="text-right pr-0">
                        <dropdown :options="actionsDropdown"></dropdown>
                    </th>
                </tr>
            </thead>
            <tbody v-for="(sessionsInDay, index) in days" :key="index">
                <tr class="bg-blue-lightest text-grey-dark font-light text-xs uppercase">
                    <td class="px-3 py-1 rounded" colspan="10"> {{ sessionsInDay[0].localStartedAtDateForHumans }} </td>
                </tr>
                <tr v-for="(session, index) in sessionsInDay" :key="index">
                    <td class="">
                        <input type="checkbox" v-model="session.isSelected" :value="session.id"/>
                    </td>
                    <td class="min-w-1 text-right">
                        {{ session.localStartedAtTimeForHumans }}
                    </td>
                    <td class="min-w-1 text-right">
                        {{ session.stopUrl }}
                        <a v-if="session.isRunning" class="btn" :href="session.stopUrl"><i class="fa fa-stop text-red"></i></a>
                        <span v-else> {{ session.localEndedAtTimeForHumans }} </span>
                    </td>
                    <td class="min-w-1 text-right pr-4">
                        {{ session.durationForHumans }}
                    </td>
                    <td class="pl-4 max-w-3">
                        <div v-if="session.task">
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
                    </td>
                    <td v-for="app in thirdPartyApplications" :key="app.id">
                        TODO
                    </td>
                    <td>
                        <a v-if="session.invoice != null" class="no-underline text-xs" :href="session.invoice.showUrl"> {{ session.invoice.number }} </a>
                    </td>
                    <td class="text-right inline-flex pb-2 @if($key == 0) pt-2 @endif float-right">
                        <form :action="session.destroyUrl" method="post">
                            <csrf-input></csrf-input>
                            <div class="btn-group pull-right">
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
    import dropdown from './Dropdown';
    import axios from 'axios';
    import modal from './Modal';
    import invoiceSelect from './InvoiceSelect';

    export default {
        props: [
            'days',
            'third-party-applications',
            'invoices',
        ],
        components: {
            dropdown: dropdown,
            modal: modal,
            invoiceSelect: invoiceSelect,
        },
        data() {
            return {
                sessions: [].concat(...this.days),
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
            }
        },
        watch: {
            selectAll(newValue) {
                this.sessions.forEach((session) => {
                    session.isSelected = newValue;
                });
            }
        }
    }
</script>