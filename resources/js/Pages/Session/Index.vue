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
        <template slot="title"> Sessions </template>
        <template slot="top-right-toolbar">
            <button type="button" class="btn" :class="(showFilters ? 'btn-blue' : 'btn-default') + ' mr-2'" @click="toggleShowFilters()">
                <i class="fa fa-filter"></i>
            </button>
            <button-link :href="route('session.create')" class="mr-2">
                <i class="fa fa-stopwatch"></i>
            </button-link>
            <button @click="startSession" class="btn btn-green">
                <i class="fa fa-play pr-2"></i>
                Start Session
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
        ></index-session-table>

    </layout>
</template>

<script>

import breadcrumbs from '@/Shared/Breadcrumbs'
import indexSessionTable from '@/components/IndexSessionTable'
import layout from '@/Shared/Layout'
import searchParams from '@/SearchParams'
import urlParser from '@/UrlParser.js';

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