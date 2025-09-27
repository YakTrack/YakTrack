<template>
    <div class="inline-block">
        <Link
            :href="href"
            :class="['btn', 'btn-default', !color || `btn-${color}`]"
            :method="method || 'get'"
            :as="(method && method !== 'get') ? 'button' : 'a'"
        >
            <slot></slot>
        </Link>
    </div>
</template>

<script>
import { Link } from '@inertiajs/vue2'

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