<template>
    <nav class="font-sans w-full pb-4">
        <ol class="list-reset flex text-gray-600">
            <li class="" v-for="(breadcrumb, index) in breadcrumbs">
                <inertia-link
                    v-if="!isLast(index) && breadcrumb.url"
                    class="text-gray-500 text-light no-underline"
                    :href="breadcrumb.url">
                    {{ breadcrumb.title }}
                </inertia-link>
                <span class="mx-4 text-gray-400" v-if="!isLast(index)"> > </span>
                <span v-else> {{ breadcrumb.title }} </span>
            </li>
        </ol>
    </nav>
</template>

<script>

    export default {
        props: [
            'breadcrumbs',
        ],
        methods: {
            isLast(index) {
                return index + 1 == this.breadcrumbs.length;
            }
        }
    }

</script>
