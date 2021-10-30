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
            :page="page"
            :per-page="perPage"
            :total="total"
            :last-page="lastPage"
            :show-filters="showFilters"
            :on-change-selected-session-ids="onChangeSelectedSessionIds"
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

export default {
    data() {
        return {
            showFilters: searchParams.get('show-filters') == 'true',
            urlParser: urlParser,
        }
    },
    components: {
        breadcrumbs: breadcrumbs,
        indexSessionTable: indexSessionTable,
        layout: layout,
        modal: modal,
        invoiceSelect: invoiceSelect,
    },
    props: {
        days: Array,
        invoices: Array,
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
        onChangeSelectedSessionIds(newValue) {
          this.selectedSessionIds = newValue
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
