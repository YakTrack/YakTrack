<template>
    <layout>
        <template slot="breadcrumbs">
            <breadcrumbs
                :breadcrumbs="[
                    {title: 'Home'},
                ]"
            ></breadcrumbs>
        </template>
        <template slot="title"> Home </template>
        <div class="bg-white rounded shadow p-4">
            <h2 class="pb-4 text-lg"> Time Tracking Summary </h2>
            <h5 class="pt-4 pb-2 text-gray-700"> THIS WEEK </h5>
            <div
                v-for="(dayOfWeek, index) in thisWeeksWorkSessions"
                class="flex pt-2 pb-2"
                :class="dayOfWeek.isToday ? '-ml-4 pl-4 -mr-4 pr-4 bg-blue-100' : ''"
                :key="index"
             >
                <div class="flex-1 text-gray-600"> {{ dayOfWeek.dateNoYearForHumans }} </div>
                <div class="flex-1 text-right font-mono" :class="dayOfWeek.totalTimeWorked != '0:00:00' ? 'text-gray-900' : ''">
                    <timer v-if="dayOfWeek.currentlyWorking" :initial-time="dayOfWeek.totalSecondsWorked"></timer>
                    <span v-else-if="dayOfWeek.isToday || dayOfWeek.totalSecondsWorked > 0" class="pr-2">{{ dayOfWeek.totalTimeWorked }} </span>
                    <span v-else class="text-gray-300 pr-2">--:--</span>
                </div>
            </div>
            <div class="flex mt-4">
                <div class="flex-1 text-xl"> Total </div>
                <div class="flex-1 text-right">
                    <timer v-if="currentlyWorking" :initial-time="totalSecondsThisWeek"></timer>
                    <strong v-else class="font-mono"> {{ thisWeeksTotal }} </strong>
                </div>
            </div>
        </div>

        <div class="grid pt-4">
            <div v-for="client in clients" :key="client.id" class="rounded shadow bg-white">
                <div class="p-4">
                    <h3 class="text-xl"> {{ client.name }} </h3>
                    <h4 class="mt-4 uppercase text-gray-800"> This Week </h4>
                    <div class="flex mt-3 bg-gray-100 p-2 rounded">
                        <div class="flex-1 text-gray-700 font-light"> Total Time </div>
                        <div class="flex-1 text-right">
                            <timer v-if="currentlyWorking && currentClientName == client.name" :initial-time="client.sessionsThisWeek | totalDuration"></timer>
                            <span v-else class="pr-2 font-mono">{{ client.sessionsThisWeek | totalDuration | durationForHumans }}</span>
                        </div>
                    </div>
                    <div v-for="sprint in client.openSprints" :key="sprint.id">
                        <h4 class="mt-4 uppercase"> {{ sprint.name }} </h4>
                        <div class="flex mt-3 bg-gray-100 p-2 rounded">
                            <div class="flex-1 text-gray-700 font-light"> Total Time </div>
                            <div class="flex-1 text-right">
                                <timer
                                    v-if="currentlyWorking && currentSession.sprint_id == sprint.id"
                                    :initial-time="sprint.sessions | totalDuration"
                                ></timer>
                                <span v-else class="pr-2 font-mono">{{ sprint.sessions | totalDuration | durationForHumans }} </span>
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
    import breadcrumbs from '@/Shared/Breadcrumbs';
    import layout from '@/Shared/Layout';
    import timer from '@/components/Timer';

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
            breadcrumbs: breadcrumbs,
            layout: layout,
            timer: timer,
        },
        data() {
            return {
                dayjs: dayjs,
            }
        },
    }
</script>