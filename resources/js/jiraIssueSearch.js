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
