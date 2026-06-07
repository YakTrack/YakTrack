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
              :on-close="closeSplitSessionModal"
              :on-submit="handleSplitSession"
          ></split-session-modal>
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
import modernModal from '@/components/Modal.vue';

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
        modernModal: modernModal,
    },
    props: {
        days: Array,
        invoices: Array,
        tasks: Array,
        sprints: Array,
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
            this.sessionToSplit = session;
            this.showSplitSessionModal = true;
        },
        closeSplitSessionModal() {
            this.showSplitSessionModal = false;
            this.sessionToSplit = null;
            this.splitTime = null;
        },
        handleSplitSession(session, segments) {
            this.$inertia.post(route('session.split', session.id), {
                segments,
            });
            this.closeSplitSessionModal();
        },
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
    },
}

</script>
