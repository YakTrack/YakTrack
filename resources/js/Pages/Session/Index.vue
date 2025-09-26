<template>
    <layout>
        <template slot="breadcrumbs">
            <breadcrumbs
                :breadcrumbs="[
                    {title: 'Home',     url: route('home')},
                    {title: 'Sessions'},
                ]"
            ></breadcrumbs>
        </template>
        <template slot="modals">
          <modal
              open-on="sessions.link-to-invoice"
              close-on="sessions.linked-to-invoice"
              :on-submit="linkSelectedSessionsToInvoice"
          >
              <template slot-scope="modal">
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
              <template slot-scope="modal">
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
              open-on="session.split"
              close-on="session.split-cancelled"
              :on-submit="splitSession"
              primary-button-text="Split Session"
              cancel-button-text="Cancel"
          >
              <template slot-scope="modal">
                  <h3 class="text-center mb-6"> Split Session </h3>
                  <div class="mb-4">
                      <p class="text-sm text-gray-600 mb-4">
                          Enter the time when you want to split this session. The original session will end at this time, and a new session will start from this time.
                      </p>
                      <div class="form-group">
                          <label for="split_time" class="block text-sm font-medium text-gray-700 mb-2"> Split Time </label>
                          <input
                              id="split_time"
                              type="datetime-local"
                              v-model="splitTime"
                              class="form-input w-full"
                              :min="sessionToSplit ? sessionToSplit.startedAtInputFormat : ''"
                              :max="sessionToSplit ? sessionToSplit.endedAtInputFormat : ''"
                          />
                      </div>
                  </div>
              </template>
          </modal>
        </template>
        <template slot="title"> Sessions </template>
        <template slot="top-right-toolbar">
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

import breadcrumbs from '@/Shared/Breadcrumbs'
import indexSessionTable from '@/components/IndexSessionTable'
import layout from '@/Shared/Layout'
import searchParams from '@/SearchParams'
import urlParser from '@/UrlParser.js';
import modal from '@/components/Modal'
import invoiceSelect from '@/Shared/InvoiceSelect';
import sprintSelect from '@/Shared/SprintSelect';

export default {
    data() {
        return {
            showFilters: searchParams.get('show-filters') == 'true',
            urlParser: urlParser,
            selectedSprintId: null,
            sessionToSplit: null,
            splitTime: null,
        }
    },
    components: {
        breadcrumbs: breadcrumbs,
        indexSessionTable: indexSessionTable,
        layout: layout,
        modal: modal,
        invoiceSelect: invoiceSelect,
        sprintSelect: sprintSelect,
    },
    props: {
        days: Array,
        invoices: Array,
        sprints: Array,
        thirdPartyApplications: Array,
        page: Number,
        perPage: Number,
        total: Number,
        lastPage: Number,
    },
    methods: {
        toggleShowFilters() {
            events.$emit('toggle-show-filters');
        },
        startSession() {
            this.$inertia.post(route('session.start'));
        },
        linkSelectedSessionsToInvoice() {
            events.$emit('sessions.linked-to-invoice');

            this.$inertia.patch(route('invoice.update', this.selectedInvoiceId), {
                sessions: this.selectedSessionIds,
                redirectToSessionsScreen: true,
            });
        },
        selectInvoice(invoiceId) {
            this.selectedInvoiceId = invoiceId;
        },
        linkSelectedSessionsToSprint() {
            events.$emit('sessions.linked-to-sprint');

            this.$inertia.patch(route('sessions.update'), {
                sessions: this.selectedSessionIds.reduce((sessions, sessionId) => {
                    sessions[sessionId] = { sprint_id: this.selectedSprintId }
                    return sessions
                }, {})
            });
        },
        selectSprint(sprintId) {
            this.selectedSprintId = sprintId;
        },
        onChangeSelectedSessionIds(newValue) {
          this.selectedSessionIds = newValue
        },
        openSplitSessionModal(session) {
            this.sessionToSplit = session;
            this.splitTime = this.calculateDefaultSplitTime(session);
            events.$emit('session.split');
        },
        calculateDefaultSplitTime(session) {
            if (!session.started_at || !session.ended_at) {
                return null;
            }
            
            const startTime = new Date(session.started_at);
            const endTime = new Date(session.ended_at);
            const midPoint = new Date((startTime.getTime() + endTime.getTime()) / 2);
            
            // Format for datetime-local input
            const year = midPoint.getFullYear();
            const month = String(midPoint.getMonth() + 1).padStart(2, '0');
            const day = String(midPoint.getDate()).padStart(2, '0');
            const hours = String(midPoint.getHours()).padStart(2, '0');
            const minutes = String(midPoint.getMinutes()).padStart(2, '0');
            
            return `${year}-${month}-${day}T${hours}:${minutes}`;
        },
        splitSession() {
            if (!this.sessionToSplit || !this.splitTime) {
                return;
            }

            events.$emit('session.split-cancelled');

            this.$inertia.post(route('session.split', this.sessionToSplit.id), {
                split_time: this.splitTime
            });
        }
    },
    created() {
        events.$on('toggle-show-filters', (perPage) => {
            this.$inertia.visit(this.urlParser.current({
                showFilters: !this.showFilters,
            }), {
                replace: true,
                preserveScroll: true,
            });
        });
    }
}

</script>
