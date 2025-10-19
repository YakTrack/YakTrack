<template>
    <layout>
        <template #breadcrumbs>
            <breadcrumbs
                :breadcrumbs="[
                    {title: 'Home',     url: route('home')},
                    {title: 'Test Runs'},
                ]"
            ></breadcrumbs>
        </template>
        <template #title>Test Runs</template>
        <template #top-right-toolbar>
            <button-link :href="route('test-run.create')" color="blue">
                <i class="fa fa-plus text-blue-100 mr-2"></i>
                Create Test Run
            </button-link>
        </template>

        <!-- Filters -->
        <div class="card mb-4">
            <div class="card-body">
                <form @submit.prevent="filterTestRuns" class="flex gap-4 items-end">
                    <div class="flex-1">
                        <label for="project_id" class="block text-sm font-medium text-gray-700 mb-1">
                            Project
                        </label>
                        <select
                            id="project_id"
                            v-model="filters.project_id"
                            class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                        >
                            <option value="">All Projects</option>
                            <option v-for="project in projects" :key="project.id" :value="project.id">
                                {{ project.name }}
                            </option>
                        </select>
                    </div>
                    <button
                        type="submit"
                        class="px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150"
                    >
                        Filter
                    </button>
                    <button-link
                        :href="route('test-run.index')"
                        color="gray"
                    >
                        Clear
                    </button-link>
                </form>
            </div>
        </div>

        <!-- Test Runs List -->
        <div class="card" v-if="testRuns.data.length">
            <table class="table card-body">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Project</th>
                        <th>Executed</th>
                        <th>Pass Rate</th>
                        <th>Completion</th>
                        <th><span class="float-right">Actions</span></th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="testRun in testRuns.data" :key="testRun.id" class="item-container">
                        <td>
                            <Link :href="route('test-run.show', testRun.id)">
                                {{ testRun.name }}
                            </Link>
                        </td>
                        <td>
                            <Link :href="route('project.show', testRun.project)" v-if="testRun.project">
                                {{ testRun.project.name }}
                            </Link>
                        </td>
                        <td>
                            <div>
                                <div>{{ formatDate(testRun.executed_at) }}</div>
                                <div class="text-xs text-gray-500">by {{ testRun.executed_by_user.name }}</div>
                            </div>
                        </td>
                        <td>
                            <div class="flex items-center">
                                <div class="w-16 bg-gray-200 rounded-full h-2 mr-2">
                                    <div
                                        class="bg-green-600 h-2 rounded-full"
                                        :style="{ width: testRun.pass_rate + '%' }"
                                    ></div>
                                </div>
                                <span class="text-sm font-medium">{{ testRun.pass_rate }}%</span>
                            </div>
                        </td>
                        <td>
                            <div class="flex items-center">
                                <div class="w-16 bg-gray-200 rounded-full h-2 mr-2">
                                    <div
                                        class="bg-blue-600 h-2 rounded-full"
                                        :style="{ width: testRun.completion_rate + '%' }"
                                    ></div>
                                </div>
                                <span class="text-sm font-medium">{{ testRun.completion_rate }}%</span>
                            </div>
                        </td>
                        <td>
                            <div class="btn-group float-right">
                                <button-link
                                    :href="route('test-run.show', testRun.id)"
                                >
                                    <i class="fa fa-eye text-gray-600 text-xs"></i>
                                </button-link>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="card-body" v-else>
            You have not created any test runs yet.
        </div>
    </layout>
</template>

<script>
import { Link, router } from '@inertiajs/vue3'
import { reactive } from 'vue'
import breadcrumbs from '@/Shared/Breadcrumbs.vue'
import layout from '@/Shared/Layout.vue'

export default {
    props: [
        'testRuns',
        'projects',
        'filters',
    ],
    components: {
        Link,
        breadcrumbs: breadcrumbs,
        layout: layout,
    },
    setup() {
        const filters = reactive({
            project_id: '',
        })

        const filterTestRuns = () => {
            router.get(route('test-run.index'), filters, {
                preserveState: true,
                preserveScroll: true,
            })
        }

        const formatDate = (date) => {
            return new Date(date).toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'short',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
            })
        }

        return {
            filters,
            filterTestRuns,
            formatDate,
        }
    }
}
</script>


