<template>
    <div class="w-full">
        <header-nav v-if="!hideNavbar"></header-nav>
        <div class="flex flex-wrap">
            <div v-if="!hideSidebar" class="
                block
                z-10
                bg-white
                lg:max-w-3xs
                border-b
                -mb-16
                md:flex
                md:relative
                sm:flex
                sm:relative
                lg:-mb-0
                lg:bg-transparent
                lg:border-b-0
                lg:pt-0
                lg:border-0
                "
            >
                <sidebar></sidebar>
            </div>
            <div class="flex-1 container mx-auto px-4">
                <div id="content-wrapper" class="min-h-screen w-full lg:max-h-full lg:overflow-visible">
                    <main role="main" class="lg:sticky w-full py-24">
                        <div class="px-2 sm:px-0">
                            <slot name="breadcrumbs"></slot>
                            <messages></messages>
                            <div
                                class="flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3"
                                v-if="$slots.title || $slots['top-right-toolbar']"
                            >
                                <div class="flex-1" v-if="$slots.title">
                                    <h1 class="text-3xl font-normal"><slot name="title"></slot></h1>
                                </div>
                                <div class="btn-toolbar mb-2 mb-md-0 flex-1 flex justify-end items-center" v-if="$slots['top-right-toolbar']">
                                    <slot name="top-right-toolbar"></slot>
                                </div>
                            </div>
                        </div>
                        <slot></slot>
                    </main>
                </div>
            </div>
            <div class="lg:w-0 xxl:w-full xxl:max-w-2xs">
            </div>
        </div>
        <slot name="modals"></slot>
    </div>
</template>

<script>
    import HeaderNav from '@/Shared/HeaderNav.vue';
    import Messages from '@/Shared/Messages.vue';
    import Sidebar from '@/Shared/Sidebar.vue';

    export default {
        props: [
            'hideNavbar',
            'hideSidebar',
        ],
        components: {
            headerNav: HeaderNav,
            messages: Messages,
            sidebar: Sidebar,
        },
        computed: {
            hasTitle() {
                return this.$slots.title && this.$slots.title().length > 0;
            },
            hasToolbar() {
                return this.$slots['top-right-toolbar'] && this.$slots['top-right-toolbar']().length > 0;
            },
            hasTitleOrToolbar() {
                return this.hasTitle || this.hasToolbar;
            }
        }
    }
</script>
