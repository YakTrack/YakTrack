<template>
    <layout>
        <template #breadcrumbs>
            <breadcrumbs
                :breadcrumbs="[
                    {title: 'Home',     url: route('home')},
                    {title: 'Projects', url: route('project.index')},
                    {title: 'Archived Projects'},
                ]"
            ></breadcrumbs>
        </template>
        <template #title> Archived Projects </template>
        <template #top-right-toolbar>
            <button-link :href="route('project.index')" color="blue">
                <i class="fa fa-arrow-left mr-2"></i>
                Back to Projects
            </button-link>
        </template>
        <div class="card">
            <table class="table card-body" v-if="projects.length">
                <thead>
                    <tr>
                        <th> Name </th>
                        <th> Client </th>
                        <th> Archived Date </th>
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
                        {{ formatDate(project.archived_at) }}
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
                No archived projects found.
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
import layout from '@/Shared/Layout.vue';
import actionsDropdown from '@/Shared/ActionsDropdown.vue';
import confirmModal from '@/Shared/ConfirmModal.vue';

export default {
    props: ['projects'],

    components: {
        Link,
        breadcrumbs: breadcrumbs,
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
                    name: 'View Project',
                    callback: () => {
                        this.$inertia.visit(route('project.show', {project: project}));
                    }
                },
                {
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
                }
            ];
            
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
        formatDate(date) {
            return new Date(date).toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'short',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        }
    }
}

</script>
