import { router, usePage } from '@inertiajs/vue3'
import debounce from 'lodash/debounce'
import { computed, reactive, ref, watch } from 'vue'

/**
 * Server-driven table state for Inertia index pages.
 *
 * Expects each response to include a `table` prop shaped like:
 * { filters: { ... }, sort: string, direction: string, per_page: number }
 *
 * @param {object} options
 * @param {string} options.routeName Ziggy route name for router.get
 * @param {string} [options.tableKey='table'] Page prop key holding table state
 * @param {Record<string, string|number|''>} [options.filterDefaults] Initial / cleared filter values (e.g. { q: '', client_id: '' })
 */
export function useInertiaTable({ routeName, tableKey = 'table', filterDefaults = {} }) {
    const page = usePage()

    const table = computed(() => page.props[tableKey] ?? {})

    const filterInitial = { ...filterDefaults }
    const filters = reactive({ ...filterInitial })
    const sort = ref('name')
    const direction = ref('asc')
    const perPage = ref(15)

    watch(
        table,
        (t) => {
            if (!t || typeof t !== 'object') {
                return
            }
            Object.assign(filters, t.filters ?? {})
            sort.value = t.sort ?? 'name'
            direction.value = t.direction ?? 'asc'
            perPage.value = t.per_page ?? 15
        },
        { immediate: true, deep: true },
    )

    const buildParams = (extra = {}) => {
        const params = {
            ...filters,
            sort: sort.value,
            direction: direction.value,
            per_page: perPage.value,
            ...extra,
        }

        return Object.fromEntries(
            Object.entries(params).filter(([, v]) => v !== undefined && v !== null && v !== ''),
        )
    }

    const visit = (extra = {}) => {
        router.get(window.route(routeName), buildParams(extra), {
            preserveState: true,
            preserveScroll: true,
            replace: true,
        })
    }

    const debouncedVisit = debounce(() => visit({ page: 1 }), 300)

    const onSearchInput = () => {
        debouncedVisit()
    }

    const applyFilters = () => {
        debouncedVisit.cancel()
        visit({ page: 1 })
    }

    const clearFilters = () => {
        debouncedVisit.cancel()
        Object.keys(filters).forEach((key) => {
            const initial = filterInitial[key]
            filters[key] = initial !== undefined && initial !== null ? initial : ''
        })
        sort.value = 'name'
        direction.value = 'asc'
        perPage.value = 15
        visit({ page: 1 })
    }

    const toggleSort = (column) => {
        debouncedVisit.cancel()
        if (sort.value !== column) {
            sort.value = column
            direction.value = 'asc'
        } else {
            direction.value = direction.value === 'asc' ? 'desc' : 'asc'
        }
        visit({ page: 1 })
    }

    const setPerPage = (value) => {
        debouncedVisit.cancel()
        perPage.value = Number(value)
        visit({ page: 1 })
    }

    const goToPage = (url) => {
        if (!url) {
            return
        }
        router.visit(url, { preserveState: true, preserveScroll: true })
    }

    const hasActiveFilters = computed(() => {
        return Object.entries(filters).some(([, v]) => v !== undefined && v !== null && String(v).trim() !== '')
    })

    return {
        filters,
        sort,
        direction,
        perPage,
        hasActiveFilters,
        visit,
        onSearchInput,
        applyFilters,
        clearFilters,
        toggleSort,
        setPerPage,
        goToPage,
    }
}
