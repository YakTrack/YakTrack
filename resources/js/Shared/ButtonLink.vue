<template>
    <Link
        :href="href"
        :class="['btn', 'btn-default', '!inline-flex', 'items-center', !color || `btn-${color}`]"
        :method="method || 'get'"
        :as="(method && method !== 'get') ? 'button' : 'a'"
    >
        <slot></slot>
    </Link>
</template>

<script>
import { Link } from '@inertiajs/vue3'

export default {
    components: {
        Link,
    },
    props: [
        'href',
        'color',
        'method',
    ],
}
</script>