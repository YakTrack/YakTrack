import { expect, test } from 'vitest'
import { createLatestRequestTracker, shouldShowNoResults } from '../jiraIssueSearch.js'

test('only the most recently begun request is considered latest', () => {
    const tracker = createLatestRequestTracker()

    const first = tracker.begin()
    const second = tracker.begin()

    expect(tracker.isLatest(first)).toBe(false)
    expect(tracker.isLatest(second)).toBe(true)
})

test('invalidate marks every in-flight request as stale', () => {
    const tracker = createLatestRequestTracker()

    const request = tracker.begin()
    tracker.invalidate()

    expect(tracker.isLatest(request)).toBe(false)
})

test('a slow earlier response does not overwrite the latest results', async () => {
    const tracker = createLatestRequestTracker()
    let results = []

    const respond = (requestId, issues, delay) =>
        new Promise((resolve) => {
            setTimeout(() => {
                if (tracker.isLatest(requestId)) {
                    results = issues
                }
                resolve()
            }, delay)
        })

    const slow = respond(tracker.begin(), ['KEY-1'], 20)
    const fast = respond(tracker.begin(), ['KEY-12'], 5)

    await Promise.all([slow, fast])

    expect(results).toEqual(['KEY-12'])
})

const completedEmptySearch = {
    query: 'KEY-1',
    isSearching: false,
    hasSearched: true,
    resultCount: 0,
    error: '',
}

test('shows the empty state after a completed search with no matches', () => {
    expect(shouldShowNoResults(completedEmptySearch)).toBe(true)
})

test.each([
    ['the query is too short', { query: 'K' }],
    ['a search is still running', { isSearching: true }],
    ['nothing has been searched yet', { hasSearched: false }],
    ['there are results', { resultCount: 3 }],
    ['the search failed', { error: 'Could not search Jira issues.' }],
])('hides the empty state when %s', (_label, overrides) => {
    expect(shouldShowNoResults({ ...completedEmptySearch, ...overrides })).toBe(false)
})
