<template>
    <layout>
        <template #breadcrumbs>
            <breadcrumbs
                :breadcrumbs="[
                    {title: 'Home',     url: route('home')},
                    {title: 'Sessions'},
                ]"
            ></breadcrumbs>
        </template>
        <template #modals>
          <modal
              open-on="sessions.link-to-invoice"
              close-on="sessions.linked-to-invoice"
              :on-submit="linkSelectedSessionsToInvoice"
          >
              <template #default="modal">
                  <h3 class="text-center"> Link Selected Sessions To Invoice </h3>
                  <div class="mt-8 form-group">
                      {{ modal.payload }}
                      <label for="invoice_id"> Select Invoice </label>
                      <invoice-select
                          :invoices="invoices"
                          :on-change="selectInvoice"
                          v-model="modal.payload"
                      ></invoice-select>
                  </div>
              </template>
          </modal>
          <modal
              open-on="sessions.link-to-sprint"
              close-on="sessions.linked-to-sprint"
              :on-submit="linkSelectedSessionsToSprint"
          >
              <template #default="modal">
                  <h3 class="text-center"> Link Selected Sessions To Sprint </h3>
                  <div class="mt-8 form-group">
                      <label for="sprint_id"> Select Sprint </label>
                      <sprint-select
                          :sprints="sprints"
                          :on-change="selectSprint"
                          v-model="modal.payload"
                      ></sprint-select>
                  </div>
              </template>
          </modal>
          <modal
              open-on="sessions.link-to-task"
              close-on="sessions.linked-to-task"
              :on-submit="linkSelectedSessionsToTask"
          >
              <template #default="modal">
                  <h3 class="text-center"> Link Selected Sessions To Task </h3>
                  <div class="mt-8 form-group">
                      <label for="task_id"> Select Task </label>
                      <task-select
                          :tasks="tasks"
                          :task="selectedTaskId"
                          :on-change="selectTask"
                      ></task-select>
                  </div>
              </template>
          </modal>
          <split-session-modal
              :is-open="showSplitSessionModal"
              :session="sessionToSplit"
              :sprints="sprints"
              :tasks="tasks"
              :pending-tasks="pendingTasksForSplit"
              :on-close="closeSplitSessionModal"
              :on-submit="handleSplitSession"
          ></split-session-modal>
          <manage-pending-tasks-modal
              :is-open="showManagePendingTasksModal"
              :session="sessionForPendingTasks"
              :tasks="tasks"
              :on-close="closeManagePendingTasksModal"
              @changed="reloadSessions"
          ></manage-pending-tasks-modal>
          <edit-session-modal
              ref="editSessionModal"
              :is-open="showEditSessionModal"
              :session="sessionToEdit"
              :on-close="closeEditSessionModal"
              :on-submit="handleEditSession"
          ></edit-session-modal>
          <add-session-modal
              ref="addSessionModal"
              :is-open="showAddSessionModal"
              :title="addSessionModalTitle"
              :initial-form="addSessionInitialForm"
              :tasks="tasks"
              :sprints="sprints"
              :invoices="invoices"
              :session-categories="sessionCategories"
              :on-close="closeAddSessionModal"
              :on-submit="handleAddSession"
          ></add-session-modal>
        </template>
        <template #title> Sessions </template>
        <template #top-right-toolbar>
            <button type="button" class="btn" :class="(showFilters ? 'btn-blue' : 'btn-default') + ' mr-2'" @click="toggleShowFilters()">
                <i class="fa fa-filter"></i>
            </button>
            <button-link :href="route('session.create')" class="mr-2">
                <i class="fa fa-stopwatch"></i>
            </button-link>
            <button @click="startSession" class="btn btn-green">
                <i class="fa fa-play sm:pr-2"></i>
                <span class="hidden sm:inline"> Start Session </span>
            </button>
        </template>

        <index-session-table
            :days="days"
            :third-party-applications="thirdPartyApplications"
            :invoices="invoices"
            :sprints="sprints"
            :page="page"
            :per-page="perPage"
            :total="total"
            :last-page="lastPage"
            :show-filters="showFilters"
            :on-change-selected-session-ids="onChangeSelectedSessionIds"
            :on-split-session="openSplitSessionModal"
            :on-edit-session="openEditSessionModal"
            :on-add-session-before="openAddSessionBefore"
            :on-add-session-after="openAddSessionAfter"
            :on-manage-pending-tasks="openManagePendingTasksModal"
            :on-split-pending-tasks="openSplitPendingTasks"
            :highlighted-session-id="recentlyEditedSessionId"
        ></index-session-table>

    </layout>
