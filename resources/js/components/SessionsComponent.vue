<template>
    <div>
        <div class="p-1">
            <table>
                <thead>
                <tr>
                    <th>
                        Filter
                    </th>
                    <th>
                        <date-picker v-model="selectedRange" range type="datetime" lang="en" :shortcuts="shortcuts"
                                     format="YYYY-MM-DD HH:mm:ss" @change="fetchData"></date-picker>
                    </th>
                </tr>
                </thead>
            </table>
        </div>
        <div class="p-4 bg-white rounded shadow">
            <table class="table table-hover bg-white" id="sessions-table">
                <thead>
                <tr>
                    <th class="pl-0"><input type="checkbox"/></th>
                    <th> Start Time</th>
                    <th> End Time</th>
                    <th> Total Time</th>
                    <th> Linked To</th>
                    <th v-for="item in applications">
                        {{ item.name }}
                    </th>
                    <th> Invoice</th>
                    <th class="text-right pr-0">
                        <div class="relative float-right text-right">
                            <button class="btn float-right">
                                <span>Actions</span>
                                <i class="fas fa-caret-down"></i>
                                <div class="rounded shadow-md mt-2 absolute mt-12 pin-t pin-l min-w-full bg-white dropdown">
                                    <ul class="list-reset">
                                        <li><a href="#"
                                               class="px-4 py-2 block text-black hover:bg-grey-light no-underline"> Link
                                            to invoice </a></li>
                                    </ul>
                                </div>
                            </button>

                        </div>
                    </th>
                </tr>
                </thead>
                <tbody v-for="item in days">
                <tr class="bg-blue-lightest text-grey-dark font-light text-xs uppercase">
                    <td class="px-3 py-1 rounded" colspan="10"> {{ sectionDate(item[0].started_at) }}</td>
                </tr>
                <tr v-for="session in item">
                    <td><input type="checkbox"/></td>
                    <td class="min-w-1">
                        {{ toDateTime(session.started_at) }}
                    </td>
                    <td class="min-w-1">
                        <a v-if="!session.ended_at" class="btn" @click="stop(session)"><i
                                class="fa fa-stop text-red"></i></a>
                        <span v-else>{{ toDateTime(session.ended_at) }}</span>
                    </td>
                    <td class="min-w-1">
                        <span v-if="session.ended_at">{{ durationTime(session.started_at, session.ended_at) }}</span>
                        <span v-else>{{ timer(time, session.started_at) }}</span>
                    </td>
                    <td class="max-w-3">
                        <session-task :session="session"></session-task>
                    </td>
                    <td v-for="app in applications">
                        <third-party-app :session="session" :app="app"></third-party-app>
                    </td>
                    <td>
                        <a v-if="session.invoice !== null" class="no-underline text-xs"
                           :href="invoiceRedirect(session.invoice)"> {{ session.invoice.number }} </a>
                    </td>
                    <td class="text-right inline-flex pb-2 pt-2 float-right">
                        <div class="btn-group pull-right">
                            <a @click="edit(session)" class="btn btn-default">
                                <i class="fa fa-edit"></i>
                            </a>
                            <a @click="destroy(session.id)" class="btn btn-default">
                                <i class="fa fa-trash"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script>
    import DatePicker from 'vue2-datepicker';
    import axios from 'axios';
    import moment from 'moment';
    import SessionTask from "./SessionTask";
    import ThirdPartyApp from "./ThirdPartyApp";

    export default {
        components: {
            ThirdPartyApp,
            SessionTask,
            DatePicker,
        },
        props: [
            'applications',
        ],
        data() {
            return {
                selectedRange: '',
                time: '',
                days: [],
                shortcuts: [
                    {
                        text: 'Today',
                        onClick: () => {
                            this.selectedRange = [new Date(), new Date()];
                            this.fetchData();
                        }
                    },
                    {
                        text: 'This week',
                        onClick: () => {
                            let curr = new Date;
                            let first = new Date;
                            first.setDate(curr.getDate() - curr.getDay());
                            let last = new Date;
                            last.setDate(curr.getDate() - curr.getDay() + 6);
                            this.selectedRange = [first, last];
                            this.fetchData();
                        }
                    },
                    {
                        text: 'Last week',
                        onClick: () => {
                            let curr = new Date;
                            let first = new Date;
                            first.setDate(curr.getDate() - curr.getDay() - 7);
                            let last = new Date;
                            last.setDate(curr.getDate() - curr.getDay() - 1);
                            this.selectedRange = [first, last];
                            this.fetchData();
                        }
                    },
                    {
                        text: 'This month',
                        onClick: () => {
                            let curr = new Date(), y = curr.getFullYear(), m = curr.getMonth();
                            this.selectedRange = [new Date(y, m, 1), new Date(y, m + 1, 0)];
                            this.fetchData();
                        }
                    },
                    {
                        text: 'Last month',
                        onClick: () => {
                            let date = new Date();
                            date.setDate(1);
                            date.setHours(-1);
                            let curr = new Date;
                            this.selectedRange = [new Date(curr.getFullYear(), curr.getMonth() - 1, 1), date];
                            this.fetchData();
                        }
                    }
                ],
            }
        },
        created() {
            this.updateTime();
            setInterval(this.updateTime, 1000);
            this.fetchData();
        },
        methods: {
            timer(time, startDate) {
                return moment.utc(time.getTime() - new Date(startDate.date).getTime()).format('HH:mm:ss')
            },
            updateTime() {
                this.time = new Date();
            },
            stop() {
                axios.get('session/stop')
                    .then(response => {
                        this.fetchData();
                    }).catch(error => {
                    console.log(error);
                });
            },
            edit(session) {
                axios.get('session/' + session.id + '/edit').then(response => {
                    window.location.href = 'session/' + session.id + '/edit';
                }).catch(error => {
                    console.log(error)
                });
            },
            destroy(id) {
                axios.delete('session/' + id + '/destroy').then(response => {
                    this.fetchData();
                }).catch(error => {
                    console.log(error);
                });
            },
            invoiceRedirect(invoice) {
                axios.get('invoice/' + invoice.id).then(response => {
                    window.location.href = 'invoice/' + invoice.id;
                }).catch(error => {
                    console.log(error);
                });
            },
            sectionDate(time) {
                return moment(time.date).format('dddd Do MMMM YYYY');
            },
            toDateTime(time) {
                return moment(time.date).format('LTS');
            },
            durationTime(start, end) {
                let endDate = new Date(end.date);
                let startDate = new Date(start.date);
                return moment.utc(endDate.getTime() - startDate.getTime()).format('HH:mm:ss');
            },
            fetchData() {
                let parameters = {};
                if (this.selectedRange[0] !== null || this.selectedRange[1] !== null) {
                    parameters = {
                        start: this.selectedRange[0],
                        end: this.selectedRange[1]
                    }
                }
                axios.post('session/filter', parameters).then(response => {
                    this.days = response.data
                }).catch(error => {
                    console.log(error);
                })
            }
        }
    }
</script>

<style scoped>

</style>