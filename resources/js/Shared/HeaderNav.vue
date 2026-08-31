<template>
    <div class="bg-white fixed w-full flex border-b border-gray-300 top-0 inset-x-0 z-40 h-16 items-center shadow">
        <nav class="flex justify-between flex-wrap bg-white pl-5 py-3 pr-0 h-16 w-full mx-auto">
            <div class="flex items-left flex-no-shrink text-white m-1">
                <logo></logo>
                <span class="text-2xl tracking-tight text-gray-900 font-thin"> {{ $page.props.app.name }} </span>
            </div>
            <div class="flex items-center gap-x-3 mx-8">
                <focus-client-menu></focus-client-menu>
                <user-menu :logout-url="route('logout')"></user-menu>
            </div>
        </nav>
    </div>
</template>

<script>
    import Logo from '@/Shared/Logo.vue';
    import UserMenu from '@/Shared/UserMenu.vue';
    import FocusClientMenu from '@/Shared/FocusClientMenu.vue';

    export default {
        components: {
            logo: Logo,
            'user-menu': UserMenu,
            'focus-client-menu': FocusClientMenu,
        },
    }
</script>

