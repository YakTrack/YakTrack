<template>
    <nav class="font-sans w-full pb-4 sm:pb-6">
        <ol class="list-reset flex text-gray-600 dark:text-gray-300">
            <li class="" v-for="(breadcrumb, index) in breadcrumbs">
                <Link
                    v-if="!isLast(index) && breadcrumb.url"
                    class="text-gray-500 dark:text-gray-400 text-light no-underline"
                    :href="breadcrumb.url">
                    {{ breadcrumb.title }}
                </Link>
                <span class="mx-4 text-gray-400 dark:text-gray-500" v-if="!isLast(index)"> > </span>
                <span v-else> {{ breadcrumb.title }} </span>
            </li>
        </ol>
    </nav>
</template>

<script>
    import { Link } from '@inertiajs/vue3';

    export default {
        components: {
            Link,
        },
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
