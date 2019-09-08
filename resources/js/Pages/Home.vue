<template>
    <layout>
        <template slot="title"> Home </template>
        <div class="bg-white rounded shadow p-4">
            <h2 class="pb-4"> Time Tracking Summary </h2>
            <h5 class="pt-4 pb-2 text-grey-darker"> THIS WEEK </h5>
            <div v-for="dayOfWeek in thisWeeksWorkSessions" class="flex pt-2 pb-2" :class="dayOfWeek.date.isToday ? '-ml-4 pl-4 -mr-4 pr-4 bg-blue-lightest' : ''">
                <div class="flex-1 text-grey-dark"> {{ dayOfWeek.dateNoYearForHumans }} </div>
                <div class="flex-1 text-center" :class="dayOfWeek.totalTimeWorked != '0:00:00' ? 'text-grey-darkest' : ''">
                    <timer v-if="dayOfWeek.totalTimeWorked != '0:00:00' && currentlyWorking && dayOfWeek.date.isToday" :initial-time="dayOfWeek.totalSecondsWorked"></timer>
                    <span v-else-if="!(currentlyWorking && dayOfWeek.date.isToday)">{{ dayOfWeek.totalTimeWorked }} </span>
                    <span v-else class="text-grey-light">-</span>
                </div>
            </div>
            <div class="flex mt-4">
                <div class="flex-1 text-xl"> Total </div>
                <div class="flex-1 text-center">
                    <timer v-if="currentlyWorking" :initial-time="totalSecondsThisWeek"></timer>
                    <strong v-else> {{ thisWeeksTotal }} </strong>
                </div>
            </div>
        </div>

        <div class="grid pt-4">
            <div v-for="client in clients" class="rounded shadow bg-white">
                <div class="p-4">
                    <h3> {{ client.name }} </h3>
                    <h4 class="mt-4"> This Week </h4>
                    <div class="flex mt-3">
                        <div class="flex-1"> Total Time </div>
                        <div class="flex-1 text-center">
                            <timer v-if="currentlyWorking && currentClientName == client.name" :initial-time="client.sessionsThisWeek | totalDuration"></timer>
                            <span v-else>{{ client.sessionsThisWeek | totalDuration | durationForHumans }}</span>
                        </div>
                    </div>
                    <div v-for="sprint in client.openSprints">
                        <h4 class="mt-4"> Sprint: {{ sprint.name }} </h4>
                        <div class="flex mt-3">
                            <div class="flex-1"> Total Time </div>
                            <div class="flex-1 text-center">
                                <timer
                                    v-if="currentlyWorking && currentSession.sprint_id == sprint.id"
                                    :initial-time="sprint.sessions | totalDuration"
                                ></timer>
                                <span v-else>{{ sprint.sessions | totalDuration | durationForHumans }} </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </layout>
</template>

<script>
    import dayjs from 'dayjs';
    import Layout from '@/Shared/Layout';
    import Timer from '@/components/Timer';

    export default {
        props: [
            'thisWeeksWorkSessions',
            'thisWeeksTotal',
            'clients',
            'currentlyWorking',
            'currentSession',
            'totalSecondsThisWeek',
            'currentClientName',
        ],
        components: {
            layout: Layout,
            timer: Timer,
        },
        data() {
            return {
                dayjs: dayjs,
            }
        },
    }
</script>