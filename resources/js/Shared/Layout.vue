<template>
    <div>
        <header-nav></header-nav>
        <div class="w-full max-w-5xl mx-auto px-8">
            <div class="lg:flex -mx-6">
                <sidebar></sidebar>
                <div id="content-wrapper" class="min-h-screen w-full lg:static lg:max-h-full lg:overflow-visible">
                    <main role="main" class="lg:sticky w-full py-24">
                        <slot name="breadcrumbs"></slot>
                        <messages></messages>
                        <div class="flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3">
                            <div class="flex-1">
                                <h1 class="h2 font-normal"><slot name="title"></slot></h1>
                            </div>
                            <div class="btn-toolbar mb-2 mb-md-0 flex-1 text-right">
                                <slot name="top-right-toolbar"></slot>
                            </div>
                        </div>
                        <slot></slot>
                    </main>
                </div>
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
    }
</script>