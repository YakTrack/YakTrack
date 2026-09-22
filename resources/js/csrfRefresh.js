import { ref } from 'vue'

// Poll the server on an interval to keep the user's CSRF token fresh so that
// long-lived pages don't hit 419 (session expired) errors on their next
// request. The session lifetime is 120 minutes, so a 15 minute interval keeps
// it comfortably alive while the app stays open.
const REFRESH_INTERVAL = 15 * 60 * 1000

const csrfMeta = () =>
    typeof document === 'undefined'
        ? null
        : document.querySelector('meta[name="csrf-token"]')

// Reactive source of truth for Vue components that bind the token (e.g. hidden
// `_token` inputs in native forms). Kept in sync with the meta tag below.
export const csrfToken = ref(csrfMeta()?.getAttribute('content') ?? '')

export const updateCsrfToken = (token) => {
    if (!token) {
        return
    }

    csrfToken.value = token
    csrfMeta()?.setAttribute('content', token)
}

export const refreshCsrfToken = async () => {
    try {
        const response = await fetch(window.route('csrf-token'), {
            headers: {
                Accept: 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
        })

        if (!response.ok) {
            return
        }

        const { token } = await response.json()

        updateCsrfToken(token)
    } catch {
        // Ignore transient network/auth errors; the next tick will try again.
    }
}

export const startCsrfRefresh = () => {
    window.setInterval(refreshCsrfToken, REFRESH_INTERVAL)

    // Background tabs throttle timers, so also refresh as soon as the user
    // returns to the tab to catch any token that went stale while hidden.
    document.addEventListener('visibilitychange', () => {
        if (document.visibilityState === 'visible') {
            refreshCsrfToken()
        }
    })
}
