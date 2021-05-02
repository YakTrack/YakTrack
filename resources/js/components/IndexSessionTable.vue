<template>
    <div>
        <div class="p-1 sm:p-4 bg-white rounded shadow" v-if="searchParams.get('show-filters') == 'true'">
            <h3> Filters </h3>
            <div class="flex-1 mt-2">
                <div class="flex">
                    <div class="btn-group">
                        <button class="btn" :class="filterPresetIsSelected('thisMonth') ? 'btn-gray-300' : ''" @click="loadFilterPreset('thisMonth')"> This Month </button>
                        <button class="btn" :class="filterPresetIsSelected('lastMonth') ? 'btn-gray-300' : ''" @click="loadFilterPreset('lastMonth')"> Last Month </button>
                        <button class="btn" :class="filterPresetIsSelected('thisWeek') ? 'btn-gray-300' : ''" @click="loadFilterPreset('thisWeek')"> This Week </button>
                        <button class="btn" :class="filterPresetIsSelected('lastWeek') ? 'btn-gray-300' : ''" @click="loadFilterPreset('lastWeek')"> Last Week </button>
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

        <div class="pt-4 pb-4 bg-white rounded shadow mt-4" v-if="true"> <!--days.length > 0">-->
            <table class="table w-full table-hover bg-white">
                <thead>
                    <tr>
                        <th class="pl-1 sm:pl-4 pb-4">
                            <input v-model="selectAll" type="checkbox" class="form-checkbox" />
                        </th>
                        <th class="">  </th>
                        <th class="">  </th>
                        <th class="text-right font-mono pr-2 font-thin text-base text-gray-500"> {{ totalDuration }} </th>
                        <th class="text-right pr-4 pb-4">
                            <dropdown :options="actionsDropdown"></dropdown>
                        </th>
                    </tr>
                </thead>
                <tbody v-for="(day, dayIndex) in filteredDays" :key="dayIndex">
                    <tr class="bg-gray-200 text-gray-600 font-light text-xs uppercase">
                        <td class="px-3 py-1 border-solid" colspan="4"> {{ day.sessions[0].localStartedAtDateForHumans }} </td>
                        <td class="text-right pr-6 text-base font-thin font-mono text-gray-500"> {{ day.totalDurationForHumans }} </td>
                        <td colspan="6"></td>
                    </tr>
                    <tr v-for="(session, sessionIndex) in day.sessions" :key="session.id" :class="session.rowClasses">
                        <td class="pl-1 sm:pl-4">
                            <input type="checkbox" class="form-checkbox " v-model="session.isSelected" :value="session.id"/>
                        </td>
                        <td class="pl-1 sm:pl-4">
                            <div class="overflow-auto h-full whitespace-no-wrap">
                                <div v-if="session.task_id">
                                    <inertia-link  class="no-underline hover:opacity-50 opacity-100 max-w-xs inline-block truncate" :href="route('task.show', session.task_id)">
                                        <i class="fas fa-check-square text-gray-400 mr-2"></i>
                                        <span class="text-sm sm:text-base font-semibold"> {{ taskPrefix(session.task_name) }} </span>
                                        <span class="text-sm sm:text-base"> {{ taskSuffix(session.task_name) }} </span>
                                    </inertia-link>
                                </div>
                                <div class="flex items-center text-gray-500">
                                    <div class="w-32">
                                        <inertia-link v-if="session.client_id != null" class="no-underline text-xs text-blue-700 opacity-50 hover:opacity-100" :href="route('client.show', session.client_id)">
                                            <i class="fas fa-users text-blue-700 mr-2 opacity-50"></i>
                                            {{ session.client_name }}
                                        </inertia-link>
                                    </div>
                                    <div class="w-32">
                                        <inertia-link v-if="session.project_id != null" class="no-underline text-xs text-indigo-700 opacity-50 hover:opacity-100" :href="route('project.show', session.project_id)">
                                            <i class="fas fa-briefcase text-indigo-700 opacity-50 mr-2"></i>
                                            {{ session.project_name }}
                                        </inertia-link>
                                    </div>
                                    <div class="w-32">
                                        <inertia-link v-if="session.sprint_id != null" class="no-underline text-xs text-purple-700 opacity-50 hover:opacity-100" :href="route('sprint.show', session.sprint_id)">
                                            <i class="fas fa-calendar-times text-purple-700 opacity-50 mr-2"></i>
                                            {{ session.sprint_name }}
                                        </inertia-link>
                                    </div>
                                    <div class="w-32">
                                        <inertia-link v-if="session.invoice_id != null" class="no-underline text-xs text-teal-700 opacity-50 hover:opacity-100" :href="route('invoice.show', session.invoice_id)">
                                            <i class="fas fa-file-invoice-dollar text-teal-700 opacity-50 mr-2"></i>
                                            {{ session.invoice_number }}
                                        </inertia-link>
                                    </div>
                                    <i v-if="session.is_billable" class="fas fa-money-bill-alt text-gray-400 mr-2"></i>
                                </div>
                            </div>
                        </td>
                        <td class="text-right">
                            <timestamp class="mr-2" :time="session.started_at"></timestamp>
                            <span class="text-gray-400 font-mono"> to </span>
                            <timestamp class="" :time="session.ended_at"></timestamp>
                        </td>
                        <td class="text-right">
                            <timer :initial-time="session.durationInSeconds" :is-paused="!session.isRunning"></timer>
                        </td>
                        <td class="text-right inline-flex pr-4 pb-4 float-right" :class="sessionIndex || 'pt-4' ">
                            <div class="btn-group float-right">
                                <button v-if="session.isRunning" class="btn text-gray-600 hover:text-red-600 hover:bg-red-100" @click="stopSession(session)">
                                    <i class="fa fa-stop fa-xs"></i>
                                </button>
                                <button v-if="session.task_id && !session.isRunning" class="btn text-gray-600 hover:text-green-600 hover:bg-green-100" @click="continueSession(session)">
                                    <i class="fas fa-play fa-xs"></i>
                                </button>
                                <inertia-link
                                    :href="session.editUrl"
                                    class="btn btn-default py-2"
                                >
                                    <i class="fa fa-edit fa-xs text-gray-600"></i>
                                </inertia-link>
                                <delete-button
                                    :url="route('session.destroy', session.id)"
                                >
                                    <i class="fa fa-trash fa-xs text-gray-600"></i>
                                </delete-button>
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


        </div>
        <div v-else>
            You have not created any sessions yet.
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
</template>

