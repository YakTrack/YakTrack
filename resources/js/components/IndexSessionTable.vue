<template>
    <div>
        <div class="p-4 bg-white rounded shadow" v-if="queryParams.showFilters == 'true'">
            <h3> Filters </h3>
                <div class="flex-1">
                    <div class="flex">
                        <div class="btn-group">
                            <button class="btn" @click="thisMonth()"> This Month </button>
                            <button class="btn" @click="lastMonth()"> Last Month </button>
                            <button class="btn" @click="lastWeek()"> Last Week </button>
                            <button class="btn" @click="thisWeek()"> This Week </button>
                        </div>
                    </div>
                    <div class="flex mt-2">
                        <div class="flex-1">
                            <label> Started After </label>
                            <datetime
                                v-model="filters.startedAfter"
                                class="border border-grey p-2"
                                :format="dateFormat"
                                type="datetime"
                            ></datetime>
                        </div>
                        <div class="flex-1 ml-2">
                            <label> Started Before </label>
                            <datetime
                                v-model="filters.startedBefore"
                                class="border border-grey p-2"
                                :format="dateFormat"
                                type="datetime"
                            ></datetime>
                        </div>
                    </div>

            </div>
        </div>

        <div class="p-4 bg-white rounded shadow mt-4" v-if="true"> <!--days.length > 0">-->
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
                <tbody v-for="(day, dayIndex) in filteredDays" :key="dayIndex">
                    <tr class="bg-blue-lightest text-grey-dark font-light text-xs uppercase">
                        <td class="px-3 py-1 rounded" colspan="10"> {{ day.date }} </td>
                    </tr>
                    <tr v-for="(session, sessionIndex) in day.sessions" :key="session.id" :class="session.rowClasses">
                        <td class="pl-2">
                            <input type="checkbox" v-model="session.isSelected" :value="session.id"/>
                        </td>
                        <td class="min-w-1 text-right">
                            {{ session.localStartedAtTimeForHumans }}
                        </td>
                        <td class="min-w-1 text-right">
                            <span v-if="session.isRunning" class="text-grey-light"> --:--:-- </span>
                            <span v-else> {{ session.localEndedAtTimeForHumans }} </span>
                        </td>
                        <td class="min-w-1 text-right pr-4">
                            <timer :started-at="new Date(session.started_at.date + ' UTC').getTime()" v-if="session.isRunning"></timer>
                            <span v-else class="p-2 text-grey-dark font-light"> {{ session.durationForHumans }} </span>
                        </td>
                        <td class="pl-4 max-w-3">
                            <div class="inline-flex">
                                <div class="mr-3 flex my-auto">
                                    <inertia-link v-if="session.isRunning" class="btn" :href="session.stopUrl"><i class="fa fa-stop fa-xs text-red"></i></inertia-link>
                                    <button v-if="session.task && !session.isRunning" class="btn" @click="createSessionForTask(session.task)">
                                        <i class="fas fa-play fa-xs text-grey"></i>
                                    </button>
                                </div>
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
                            </div>
                        </td>
                        <td>
                            <inertia-link v-if="session.sprint != null" class="no-underline text-xs" :href="session.sprint.showUrl || ''"> {{ session.sprint.name }} </inertia-link>
                        </td>
                        <td>
                            <inertia-link v-if="session.invoice != null" class="no-underline text-xs" :href="session.invoice.showUrl"> {{ session.invoice.number }} </inertia-link>
                        </td>
                        <td class="text-right inline-flex pb-2 @if($key == 0) pt-2 @endif float-right">
                            <div class="btn-group float-right">
                                <inertia-link
                                    :href="session.editUrl"
                                    class="btn btn-default"
                                >
                                    <i class="fa fa-edit"></i>
                                </inertia-link>
                                <button
                                    type="button"
                                    class="btn delete-item-button"
                                    @click="deleteSession(session)"
                                >
                                    <i class="fa fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>

            <div class="flex justify-center mt-10">
                <div class="flex">
                    <dropdown
                        :options="perPageOptions"
                        :selected-option="selectedPerPageOption"
                        class=""
                        name="Per Page"
                    ></dropdown>
                    <page-selector
                        :total="total"
                        :last-page="lastPage"
                        :page="page"
                        :per-page="perPage"
                        :on-page-select="selectPage"
                    >
                    </page-selector>
                </div>
            </div>

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
    </div>
</template>