</template>

<script>

import breadcrumbs from '@/Shared/Breadcrumbs.vue'
import indexSessionTable from '@/components/IndexSessionTable.vue'
import layout from '@/Shared/Layout.vue'
import searchParams from '@/SearchParams'
import urlParser from '@/UrlParser.js';
import modal from '@/components/LegacyModal.vue'
import invoiceSelect from '@/Shared/InvoiceSelect.vue';
import sprintSelect from '@/Shared/SprintSelect.vue';
import taskSelect from '@/Shared/TaskSelect.vue';
import splitSessionModal from '@/components/SplitSessionModal.vue';
import managePendingTasksModal from '@/components/ManagePendingTasksModal.vue';
import editSessionModal from '@/components/EditSessionModal.vue';
import addSessionModal from '@/components/AddSessionModal.vue';
import modernModal from '@/components/Modal.vue';

// Tracks split-pending session ids already handled this page-lifetime. Inertia v1
// restores pages from its history cache (browser Back), remounting this component
// and re-reading the stale flash.splitPendingSessionId; this guard stops the split
// modal from re-opening for an id that was already dealt with.
const handledSplitPendingSessionIds = new Set();

export default {
    data() {
        return {
            showFilters: searchParams.get('show-filters') == 'true',
            urlParser: urlParser,
            selectedSprintId: null,
            selectedTaskId: null,
            sessionToSplit: null,
            splitTime: null,
            showSplitSessionModal: false,
            pendingTasksForSplit: [],
            showManagePendingTasksModal: false,
            sessionForPendingTasks: null,
            sessionToEdit: null,
            showEditSessionModal: false,
            showAddSessionModal: false,
            addSessionModalTitle: 'Add Session',
            addSessionInitialForm: null,
            recentlyEditedSessionId: null,
            recentlyEditedSessionTimeout: null,
        }
    },
    components: {
        breadcrumbs: breadcrumbs,
        indexSessionTable: indexSessionTable,
        layout: layout,
        modal: modal,
        invoiceSelect: invoiceSelect,
        sprintSelect: sprintSelect,
        taskSelect: taskSelect,
        splitSessionModal: splitSessionModal,
        managePendingTasksModal: managePendingTasksModal,
        editSessionModal: editSessionModal,
        addSessionModal: addSessionModal,
        modernModal: modernModal,
    },
    props: {
        days: Array,
        invoices: Array,
        tasks: Array,
        sprints: Array,
        sessionCategories: Array,
        thirdPartyApplications: Array,
        page: Number,
        perPage: Number,
        total: Number,
        lastPage: Number,
    },
    methods: {
        toggleShowFilters() {
            events.emit('toggle-show-filters');
        },
        startSession() {
            this.$inertia.post(route('session.start'));
        },
        linkSelectedSessionsToInvoice() {
            events.emit('sessions.linked-to-invoice');

            this.$inertia.patch(route('invoice.update', this.selectedInvoiceId), {
                sessions: this.selectedSessionIds,
                redirectToSessionsScreen: true,
            });
        },
        selectInvoice(invoiceId) {
            this.selectedInvoiceId = invoiceId;
        },
        linkSelectedSessionsToSprint() {
            events.emit('sessions.linked-to-sprint');

            this.$inertia.patch(route('sessions.update'), {
                sessions: this.selectedSessionIds.reduce((sessions, sessionId) => {
                    sessions[sessionId] = { sprint_id: this.selectedSprintId }
                    return sessions
                }, {})
            });
        },
        linkSelectedSessionsToTask() {
            events.emit('sessions.linked-to-task');

            this.$inertia.patch(route('sessions.update'), {
                sessions: this.selectedSessionIds.reduce((sessions, sessionId) => {
                    sessions[sessionId] = { task_id: this.selectedTaskId }

                    return sessions
                }, {}),
            });
        },
        selectSprint(sprintId) {
            this.selectedSprintId = sprintId;
        },
        selectTask(taskId) {
            this.selectedTaskId = taskId;
        },
        onChangeSelectedSessionIds(newValue) {
          this.selectedSessionIds = newValue
        },
        openSplitSessionModal(session) {
            this.pendingTasksForSplit = [];
            this.sessionToSplit = session;
            this.showSplitSessionModal = true;
        },
        openSplitPendingTasks(session) {
            this.pendingTasksForSplit = Array.isArray(session.pending_tasks) ? session.pending_tasks : [];
            this.sessionToSplit = session;
            this.showSplitSessionModal = true;
        },
        closeSplitSessionModal() {
            this.showSplitSessionModal = false;
            this.sessionToSplit = null;
            this.splitTime = null;
            this.pendingTasksForSplit = [];
        },
        openManagePendingTasksModal(session) {
            this.sessionForPendingTasks = session;
            this.showManagePendingTasksModal = true;
        },
        closeManagePendingTasksModal() {
            this.showManagePendingTasksModal = false;
            this.sessionForPendingTasks = null;
        },
        reloadSessions() {
            this.$inertia.reload({ only: ['days'] });
        },
        maybeOpenPendingSplit() {
            const sessionId = this.$page.props.flash?.splitPendingSessionId;

            if (!sessionId || handledSplitPendingSessionIds.has(sessionId)) {
                return;
            }

            handledSplitPendingSessionIds.add(sessionId);

            // Consume the local flash value so a history-cache restore (browser Back)
            // that remounts this page cannot re-trigger from the same stale prop.
            if (this.$page.props.flash) {
                this.$page.props.flash.splitPendingSessionId = null;
            }

            const session = this.days
                .flatMap((day) => day.sessions)
                .find((candidate) => candidate.id === sessionId);

            if (session && Array.isArray(session.pending_tasks) && session.pending_tasks.length >= 2) {
                this.openSplitPendingTasks(session);
            }
        },
        handleSplitSession(session, segments, fromPendingTasks = false) {
            this.$inertia.post(route('session.split', session.id), {
                segments,
                from_pending_tasks: fromPendingTasks,
            });
            this.closeSplitSessionModal();
        },
        openEditSessionModal(session) {
            this.sessionToEdit = session;
            this.showEditSessionModal = true;
        },
        closeEditSessionModal() {
            this.showEditSessionModal = false;
            this.sessionToEdit = null;
        },
        handleEditSession(form) {
            const sessionId = this.sessionToEdit.id;

            this.$inertia.patch(route('session.update', { session: sessionId }), {
                ...form,
                from_modal: true,
            }, {
                preserveScroll: true,
                onSuccess: () => {
                    this.closeEditSessionModal();
                    this.highlightEditedSession(sessionId);
                },
                onError: () => {
                    this.$refs.editSessionModal?.resetSubmitting();
                },
            });
        },
        openAddSessionBefore(session) {
            this.openAddSession(session, 'before');
        },
        openAddSessionAfter(session) {
            this.openAddSession(session, 'after');
        },
        openAddSession(session, direction) {
            this.addSessionModalTitle = direction === 'before' ? 'Add Session Before' : 'Add Session After';
            this.addSessionInitialForm = {
                started_at: direction === 'before' ? session.add_before_started_at : session.add_after_started_at,
                ended_at: direction === 'before' ? session.add_before_ended_at : session.add_after_ended_at,
                task_id: session.task_id,
                sprint_id: session.sprint_id,
                invoice_id: session.invoice_id,
                session_category_id: session.session_category_id,
                comment: null,
                is_billable: Boolean(session.is_billable),
            };
            this.showAddSessionModal = true;
        },
        closeAddSessionModal() {
            this.showAddSessionModal = false;
            this.addSessionInitialForm = null;
        },
        handleAddSession(form) {
            this.$inertia.post(route('session.store'), form, {
                preserveScroll: true,
                onSuccess: () => {
                    this.closeAddSessionModal();
                },
                onError: () => {
                    this.$refs.addSessionModal?.resetSubmitting();
                },
            });
        },
        highlightEditedSession(sessionId) {
            if (this.recentlyEditedSessionTimeout) {
                clearTimeout(this.recentlyEditedSessionTimeout);
            }

            this.recentlyEditedSessionId = sessionId;

            this.recentlyEditedSessionTimeout = setTimeout(() => {
                this.recentlyEditedSessionId = null;
                this.recentlyEditedSessionTimeout = null;
            }, 6000);
        },
    },
    mounted() {
        this.maybeOpenPendingSplit();
    },
    created() {
        this._onToggleShowFilters = () => {
            this.$inertia.visit(this.urlParser.current({
                showFilters: !this.showFilters,
            }), {
                replace: true,
                preserveScroll: true,
            });
        };
        events.on('toggle-show-filters', this._onToggleShowFilters);
    },
    beforeUnmount() {
        events.off('toggle-show-filters', this._onToggleShowFilters);

        if (this.recentlyEditedSessionTimeout) {
            clearTimeout(this.recentlyEditedSessionTimeout);
        }
    },
}

</script>
