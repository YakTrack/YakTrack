<template>
    <div class="w-full">
        <header-nav></header-nav>
        <div class="flex flex-wrap">
            <div class="block z-90 bg-white w-full lg:max-w-3xs border-b -mb-16 lg:-mb-0 lg:static lg:bg-transparent lg:border-b-0 lg:pt-0 lg:border-0 sm:flex md:flex md:relative sm:relative">
                <sidebar></sidebar>
            </div>
            <div class="flex-1 container mx-auto px-8">
                <div id="content-wrapper" class="min-h-screen w-full lg:static lg:max-h-full lg:overflow-visible">
                    <main role="main" class="lg:sticky w-full py-24">
                        <slot name="breadcrumbs"></slot>
                        <messages></messages>
                        <div
                            class="flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3"
                            v-if="hasTitleOrToolbar"
                        >
                            <div class="flex-1">
                                <h1 class="text-3xl font-normal"><slot name="title"></slot></h1>
                            </div>
                            <div class="btn-toolbar mb-2 mb-md-0 flex-1 text-right">
                                <slot name="top-right-toolbar"></slot>
                            </div>
                        </div>
                        <slot></slot>
                    </main>
                </div>
            </div>
            <div class="lg:w-0 xxl:w-full xxl:max-w-2xs">
            </div>
        </div>
    </div>
</template>

<script>
    import HeaderNav from '@/Shared/HeaderNav';
    import Messages from '@/Shared/Messages';
    import Sidebar from '@/Shared/Sidebar';

    export default {
        components: {
            headerNav: HeaderNav,
            messages: Messages,
            sidebar: Sidebar,
        },
        computed: {
            hasTitleOrToolbar() {
                return !!(this.$slots['footer'] || this.$slots['top-right-toolbar']);
            }
        }
    }
</script>