<script>
    import axios from 'axios';
    import Dropdown from './Dropdown';
    import InvoiceSelect from './InvoiceSelect';
    import Modal from './Modal';
    import Timer from './Timer';
    import DateTime from './../filters/DateTime';
    import DatetimeInput from './DatetimeInput';
    import PageSelector from './PageSelector';
    import { mapState } from 'vuex'
    import closeable from '@/directives/Closeable';
    import UrlParser from '@/UrlParser.js';

    const DATE_FORMAT = "yyyy'-'MM'-'dd HH':'mm':'ss";

    export default {
        props: [
            'invoices',
            'third-party-applications',
            'showFilters',
            'days',
            'page',
            'perPage',
            'total',
            'lastPage',
        ],
        components: {
            dropdown: Dropdown,
            invoiceSelect: InvoiceSelect,
            modal: Modal,
            timer: Timer,
            datetimeInput: DatetimeInput,
            pageSelector: PageSelector,
        },
        data() {
            return {
                selectAll: false,
                selectedInvoiceId: null,
                actionsDropdown: [
                    {
                        name: 'Link to invoice',
                        event: 'sessions.link-to-invoice',
                        disabled: () => this.selectedSessions.length > 0,
                    },
                ],
                dateFormat: DATE_FORMAT,
                filters: {
                    startedAfter: null,
                    startedBefore: null,
                },
                dateTime: DateTime,
                urlParser: UrlParser,
            };
        },
        computed: {
            queryParams() {
                return (new URLSearchParams(window.location.search));
            },
            selectedSessions() {
                return this.sessions.filter(function (session) {
                    return session.isSelected;
                });
            },
            selectedSessionIds() {
                return this.selectedSessions.map((session) => session.id);
            },
            sessions() {
                return this.days.reduce((acculumatedSessions, day) => [].concat(acculumatedSessions, day.sessions), []);
            },
            filteredDays() {
                return this.days.filter(day => day.sessions.length > 0);
            },
            perPageOptions() {
                return [
                    {
                        name: '50 per page',
                        event: {
                            name: 'set-per-page',
                            args: 50,
                        },
                    },
                    {
                        name: '100 per page',
                        event: {
                            name: 'set-per-page',
                            args: 100,
                        },
                    },
                    {
                        name: '200 per page',
                        event: {
                            name: 'set-per-page',
                            args: 200,
                        },
                    },
                    {
                        name: '500 per page',
                        event: {
                            name: 'set-per-page',
                            args: 500,
                        },
                    },
                    {
                        name: '1000 per page',
                        event: {
                            name: 'set-per-page',
                            args: 1000,
                        },
                    },
                ];
            },
            selectedPerPageOption() {
                return this.perPageOptions.find(option =>{
                    return Number.parseInt(option.name) == this.queryParams.get('per-page');
                });
            }
        },
        mounted() {
            this.setDateTimeFilters(
                this.dateTime.fromDateTimeString(this.queryParams.startedAfter),
                this.dateTime.fromDateTimeString(this.queryParams.startedBefore)
            );


            events.$on('set-per-page', (perPage) => {
                this.$inertia.visit(this.urlParser.current({
                    perPage: perPage,
                }), {
                    replace: true,
                    preserveScroll: true,
                });
            });
        },
        methods: {
            deleteSession(session) {
                this.$inertia.delete(session.destroyUrl);
            },
            getSessions() {
                axios.get(`json/session`, this.queryParams)
                    .then(response => {
                    this.$set(this, 'days', response.data.days.map(day => {
                        day.sessions = day.sessions.map(session => {
                            session.isSelected = false;
                            return session;
                        });
                        return day;
                    }));
                    this.$set(this, 'total', response.data.total);
                    this.$set(this, 'lastPage', response.data.lastPage);
                });
            },
            toggleAllCheckboxes(event) {
                this.sessions.forEach((session) => {
                    session.isSelected = event.target.isChecked;
                });
            },
            linkSelectedSessionsToInvoice() {
                this.$inertia.patch(route('invoice.update', this.selectedInvoiceId), {
                    sessions: this.selectedSessionIds,
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
                this.$inertia.post(`task/${task.id}/session`, {
                    started_at: (new Date),
                });
            },
            thisWeek() {
                this.setDateTimeFilters(
                    this.dateTime.startOfWeek(new Date()),
                    this.dateTime.endOfWeek(new Date())
                );
            },
            lastWeek() {
                this.setDateTimeFilters(
                    this.dateTime.startOfLastWeek(new Date()),
                    this.dateTime.endOfLastWeek(new Date())
                );
            },
            thisMonth() {
                this.setDateTimeFilters(
                    this.dateTime.startOfMonth(new Date()),
                    this.dateTime.endOfMonth(new Date())
                );
            },
            lastMonth() {
                this.setDateTimeFilters(
                    this.dateTime.startOfLastMonth(new Date()),
                    this.dateTime.endOfLastMonth(new Date())
                );
            },
            setDateTimeFilters(startedAfter, startedBefore) {
                this.setStartedAfterFilter(startedAfter);
                this.setStartedBeforeFilter(startedBefore);
            },
            setStartedAfterFilter(startedAfter) {
                this.filters.startedAfter = startedAfter;
            },
            setStartedBeforeFilter(startedBefore) {
                this.filters.startedBefore = startedBefore;
            },
            selectPage(page) {
                this.$inertia.visit(this.urlParser.current({page: page}), {
                    replace: true,
                    preserveScroll: true,
                });
            },
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
            },
            filters: {
                deep: true,
                handler(newValue) {
                    ['startedAfter', 'startedBefore'].forEach(key => {
                    });
                }
            },
        },
    }
</script>