<script>
    import dropdown from '@/Shared/Dropdown';
    import invoiceSelect from '@/Shared/InvoiceSelect';
    import deleteButton from '@/Shared/DeleteButton';
    import timestamp from '@/Shared/Timestamp';
    import modal from './Modal';
    import timer from './Timer';
    import dateTime from './../filters/DateTime';
    import datetimeInput from './DatetimeInput';
    import pageSelector from './PageSelector';
    import closeable from '@/directives/Closeable';
    import urlParser from '@/UrlParser.js';
    import searchParams from '@/SearchParams';
    import dayjs from 'dayjs';

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
            deleteButton: deleteButton,
            dropdown: dropdown,
            invoiceSelect: invoiceSelect,
            modal: modal,
            timer: timer,
            timestamp: timestamp,
            datetimeInput: datetimeInput,
            pageSelector: pageSelector,
        },
        data() {
            return {
                dayjs: dayjs,
                selectAll: false,
                selectedInvoiceId: null,
                actionsDropdown: [
                    {
                        name: 'Link to invoice',
                        event: 'sessions.link-to-invoice',
                        disabled: () => this.selectedSessions.length > 0,
                    },
                    {
                        name: 'Mark as billable',
                        event: 'sessions.mark-as-billable',
                        disabled: () => this.selectedSessions.length > 0,
                    },
                    {
                        name: 'Mark as non-billable',
                        event: 'sessions.mark-as-non-billable',
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

            events.$on('sessions.mark-as-billable', () => this.updateSelectedSessions({ is_billable: 1 }))
            events.$on('sessions.mark-as-non-billable', () => this.updateSelectedSessions({ is_billable: 0 }))
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
            updateSelectedSessions(payload) {
               this.$inertia.patch(
                   route('sessions.update'),
                   {
                        sessions: this.selectedSessions.reduce((sessions, session) => {
                            sessions[session.id] = payload

                            return sessions
                        }, {})
                   }
                )
            },
            getSessions() {
                this.$inertia.get(`json/session`, searchParams)
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
                    classes.push('bg-green-100');
                };

                if (session.isSelected) {
                    classes.push('bg-blue-100');
                }

                return classes.join(' ');
            },
            continueSession(session) {
                this.$inertia.post(`session/${session.id}/continue`)
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
                this.$inertia.visit(this.urlParser.current({page: page}), {
                    replace: true,
                    preserveScroll: true,
                });
            },
            taskPrefix(name) {
                return name.split(':')[0];
            },
            taskSuffix(name) {
                let suffix = name.split(':')[1];

                return suffix ? ':' + suffix : '';
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
