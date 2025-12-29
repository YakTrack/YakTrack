<template>
    <layout>
        <template #breadcrumbs>
            <breadcrumbs
                :breadcrumbs="[
                    {title: 'Home',     url: route('home')},
                    {title: 'Projects'},
                ]"
            ></breadcrumbs>
        </template>
        <template #title> Projects </template>
        <template #top-right-toolbar>
            <div class="flex space-x-2">
                <button-link :href="route('project.archived')" color="gray">
                    <i class="fa fa-archive mr-2"></i>
                    View Archived
                </button-link>
                <button-link :href="route('project.create')" color="blue">
                    <i class="fa fa-plus mr-2 text-blue-100"></i>
                    Create Project
                </button-link>
            </div>
        </template>
        <div class="card">
            <table class="table card-body" v-if="projects.length">
                <thead>
                    <tr>
                        <th> Name </th>
                        <th> Client </th>
                        <th> <span class="float-right"> Actions </span> </th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="project in projects"
                        :key="project.id"
                        class="item-container"
                    >
                    <td>
                        <Link :href="route('project.show', {project: project.id})">
                            {{ project.name }}
                        </Link>
                    </td>
                    <td>
                        <Link
                            v-if="project.client"
                            :href="route('client.show', {client: project.client.id})">
                            {{ project.client.name }}
                        </Link>
                    </td>
                    <td>
                        <div class="float-right">
                            <actions-dropdown :options="getProjectActions(project)" direction="left"></actions-dropdown>
                        </div>
                    </td>
                    </tr>
                </tbody>
            </table>
            <div class="card-body" v-else>
                You have not created any projects yet.
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
    props: ['projects'],

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
        getProjectActions(project) {
            const actions = [
                {
                    name: 'Edit Project',
                    callback: () => {
                        this.$inertia.visit(route('project.edit', {project: project}));
                    }
                }
            ];
            
            if (project.isDeletable) {
                actions.push({
                    name: 'Delete Project',
                    callback: () => {
                        this.openConfirmModal(
                            'Delete Project',
                            'Are you sure you want to delete this project?',
                            'Delete',
                            () => {
                                this.$inertia.delete(route('project.destroy', project.id));
                            }
                        );
                    }
                });
            }

            // Add archive/unarchive action
            if (project.is_archived) {
                actions.push({
                    name: 'Unarchive Project',
                    callback: () => {
                        this.openConfirmModal(
                            'Unarchive Project',
                            'Are you sure you want to unarchive this project?',
                            'Unarchive',
                            () => {
                                this.$inertia.patch(route('project.unarchive', project.id));
                            }
                        );
                    }
                });
            } else {
                actions.push({
                    name: 'Archive Project',
                    callback: () => {
                        this.openConfirmModal(
                            'Archive Project',
                            'Are you sure you want to archive this project?',
                            'Archive',
                            () => {
                                this.$inertia.patch(route('project.archive', project.id));
                            }
                        );
                    }
                });
            }
            
            return actions;
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
