<template>
    <layout>
        <template #breadcrumbs>
            <breadcrumbs :breadcrumbs="[
                {title: 'Home',     url: route('home')},
                {title: 'Tasks',    url: route('task.index')},
                {title: task.name},
            ]" ></breadcrumbs>
        </template>

        <!-- Main Task Card -->
        <div class="card">
            <div class="flex items-start justify-between mb-4">
                <div class="flex-1">
                    <h1 class="font-medium text-gray-dark text-2xl"> {{ task.name }} </h1>
                </div>
                <div class="flex gap-2 ml-4">
                    <button-link
                        :href="route('task.edit', task.id)"
                        size="sm"
                    >
                        <i class="fa fa-edit mr-1"></i>
                        Edit
                    </button-link>
                    <delete-button
                        :url="route('task.destroy', task.id)"
                        redirect-to="task.index"
                        size="sm"
                    >
                        <i class="fa fa-trash mr-1"></i>
                        Delete
                    </delete-button>
                </div>
            </div>

            <p v-if="task.description" class="mt-4 text-gray-600">
                {{ task.description }}
            </p>
            <p v-else class="mt-4 text-gray-400 italic">
                No description provided
            </p>

            <!-- Metadata Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6 pt-6 border-t border-gray-200">
                <!-- Status -->
                <div v-if="task.project && task.project.task_statuses && task.project.task_statuses.length > 0">
                    <label class="block text-sm font-medium text-gray-500 mb-2">
                        <i class="fa fa-flag mr-2"></i>Status
                    </label>
                    <Menu as="div" class="relative inline-block text-left">
                        <MenuButton
                            class="inline-flex items-center gap-x-2 rounded-md px-3 py-2 text-sm font-semibold shadow-sm inset-ring-1 hover:bg-gray-50 dark:hover:bg-white/10"
                            :style="{
                                backgroundColor: currentStatus ? currentStatus.color : '#e5e7eb',
                                color: currentStatus && isLightColor(currentStatus.color) ? '#000' : '#fff',
                                borderColor: currentStatus ? currentStatus.color : '#d1d5db'
                            }"
                        >
                            <span>{{ currentStatus ? currentStatus.name : 'No Status' }}</span>
                            <ChevronDownIcon class="size-5" aria-hidden="true" />
                        </MenuButton>

                        <transition
                            enter-active-class="transition ease-out duration-100"
                            enter-from-class="transform opacity-0 scale-95"
                            enter-to-class="transform opacity-100 scale-100"
                            leave-active-class="transition ease-in duration-75"
                            leave-from-class="transform opacity-100 scale-100"
                            leave-to-class="transform opacity-0 scale-95"
                        >
                            <MenuItems class="absolute left-0 z-10 mt-2 w-56 origin-top-left rounded-md bg-white shadow-lg outline-1 outline-black/5 dark:bg-gray-800 dark:shadow-none dark:-outline-offset-1 dark:outline-white/10">
                                <div class="py-1">
                                    <MenuItem
                                        v-for="status in task.project.task_statuses"
                                        :key="status.id"
                                        v-slot="{ active }"
                                    >
                                        <button
                                            @click="changeStatus(status.id)"
                                            :class="[
                                                active ? 'bg-gray-100 dark:bg-white/5' : '',
                                                'flex items-center w-full px-4 py-2 text-sm text-left text-gray-700 dark:text-gray-300'
                                            ]"
                                            :disabled="isUpdatingStatus"
                                        >
                                            <div
                                                :style="{ backgroundColor: status.color }"
                                                class="w-3 h-3 rounded-full mr-2"
                                            ></div>
                                            <span>{{ status.name }}</span>
                                            <span v-if="currentStatus && status.id === currentStatus.id" class="ml-auto">
                                                <i class="fa fa-check text-green-500"></i>
                                            </span>
                                        </button>
                                    </MenuItem>
                                </div>
                            </MenuItems>
                        </transition>
                    </Menu>
                </div>

                <!-- Project -->
                <div v-if="task.project">
                    <label class="block text-sm font-medium text-gray-500 mb-2">
                        <i class="fa fa-briefcase mr-2"></i>Project
                    </label>
                    <a :href="route('project.show', task.project.id)" class="text-blue-600 hover:text-blue-900 text-base font-medium">
                        {{ task.project.name }}
                    </a>
                </div>

                <!-- Client -->
                <div v-if="task.project && task.project.client">
                    <label class="block text-sm font-medium text-gray-500 mb-2">
                        <i class="fa fa-users mr-2"></i>Client
                    </label>
                    <a :href="route('client.show', task.project.client.id)" class="text-blue-600 hover:text-blue-900 text-base font-medium">
                        {{ task.project.client.name }}
                    </a>
                </div>

                <!-- Parent Task -->
                <div v-if="task.parent">
                    <label class="block text-sm font-medium text-gray-500 mb-2">
                        <i class="fa fa-sitemap mr-2"></i>Parent Task
                    </label>
                    <a :href="route('task.show', task.parent.id)" class="text-blue-600 hover:text-blue-900 text-base font-medium">
                        {{ task.parent.name }}
                    </a>
                </div>

                <!-- Total Time -->
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-2">
                        <i class="fa fa-clock mr-2"></i>Total Time Tracked
                    </label>
                    <div class="font-mono text-lg text-gray-900"> {{ totalDurationForHumans }} </div>
                </div>

                <!-- Sessions Count -->
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-2">
                        <i class="fa fa-list mr-2"></i>Sessions
                    </label>
                    <div class="text-lg text-gray-900"> {{ task.sessions.length }} session{{ task.sessions.length !== 1 ? 's' : '' }} </div>
                </div>
            </div>
        </div>

        <!-- Sessions Section -->
        <div class="card mt-4" v-if="task.sessions && task.sessions.length > 0">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-medium text-gray-dark">Sessions</h2>
            </div>
            <session-table
                :sessions="task.sessions"
                :hide-linked-to-column="true"
                :hide-invoice-column="true"
            ></session-table>
        </div>

        <!-- Empty State for Sessions -->
        <div class="card mt-4" v-else>
            <div class="text-center py-8 text-gray-500">
                <i class="fa fa-clock text-4xl mb-4"></i>
                <p class="text-lg mb-2">No sessions yet</p>
                <p class="mb-4">Time tracking sessions for this task will appear here.</p>
            </div>
        </div>
    </layout>
