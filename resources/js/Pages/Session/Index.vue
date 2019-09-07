<template>
    <layout>
        <template slot="title"> Sessions </template>
        <template slot="top-right-toolbar">
            <button type="button" class="btn" :class="(filtersAreShown ? 'btn-blue' : 'btn-primary') + ' btn-sm mr-2'" @click="toggleShowFilters()">
                <i class="fa fa-filter"></i>
            </button>
            <inertia-link :href="route('session.create')" class="btn btn-primary btn-sm mr-2">
                <i class="fa fa-stopwatch"></i>
            </inertia-link>
            <inertia-link :href="route('session.start')" class="btn btn-green">
                <i class="fa fa-play pr-2"></i>
                Start Session
            </inertia-link>
        </template>

        <index-session-table
            :days="days"
            :third-party-applications="thirdPartyApplications"
            :invoices="invoices"
            :show-filters="showFilters"
            :page="page"
            :per-page="perPage"
            :total="total"
            :last-page="lastPage"
        ></index-session-table>

    </layout>
</template>

<script>

import IndexSessionTable from '@/components/IndexSessionTable'
import Layout from '@/Shared/Layout'

export default {
    components: {
        indexSessionTable: IndexSessionTable,
        layout: Layout,
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
    computed: {
        filtersAreShown() {
            return (new URLSearchParams(window.location.search)).has('show-filters');
        }
    },
    methods: {
        showFilters() {

        },
    }
}

</script>