<template>
    <div class="bg-white rounded shadow">
        <div class="text-center bg-gray-300" colspan="4">
            <h3 class="text-indigo-90 uppercase text-xl font-medium"> This Week </h3>
        </div>
        <div class="px-1 py-2 text-lg flex">
            <div class="mr-4">
                <a href=""
                    @click.prevent="billableStatus = 'billable'"
                    class="border-solid border-blue-700 leading-tight"
                    :class="billableStatus === 'billable' ? 'border-b text-indigo-800' : 'text-gray-500'">
                    Billable
                </a>
            </div>
            <div class="mr-4">
                <a href=""
                    @click.prevent="billableStatus = 'not_billable'"
                    class="border-solid border-blue-700 leading-tight"
                    :class="billableStatus === 'not_billable' ? 'border-b text-indigo-900' : 'text-gray-500'">
                    Not Billable
                </a>
            </div>
        </div>

        <div class="">
            <table class="w-full" :key="billableStatus">
                <thead>
                    <tr class="bg-indigo-100">
                        <td class="pl-1 sm:pr-2" colspan="4">
                            <div class="uppercase text-indigo-700 text-sm"> All Clients </div>
                        </td>
                    </tr>
                    <tr>
                        <th class="px-1 text-gray-500 font-light text-sm text-left"></th>
                        <th class="pl-10 pr-3 sm:px-1 text-gray-500 font-light text-sm text-center sm:text-right">
                            <span class="fa fa-bullseye mr-1"></span>
                            <span class="hidden sm:block"> Target </span>
                        </th>
                        <th class="pl-10 pr-3 sm:px-1 text-gray-500 font-light text-sm text-center sm:text-right">
                            <pre>+/-</pre>
                            <span class="hidden sm:block"> Remaining </span>
                        </th>
                        <th class="pl-10 pr-3 sm:px-1 text-gray-500 font-light text-sm text-center sm:text-right">
                            <span class="fa fa-stopwatch mr-1"></span>
                            <span class="hidden sm:block"> Total </span>
                        </th>
                    </tr>

                </thead>
                <tbody>
                    <tr
                        v-for="(dayOfWeek, index) in thisWeek.days"
                        class="h-12"
                        :class="dayOfWeek.is_today ? (dayOfWeek.currentlyWorking ? 'bg-green-100' : 'bg-blue-100') : ''"
                        :key="index"
                    >
                        <!-- DATE COLUMN -->
                        <td class="pl-1 sm:pr-2">
                            <div class="uppercase text-gray-700 text-sm"> {{ dayjs(dayOfWeek.date).format('ddd') }} </div>
                            <div class="font-thin text-gray-600 text-xs"> {{ dayjs(dayOfWeek.date).format('Do MMM') }} </div>
                        </td>

                        <!-- TARGET COLUMN -->
                        <td class="text-sm sm:text-base">
                            <div class="text-right font-mono">
                                <duration v-if="dayOfWeek[billableStatus].target" :duration-in-seconds="dayOfWeek[billableStatus].target" color="light-gray"></duration>
                                <span v-else class="text-gray-300 "> --:--:-- </span>
                            </div>
                        </td>

                        <!-- TIME REMAINING COLUMN -->
                        <td class="text-sm sm:text-base">
                            <div class="text-right font-mono" :class="dayOfWeek.totalTimeRemainingForTarget != null ? 'text-gray-500' : ''">
                                <duration
                                    v-if="dayOfWeek[billableStatus].target && !dayOfWeek[billableStatus].is_active"
                                    :duration-in-seconds="dayOfWeek[billableStatus].target - dayOfWeek[billableStatus].actual"
                                    :color="dayOfWeek[billableStatus].actual > dayOfWeek[billableStatus].target ? 'green' : 'light-gray'"
                                ></duration>
                                <timer
                                    v-else-if="dayOfWeek[billableStatus].target"
                                    :initial-time="dayOfWeek[billableStatus].target - dayOfWeek[billableStatus].actual"
                                    :color="dayOfWeek[billableStatus].actual - dayOfWeek[billableStatus].target >= 0 ? 'green' : 'blue'"
                                    :count-down="true"
                                />
                                <span v-else class="text-gray-300">--:--:--</span>
                            </div>
                        </td>

                        <!-- TIME WORKED COLUMN -->
                        <td class="text-sm sm:text-base pr-1 sm:pr-2">
                            <div class="flex-1 text-right font-mono" :class="dayOfWeek[billableStatus].actual > 0 ? 'text-gray-900' : ''">
                                <timer v-if="dayOfWeek[billableStatus].is_active" :initial-time="dayOfWeek[billableStatus].actual"/>
                                <duration v-else-if="dayOfWeek.is_today || dayOfWeek[billableStatus].actual > 0" :duration-in-seconds="dayOfWeek[billableStatus].actual"/>
                                <span v-else class="text-gray-300">--:--:--</span>
                            </div>
                        </td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr>
                        <td class="h-4" colspan="4">
                        </td>
                    </tr>
                    <tr>
                        <th class="text-left uppercase text-gray-700 text-sm sm:text-base font-bold p-0 pl-1 bg-indigo-100">
                            Total
                        </th>

                        <!-- Total Weekly Target -->
                        <th class="text-right text-sm sm:text-base p-0 bg-indigo-100">
                            <duration :duration-in-seconds="thisWeek[billableStatus].target"/>
                        </th>

                        <!-- Total Remaining For Week -->
                        <th class="text-right text-sm sm:text-base p-0 bg-indigo-100">
                            <timer v-if="currentlyWorking" :initial-time="thisWeek[billableStatus].target - thisWeek[billableStatus].actual" :count-down="true"></timer>
                            <duration v-else :duration-in-seconds="thisWeek[billableStatus].target - thisWeek[billableStatus].actual"/>
                        </th>

                        <!-- Total Worked For Week -->
                        <th class="text-right text-sm sm:text-base p-0 pr-1 sm:pr-2 bg-indigo-100">
                            <timer v-if="currentlyWorking" :initial-time="thisWeek[billableStatus].actual" color="indigo"></timer>
                            <strong v-else class="font-mono"> {{ thisWeek[billableStatus].actual | durationForHumans }} </strong>
                        </th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</template>

<script>

import timer from '@/components/Timer';
import duration from '@/components/Duration';
import dayjs from 'dayjs';

export default {
    props: [
        'thisWeek',
        'totalSecondsRemainingForTargetsThisWeek',
        'thisWeeksTotal',
        'currentlyWorking',
    ],
    components: {
        timer: timer,
        duration: duration,
    },
    data() {
        return {
            dayjs: dayjs,
            billableStatus: 'billable',
        }
    },
}

</script>