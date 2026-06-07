import dayjs from 'dayjs'

export const MIN_SEGMENT_MS = 60 * 1000

export const SEGMENT_STYLES = [
    { bg: 'bg-indigo-500', light: 'bg-indigo-50', border: 'border-indigo-300', ring: 'ring-indigo-400', text: 'text-indigo-700', handle: 'border-indigo-500', muted: 'text-indigo-600/70' },
    { bg: 'bg-teal-500', light: 'bg-teal-50', border: 'border-teal-300', ring: 'ring-teal-400', text: 'text-teal-700', handle: 'border-teal-500', muted: 'text-teal-600/70' },
    { bg: 'bg-violet-500', light: 'bg-violet-50', border: 'border-violet-300', ring: 'ring-violet-400', text: 'text-violet-700', handle: 'border-violet-500', muted: 'text-violet-600/70' },
    { bg: 'bg-amber-500', light: 'bg-amber-50', border: 'border-amber-300', ring: 'ring-amber-400', text: 'text-amber-700', handle: 'border-amber-500', muted: 'text-amber-600/70' },
    { bg: 'bg-rose-500', light: 'bg-rose-50', border: 'border-rose-300', ring: 'ring-rose-400', text: 'text-rose-700', handle: 'border-rose-500', muted: 'text-rose-600/70' },
    { bg: 'bg-emerald-500', light: 'bg-emerald-50', border: 'border-emerald-300', ring: 'ring-emerald-400', text: 'text-emerald-700', handle: 'border-emerald-500', muted: 'text-emerald-600/70' },
    { bg: 'bg-sky-500', light: 'bg-sky-50', border: 'border-sky-300', ring: 'ring-sky-400', text: 'text-sky-700', handle: 'border-sky-500', muted: 'text-sky-600/70' },
]

export function parseSessionTime(value) {
    return dayjs(value).valueOf()
}

export function toInputFormat(ms) {
    return dayjs(ms).format('YYYY-MM-DDTHH:mm')
}

export function toApiFormat(ms) {
    return dayjs(ms).format('YYYY-MM-DD HH:mm:ss')
}

export function formatDuration(ms) {
    const totalSeconds = Math.max(0, Math.round(ms / 1000))
    const hours = Math.floor(totalSeconds / 3600)
    const minutes = Math.floor((totalSeconds % 3600) / 60)
    const seconds = totalSeconds % 60

    return [hours, minutes, seconds].map((part) => String(part).padStart(2, '0')).join(':')
}

export function formatTimeLabel(ms) {
    return dayjs(ms).format('h:mm A')
}

export function formatDateTimeLabel(ms) {
    return dayjs(ms).format('MMM D, h:mm A')
}

export function distributeEvenly(startMs, endMs, segmentCount) {
    const duration = endMs - startMs
    const cutPoints = []

    for (let index = 1; index < segmentCount; index++) {
        cutPoints.push(startMs + (duration * index) / segmentCount)
    }

    return cutPoints
}

export function constrainCutPoint(index, value, cutPoints, startMs, endMs) {
    const min = index === 0
        ? startMs + MIN_SEGMENT_MS
        : cutPoints[index - 1] + MIN_SEGMENT_MS
    const max = index === cutPoints.length - 1
        ? endMs - MIN_SEGMENT_MS
        : cutPoints[index + 1] - MIN_SEGMENT_MS

    return Math.max(min, Math.min(max, value))
}

export function buildSegments(cutPoints, startMs, endMs, assignments = []) {
    const boundaries = [startMs, ...cutPoints, endMs]
    const total = endMs - startMs

    return boundaries.slice(0, -1).map((start, index) => {
        const end = boundaries[index + 1]
        const duration = end - start

        return {
            startMs: start,
            endMs: end,
            ratio: total > 0 ? (duration / total) * 100 : 0,
            sprint_id: assignments[index]?.sprint_id ?? null,
            task_id: assignments[index]?.task_id ?? null,
        }
    })
}

export function positionToTime(clientX, rect, startMs, endMs) {
    const ratio = Math.max(0, Math.min(1, (clientX - rect.left) / rect.width))

    return startMs + ratio * (endMs - startMs)
}

export function applySegmentRatio(segmentIndex, newRatio, cutPoints, startMs, endMs, segmentCount) {
    const total = endMs - startMs
    const clampedRatio = Math.max(0, Math.min(100, Number(newRatio) || 0))
    const newDuration = (clampedRatio / 100) * total
    const nextCutPoints = [...cutPoints]
    const segmentStart = segmentIndex === 0 ? startMs : cutPoints[segmentIndex - 1]

    if (segmentIndex < segmentCount - 1) {
        nextCutPoints[segmentIndex] = constrainCutPoint(
            segmentIndex,
            segmentStart + newDuration,
            nextCutPoints,
            startMs,
            endMs,
        )
    } else {
        const cutIndex = segmentCount - 2
        nextCutPoints[cutIndex] = constrainCutPoint(
            cutIndex,
            endMs - newDuration,
            nextCutPoints,
            startMs,
            endMs,
        )
    }

    return nextCutPoints
}

export function applyCutPointTime(cutIndex, inputValue, cutPoints, startMs, endMs) {
    const nextCutPoints = [...cutPoints]
    const parsed = parseSessionTime(inputValue)

    if (Number.isNaN(parsed)) {
        return nextCutPoints
    }

    nextCutPoints[cutIndex] = constrainCutPoint(cutIndex, parsed, nextCutPoints, startMs, endMs)

    return nextCutPoints
}

export function segmentStyle(index) {
    return SEGMENT_STYLES[index % SEGMENT_STYLES.length]
}
