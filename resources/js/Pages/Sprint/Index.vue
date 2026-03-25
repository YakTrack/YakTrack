<template>
    <layout>
        <template #breadcrumbs>
            <breadcrumbs
                :breadcrumbs="[
                    {title: 'Home',     url: route('home')},
                    {title: 'Sprints'},
                ]"
            ></breadcrumbs>
        </template>
        <template #title> Sprints </template>
        <template #top-right-toolbar> 
            <button-link :href="route('sprint.create')" color="blue">
                <i class="fa fa-plus text-blue-100 mr-2"></i>
                Create Sprint
            </button-link>
        </template>
        <div class="card item-type-container" data-item-type="sprint">
            <table class="table card-body" v-if="sprints.length">
                <thead>
                    <tr>
                        <th> Name </th>
                        <th> Project </th>
                        <th> Status </th>
                        <th class="text-right"> Total Hours </th>
                        <th> <span class="float-right"> Actions </span> </th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="sprint in sprints"
                        :key="sprint.id"
                        class="item-container"
                        :data-item-name="sprint.name"
                        :data-item-destroy-route="route('sprint.destroy', sprint.id)"
                    >
                        <td>
                            <Link :href="route('sprint.show', sprint.id)">
                                {{ sprint.name }}
                            </Link>
                        </td>
                        <td>
                            <template v-if="sprint.projects && sprint.projects.length">
                                <template v-for="(project, index) in sprint.projects" :key="project.id">
                                    <Link :href="route('project.show', project.id)">{{ project.name }}</Link><span v-if="index < sprint.projects.length - 1">, </span>
                                </template>
                            </template>
                        </td>
                        <td>
                            <div class="text-green" v-if="sprint.is_open"> Open </div>
                        </td>
                        <td class="text-right font-mono">
                            {{ sprint.totalDurationForHumans }} 
                        </td>
                        <td>
                            <div class="float-right">
                                <actions-dropdown :options="getSprintActions(sprint)" direction="left"></actions-dropdown>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="card-body" v-else>
                You have not created any sprints yet.
            </div>
        </div>

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

    export default {
        props: [
            'sprints',
        ],
        components: {
            Link,
            breadcrumbs: breadcrumbs,
            deleteButton: deleteButton,
            layout: layout,
            actionsDropdown: actionsDropdown,
            confirmModal: confirmModal,
        },
        data() {
            return {
                showConfirmModal: false,
                confirmModalTitle: '',
                confirmModalDescription: '',
                confirmModalConfirmText: 'Confirm',
                pendingAction: null,
            };
        },
        methods: {
            getSprintActions(sprint) {
                return [
                    {
                        name: 'Edit Sprint',
                        callback: () => {
                            this.$inertia.visit(route('sprint.edit', sprint.id));
                        }
                    },
                    {
                        name: 'Delete Sprint',
                        callback: () => {
                            this.openConfirmModal(
                                'Delete Sprint',
                                'Are you sure you want to delete this sprint?',
                                'Delete',
                                () => {
                                    this.$inertia.delete(route('sprint.destroy', sprint.id));
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
        },
    }
</script>
