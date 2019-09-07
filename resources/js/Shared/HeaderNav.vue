<template>
    <div class="bg-white fixed flex border-b border-grey-lighter pin-t pin-x z-100 h-16 items-center shadow">

        <nav class="flex justify-between flex-wrap bg-white pl-4 py-3 pr-0 h-16 max-w-5xl w-full mx-auto">
            <logo></logo>
            <div class="flex-1 items-left flex-no-shrink text-white m-1">
                <span class="text-2xl tracking-tight text-grey-darkest font-thin"> {{ $page.app.name }} </span>
            </div>
            <div class="flex-1 text-right p-2">
                <inertia-link :href="route('logout')" method="post" class="btn btn-link nav-link">Log out</inertia-link>
            </div>
        </nav>

    </div>
</template>

<script>
    import Logo from '@/Shared/Logo';

    export default {
        components: {
            logo: Logo,
        },
        methods: {
            logout() {
                this.$inertia.post(route('logout'));
            }
        }
    }
</script>

