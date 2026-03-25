<template>
    <layout>
        <template #breadcrumbs>
            <breadcrumbs
                :breadcrumbs="[
                    {title: 'Home',     url: route('home')},
                    {title: 'Tasks'},
                ]"
            ></breadcrumbs>
        </template>
        <template #title> Tasks </template>
        <template #top-right-toolbar>
            <div class="flex items-center gap-3">
                <span
                    v-if="selectedTaskIds.length"
                    class="text-sm text-gray-600 dark:text-gray-400 hidden sm:inline"
                >
                    {{ selectedTaskIds.length }} selected
                </span>
                <span
                    v-if="tasks.length && selectedTaskIds.length && projects.length"
                    class="inline-flex"
                    title="Bulk actions"
                >
                    <actions-dropdown
                        :options="bulkActionsOptions"
                        direction="left"
                    />
                </span>
                <button-link :href="route('task.create')" color="blue">
                    <i class="fa fa-plus text-blue-100 mr-2"></i>
                    Create Task
                </button-link>
            </div>
        </template>
        <div class="card" v-if="tasks.length">
            <table class="table card-body">
                <thead>
                    <tr>
                        <th class="w-10">
                            <input
                                type="checkbox"
                                class="rounded border-gray-300 dark:border-gray-600"
                                :checked="allSelected"
                                @change="toggleSelectAll($event.target.checked)"
                            />
                        </th>
                        <th> Name </th>
                        <th> Parent </th>
                        <th> Project </th>
                        <th> Client </th>
                        <th> Status </th>
                        <th> <span class="float-right"> Actions </span> </th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="task in tasks"
                        :key="task.id"
                        class="item-container"
                    >
                        <td>
                            <input
                                type="checkbox"
                                class="rounded border-gray-300 dark:border-gray-600"
                                :value="task.id"
                                v-model="selectedTaskIds"
                            />
                        </td>
                        <td>
                            <Link :href="route('task.show', task)">
                                {{ task.shortName }}
                            </Link>
                        </td>
                        <td>
                            <Link :href="route('task.show', task.parent)" v-if="task.parent">
                                {{ task.parent.shortName }}
                            </Link>
                        </td>
                        <td>
                            <Link :href="route('project.show', task.project)" v-if="task.project">
                                {{ task.project.name }}
                            </Link>
                        </td>
                        <td>
                            <Link :href="route('client.show', task.project.client)" v-if="task.project && task.project.client">
                                {{ task.project.client.name }}
                            </Link>
                        </td>
                        <td>
                            <span v-if="task.task_status"
                                  :style="{ backgroundColor: task.task_status.color }"
                                  class="px-2 py-1 text-xs rounded-full text-white inline-block">
                                {{ task.task_status.name }}
                            </span>
                            <span v-else class="px-2 py-1 text-xs rounded-full bg-gray-300 text-gray-700 inline-block">
                                No Status
                            </span>
                        </td>
                        <td>
                            <div class="float-right">
                                <actions-dropdown :options="getTaskActions(task)" direction="left"></actions-dropdown>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="card-body" v-else>
            You have not created any tasks yet.
        </div>

        <modal
            :is-open="showBulkAssignModal"
            title="Assign tasks to project"
            :description="bulkAssignModalDescription"
            :show-default-footer="true"
            cancel-text="Cancel"
            confirm-text="Assign"
            :confirm-loading="bulkAssignSubmitting"
            :close-on-backdrop="true"
            :close-on-escape="true"
            @close="closeBulkAssignModal"
            @confirm="confirmBulkAssign"
        >
            <div class="mt-2">
                <label
                    for="bulk-assign-project"
                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                >
                    Project
                </label>
                <select
                    id="bulk-assign-project"
                    v-model="bulkProjectId"
                    class="block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                >
                    <option value="">Select a project</option>
                    <option v-for="project in projects" :key="project.id" :value="project.id">
                        {{ project.name }}
                    </option>
                </select>
            </div>
        </modal>

        <confirm-modal
            :is-open="showConfirmModal"
            :title="confirmModalTitle"
            :description="confirmModalDescription"
            :confirm-text="confirmModalConfirmText"
            @close="closeConfirmModal"
            @confirm="executeConfirmAction"
        />
    </layout>