</template>

<script>
    import breadcrumbs from '@/Shared/Breadcrumbs.vue';
    import deleteButton from '@/Shared/DeleteButton.vue';
    import layout from '@/Shared/Layout.vue';
    import sessionTable from '@/Shared/SessionTable.vue';
    import { Menu, MenuButton, MenuItem, MenuItems } from '@headlessui/vue';
    import { ChevronDownIcon } from '@heroicons/vue/20/solid';

    export default {
        props: [
            'task',
            'totalDurationForHumans',
        ],
        components: {
            breadcrumbs: breadcrumbs,
            deleteButton: deleteButton,
            layout: layout,
            sessionTable: sessionTable,
            Menu,
            MenuButton,
            MenuItem,
            MenuItems,
            ChevronDownIcon,
        },
        data() {
            return {
                isUpdatingStatus: false,
            };
        },
        computed: {
            currentStatus() {
                if (!this.task.task_status) {
                    return null;
                }
                return this.task.task_status;
            }
        },
        methods: {
            async changeStatus(statusId) {
                if (this.isUpdatingStatus || statusId === this.task.status_id) {
                    return;
                }

                this.isUpdatingStatus = true;
                const oldStatusId = this.task.status_id;
                const oldTaskStatus = this.task.task_status;

                // Optimistically update the UI
                this.task.status_id = statusId;
                this.task.task_status = this.task.project.task_statuses.find(s => s.id === statusId);

                try {
                    const response = await fetch(route('task.updateStatus', this.task.id), {
                        method: 'PATCH',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({ status_id: statusId }),
                    });

                    const data = await response.json();

                    if (!response.ok) {
                        // Revert on error
                        this.task.status_id = oldStatusId;
                        this.task.task_status = oldTaskStatus;

                        const errorMessage = data.errors?.status_id?.[0] || data.message || 'Failed to update task status';
                        alert(errorMessage);
                    }
                } catch (error) {
                    // Revert on network error
                    this.task.status_id = oldStatusId;
                    this.task.task_status = oldTaskStatus;
                    alert('Network error: Failed to update task status');
                } finally {
                    this.isUpdatingStatus = false;
                }
            },
            isLightColor(color) {
                // Convert hex to RGB and calculate relative luminance
                const hex = color.replace('#', '');
                const r = parseInt(hex.substr(0, 2), 16);
                const g = parseInt(hex.substr(2, 2), 16);
                const b = parseInt(hex.substr(4, 2), 16);
                const luminance = (0.299 * r + 0.587 * g + 0.114 * b) / 255;
                return luminance > 0.5;
            }
        }
    }

</script>
