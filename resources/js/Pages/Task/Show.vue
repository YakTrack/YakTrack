<template>
    <layout>
        <template #breadcrumbs>
            <breadcrumbs :breadcrumbs="[
                {title: 'Home',     url: route('home')},
                {title: 'Tasks',    url: route('task.index')},
                {title: task.name},
            ]" ></breadcrumbs>
        </template>
        <template #title> {{ task.name }} </template>
        <div class="card">
            <h1 class="font-medium text-gray-dark"> {{ task.name }} </h1>

            <!-- Status Dropdown -->
            <div class="mt-4" v-if="task.project && task.project.task_statuses && task.project.task_statuses.length > 0">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Status
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

            <div class="p4 mt-4" v-if="task.project">
                <i class="fa fa-briefcase text-2xl text-gray-300 mr-2"></i>
                <span class="text-2xl font-light"> {{ task.project.name }} </span>
            </div>
            <div class="p4 mt-4" v-if="task.project && task.project.client">
                <i class="fa fa-users text-2xl text-gray-300 mr-2"></i>
                <span class="text-2xl font-light"> {{ task.project.client.name }} </span>
            </div>
            <div class="mt-2">
                <div class="font-mono text-lg"> {{ totalDurationForHumans }} </div>
                <div class="mt-1 text-gray-500"> Total time </div>
            </div>
            <p class="mt-4">
                {{ task.description }}
            </p>
        </div>
        <session-table
            class="mt-6"
            :sessions="task.sessions"
            :hide-linked-to-column="true"
            :hide-invoice-column="true"
        ></session-table>
    </layout>
</template>

<script>
    import breadcrumbs from '@/Shared/Breadcrumbs.vue';
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