</template>

<script>

    import { Link } from '@inertiajs/vue3';
    import breadcrumbs from '@/Shared/Breadcrumbs.vue';
    import deleteButton from '@/Shared/DeleteButton.vue';
    import layout from '@/Shared/Layout.vue';
    import actionsDropdown from '@/Shared/ActionsDropdown.vue';
    import confirmModal from '@/Shared/ConfirmModal.vue';
    import Modal from '@/components/Modal.vue';

    export default {
        props: [
            'tasks',
            'projects',
        ],
        components: {
            Link,
            breadcrumbs: breadcrumbs,
            deleteButton: deleteButton,
            layout: layout,
            actionsDropdown: actionsDropdown,
            confirmModal: confirmModal,
            Modal,
        },
        data() {
            return {
                bulkProjectId: '',
                bulkAssignSubmitting: false,
                showBulkAssignModal: false,
                selectedTaskIds: [],
                showConfirmModal: false,
                confirmModalTitle: '',
                confirmModalDescription: '',
                confirmModalConfirmText: 'Confirm',
                pendingAction: null,
            };
        },
        computed: {
            allSelected() {
                return this.tasks.length > 0 && this.selectedTaskIds.length === this.tasks.length;
            },
            bulkActionsOptions() {
                return [
                    {
                        name: 'Assign to project…',
                        callback: () => this.openBulkAssignModal(),
                    },
                ];
            },
            bulkAssignModalDescription() {
                const n = this.selectedTaskIds.length;
                const label = n === 1 ? 'task' : 'tasks';

                return `Choose which project to assign ${n} selected ${label} to.`;
            },
        },
        methods: {
            toggleSelectAll(checked) {
                this.selectedTaskIds = checked ? this.tasks.map((t) => t.id) : [];
            },
            openBulkAssignModal() {
                this.bulkProjectId = '';
                this.showBulkAssignModal = true;
            },
            closeBulkAssignModal() {
                this.showBulkAssignModal = false;
                this.bulkProjectId = '';
                this.bulkAssignSubmitting = false;
            },
            confirmBulkAssign() {
                if (this.bulkProjectId === '' || this.bulkProjectId === null) {
                    return;
                }
                if (this.bulkAssignSubmitting) {
                    return;
                }
                this.bulkAssignSubmitting = true;
                this.$inertia.patch(
                    route('task.bulk-assign-project'),
                    {
                        project_id: Number(this.bulkProjectId),
                        task_ids: this.selectedTaskIds,
                    },
                    {
                        preserveScroll: true,
                        onFinish: () => {
                            this.bulkAssignSubmitting = false;
                        },
                        onSuccess: () => {
                            this.closeBulkAssignModal();
                            this.selectedTaskIds = [];
                        },
                    },
                );
            },
            getTaskActions(task) {
                return [
                    {
                        name: 'Edit Task',
                        callback: () => {
                            this.$inertia.visit(route('task.edit', task));
                        }
                    },
                    {
                        name: 'Delete Task',
                        callback: () => {
                            this.openConfirmModal(
                                'Delete Task',
                                'Are you sure you want to delete this task?',
                                'Delete',
                                () => {
                                    this.$inertia.delete(route('task.destroy', task.id));
                                }
                            );
                        }
                    }
                ];
            },
            openConfirmModal(title, description, confirmText, action) {
                this.confirmModalTitle = title;
                this.confirmModalDescription = description;
                this.confirmModalConfirmText = confirmText;
                this.pendingAction = action;
                this.showConfirmModal = true;
            },
            closeConfirmModal() {
                this.showConfirmModal = false;
                this.pendingAction = null;
            },
            executeConfirmAction() {
                if (this.pendingAction) {
                    this.pendingAction();
                }
                this.closeConfirmModal();
            },
        }
    }

</script>