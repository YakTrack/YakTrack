<template>
    <div>
        <div class="p-4 bg-white rounded shadow" v-if="searchParams.get('show-filters') == 'true'">
            <h3> Filters </h3>
            <div class="flex-1 mt-2">
                <div class="flex">
                    <div class="btn-group">
                        <button class="btn" :class="filterPresetIsSelected('thisMonth') ? 'btn-grey-lighter' : ''" @click="loadFilterPreset('thisMonth')"> This Month </button>
                        <button class="btn" :class="filterPresetIsSelected('lastMonth') ? 'btn-grey-lighter' : ''" @click="loadFilterPreset('lastMonth')"> Last Month </button>
                        <button class="btn" :class="filterPresetIsSelected('thisWeek') ? 'btn-grey-lighter' : ''" @click="loadFilterPreset('thisWeek')"> This Week </button>
                        <button class="btn" :class="filterPresetIsSelected('lastWeek') ? 'btn-grey-lighter' : ''" @click="loadFilterPreset('lastWeek')"> Last Week </button>
                    </div>
                </div>
                <div class="flex mt-2">
                    <div class="flex-1">
                        <label> Started After </label>
                        <datetime-input
                            v-model="filters.startedAfter"
                        />
                    </div>
                    <div class="flex-1 ml-2">
                        <label> Started Before </label>
                        <datetime-input
                            v-model="filters.startedBefore"
                        />
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
                        <th class="text-right pr-4"> Total Time </th>
                        <th class="text-center"> Linked To </th>
                        <th> Sprint </th>
                        <th> Invoice </th>
                        <th class="text-right pr-0">
                            <dropdown :options="actionsDropdown"></dropdown>
                        </th>
                    </tr>
                    <tr>
                        <th class="pl-2"></th>
                        <th class="text-right"></th>
                        <th class="text-right"></th>
                        <th class="text-right font-mono pr-4 text-base text-grey"> {{ totalDuration }} </th>
                        <th class="text-center"></th>
                        <th></th>
                        <th></th>
                    </tr>
                </thead>
                <tbody v-for="(day, dayIndex) in filteredDays" :key="dayIndex">
                    <tr class="bg-blue-lightest text-grey-dark font-light text-xs uppercase">
                        <td class="px-3 py-1 rounded" colspan="10"> {{ day.sessions[0].localStartedAtDateForHumans }} </td>
                        <td class="text-right pr-4 text-base font-mono text-grey"> {{ day.totalDurationForHumans }} </td>
                        <td colspan="6"></td>
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
                            <timer :started-at="new Date(session.started_at).getTime()" v-if="session.isRunning"></timer>
                            <span v-else class="pl-2 text-grey-dark font-light text-base font-mono"> {{ session.durationForHumans }} </span>
                        </td>
                        <td class="pl-4 max-w-3">
                            <div class="inline-flex">
                                <div class="mr-3 flex my-auto">
                                    <button v-if="session.isRunning" class="btn" @click="stopSession(session)">
                                        <i class="fa fa-stop fa-xs text-red"></i>
                                    </button>
                                    <button v-if="session.task_id && !session.isRunning" class="btn" @click="createSessionForTask(session.task_id)">
                                        <i class="fas fa-play fa-xs text-grey"></i>
                                    </button>
                                </div>
                                <div v-if="session.task_id">
                                    <div>
                                        <span class="text-grey text-sm"> #{{ session.task_id }}</span>
                                        {{ session.task_name }}
                                    </div>
                                    <div v-if="session.project_id">
                                        <span v-if="session.client_id" class="text-xs text-grey flex-1">
                                            {{ session.client_name }} >
                                        </span>
                                        <span class="text-xs text-grey-darker flex-1">
                                            {{ session.project_name }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <inertia-link v-if="session.sprint_id != null" class="no-underline text-xs" :href="route('sprint.show', session.sprint_id)"> {{ session.sprint_name }} </inertia-link>
                        </td>
                        <td>
                            <inertia-link v-if="session.invoice_id != null" class="no-underline text-xs" :href="route('invoice.show', session.invoice_id)"> {{ session.invoice_number }} </inertia-link>
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
    import dropdown from '@/Shared/Dropdown';
    import invoiceSelect from '@/Shared/InvoiceSelect';
    import modal from './Modal';
    import timer from './Timer';
    import dateTime from './../filters/DateTime';
    import datetimeInput from './DatetimeInput';
    import pageSelector from './PageSelector';
    import { mapState } from 'vuex'
    import closeable from '@/directives/Closeable';
    import urlParser from '@/UrlParser.js';
    import searchParams from '@/SearchParams';

    const DATE_FORMAT = "yyyy'-'MM'-'dd HH':'mm':'ss";

    export default {
        props: [
            'invoices',
            'thirdPartyApplications',
            'days',
            'page',
            'perPage',
            'total',
            'lastPage',
        ],
        components: {
            dropdown: dropdown,
            invoiceSelect: invoiceSelect,
            modal: modal,
            timer: timer,
            datetimeInput: datetimeInput,
            pageSelector: pageSelector,
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
                dateTime: dateTime,
                urlParser: urlParser,
                searchParams: searchParams,
                filterPresets: {
                    thisWeek: {
                        startedAfter: dateTime.startOfWeek(new Date()),
                        startedBefore: dateTime.endOfWeek(new Date()),
                    },
                    lastWeek: {
                        startedAfter: dateTime.startOfLastWeek(new Date()),
                        startedBefore: dateTime.endOfLastWeek(new Date()),
                    },
                    thisMonth: {
                        startedAfter: dateTime.startOfMonth(new Date()),
                        startedBefore: dateTime.endOfMonth(new Date()),
                    },
                    lastMonth: {
                        startedAfter: dateTime.startOfLastMonth(new Date()),
                        startedBefore: dateTime.endOfLastMonth(new Date()),
                    },
                },
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
            sessions() {
                return this.days.reduce((acculumatedSessions, day) => [].concat(acculumatedSessions, day.sessions), []);
            },
            filteredDays() {
                return this.days.filter(day => day.sessions.length > 0);
            },
            totalDuration() {
                return this.dateTime.durationForHumans(this.sessions.reduce(function (accumulator, session) {
                    return session.durationInSeconds + accumulator
                }, 0));
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
                    return Number.parseInt(option.name) == searchParams.get('per-page');
                });
            }
        },
        mounted() {
            this.setDateTimeFilters(
                this.dateTime.toInputFormat(this.dateTime.fromSearchParam(searchParams.get('started-after'))),
                this.dateTime.toInputFormat(this.dateTime.fromSearchParam(searchParams.get('started-before'))),
                false
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
            loadFilterPreset(presetKey) {
                this.setDateTimeFilters(
                    this.filterPresets[presetKey].startedAfter,
                    this.filterPresets[presetKey].startedBefore
                );
            },
            filterPresetIsSelected(presetKey) {
                const preset = this.filterPresets[presetKey];
                return this.filters.startedAfter == preset.startedAfter && this.filters.startedBefore == preset.startedBefore;
            },
            deleteSession(session) {
                this.$inertia.delete(session.destroyUrl);
            },
            stopSession(session) {
                this.$inertia.post(route('session.stop', session.id));
            },
            getSessions() {
                axios.get(`json/session`, searchParams)
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
            createSessionForTask(taskId) {
                this.$inertia.post(`task/${taskId}/session`, {
                    started_at: (new Date),
                });
            },
            setDateTimeFilters(startedAfter, startedBefore, reload = true) {
                this.setStartedAfterFilter(startedAfter);
                this.setStartedBeforeFilter(startedBefore);

                if (!reload) {
                    return;
                }

                this.$inertia.visit(this.urlParser.current({
                    startedAfter: this.filters.startedAfter,
                    startedBefore: this.filters.startedBefore,
                }), {
                    preserveScroll: true,
                });

            },
            setStartedAfterFilter(startedAfter) {
                this.filters.startedAfter = startedAfter;
            },
            setStartedBeforeFilter(startedBefore) {
                this.filters.startedBefore = startedBefore;
            },
            selectPage(page) {
                this.$inertia.visit(this.urlParser.current({page: this.page}), {
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

                }
            },
        },
    }
</script>
