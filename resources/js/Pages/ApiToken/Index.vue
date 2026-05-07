<template>
    <layout>
        <template #breadcrumbs>
            <breadcrumbs
                :breadcrumbs="[
                    {title: 'Home', url: route('home')},
                    {title: 'API Tokens'},
                ]"
            ></breadcrumbs>
        </template>
        <template #title> API Tokens </template>
        <template #top-right-toolbar>
            <button-link :href="route('api-tokens.create')" color="blue">
                <i class="fa fa-plus text-blue-100 mr-2"></i>
                Create Token
            </button-link>
        </template>
        <div class="card item-type-container" data-item-type="api-token">
            <table class="table w-full card-body" v-if="tokens.length">
                <thead>
                    <tr>
                        <th> Name </th>
                        <th> Last Used </th>
                        <th> Created </th>
                        <th> <span class="float-right"> Actions </span> </th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="token in tokens"
                        :key="token.id"
                        class="item-container"
                        :data-item-name="token.name"
                    >
                        <td>{{ token.name }}</td>
                        <td>{{ token.last_used_at || 'Never' }}</td>
                        <td>{{ token.created_at }}</td>
                        <td>
                            <div class="float-right">
                                <actions-dropdown :options="getTokenActions(token)" direction="left"></actions-dropdown>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="card-body" v-else>
                You have not created any API tokens yet.
            </div>
        </div>

        <modal
            :is-open="showTokenModal"
            title="Your New API Token"
            @close="showTokenModal = false"
        >
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                Copy this token now. It will not be shown again.
            </p>
            <div class="flex items-center gap-2">
                <code class="flex-1 block p-3 bg-gray-100 dark:bg-gray-800 rounded-lg text-sm font-mono break-all select-all text-gray-900 dark:text-gray-100">{{ newToken }}</code>
                <button
                    type="button"
                    @click="copyToken"
                    class="shrink-0 px-3 py-3 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors duration-200"
                    :title="copied ? 'Copied!' : 'Copy to clipboard'"
                >
                    <span v-if="copied">Copied!</span>
                    <span v-else>Copy</span>
                </button>
            </div>
            <template #footer>
                <button
                    type="button"
                    @click="showTokenModal = false"
                    class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-lg hover:bg-blue-700 transition-colors duration-200"
                >
                    Done
                </button>
            </template>
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
    import breadcrumbs from '@/Shared/Breadcrumbs.vue';
    import layout from '@/Shared/Layout.vue';
    import actionsDropdown from '@/Shared/ActionsDropdown.vue';
    import confirmModal from '@/Shared/ConfirmModal.vue';
    import modal from '@/components/Modal.vue';

    export default {
        props: [
            'tokens',
        ],
        components: {
            breadcrumbs: breadcrumbs,
            layout: layout,
            actionsDropdown: actionsDropdown,
            confirmModal: confirmModal,
            modal: modal,
        },
        data() {
            return {
                showTokenModal: false,
                newToken: '',
                copied: false,
                showConfirmModal: false,
                confirmModalTitle: '',
                confirmModalDescription: '',
                confirmModalConfirmText: 'Confirm',
                pendingAction: null,
            };
        },
        mounted() {
            const token = this.$page.props.flash?.newToken;
            if (token) {
                this.newToken = token;
                this.showTokenModal = true;
            }
        },
        methods: {
            copyToken() {
                navigator.clipboard.writeText(this.newToken).then(() => {
                    this.copied = true;
                    setTimeout(() => { this.copied = false; }, 2000);
                });
            },
            getTokenActions(token) {
                return [
                    {
                        name: 'Revoke Token',
                        callback: () => {
                            this.openConfirmModal(
                                'Revoke Token',
                                `Are you sure you want to revoke the token "${token.name}"? Any applications using this token will lose access.`,
                                'Revoke',
                                () => {
                                    this.$inertia.delete(route('api-tokens.destroy', token.id));
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
