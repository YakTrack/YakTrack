/**
 * Tracks debounced search requests so that a slow response for an earlier query
 * cannot overwrite the results of the most recent one.
 */
export const createLatestRequestTracker = () => {
    let latestRequestId = 0

    return {
        begin() {
            latestRequestId += 1

            return latestRequestId
        },
        isLatest(requestId) {
            return requestId === latestRequestId
        },
        invalidate() {
            latestRequestId += 1
        },
    }
}

/**
 * Whether to tell the user a completed search matched nothing. Errors and
 * in-flight searches take precedence over the empty state.
 */
export const shouldShowNoResults = ({ query, isSearching, hasSearched, resultCount, error }) =>
    query.trim().length >= 2 && !isSearching && hasSearched && resultCount === 0 && !error
