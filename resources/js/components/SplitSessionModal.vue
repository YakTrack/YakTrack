<template>
  <modal
    :is-open="isOpen"
    title="Split Session"
    description="Divide this session into multiple parts. Drag the handles, edit times or ratios, and optionally assign a sprint and task to each part."
    max-width="4xl"
    :show-default-footer="false"
    :close-on-backdrop="!isDragging"
    :close-on-escape="!isDragging"
    @close="handleClose"
  >
    <div v-if="session" class="space-y-6">
      <div
        v-if="hasTruncatedPendingTasks"
        class="flex items-start gap-3 rounded-xl border border-amber-200 bg-amber-50 p-4 text-amber-800"
      >
        <svg class="mt-0.5 h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z" />
        </svg>
        <div class="text-sm">
          <p class="font-semibold">Not all linked tasks fit in this split.</p>
          <p class="mt-1">
            Only the first {{ maxSegmentCount }} of {{ pendingTaskCount }} linked tasks fit here.
            The remaining {{ droppedPendingTaskCount }} stay linked to the session and can be split out later.
          </p>
        </div>
      </div>

      <div class="rounded-xl border border-slate-200 bg-gradient-to-br from-slate-50 to-white p-4">
        <div class="flex flex-wrap items-start justify-between gap-4">
          <div>
            <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Original session</p>
            <p v-if="session.task_name" class="mt-1 text-sm font-medium text-slate-900">{{ session.task_name }}</p>
            <p class="mt-1 text-sm text-slate-600">{{ formatDateTimeLabel(sessionStartMs) }} → {{ formatDateTimeLabel(sessionEndMs) }}</p>
          </div>
          <div class="rounded-lg bg-white px-3 py-2 text-right shadow-sm ring-1 ring-slate-200">
            <p class="text-xs font-medium uppercase tracking-wide text-slate-500">Total duration</p>
            <p class="font-mono text-lg font-semibold text-slate-900">{{ formatDuration(totalDurationMs) }}</p>
          </div>
        </div>
      </div>

      <div v-if="!initialisedFromPendingTasks" class="flex flex-wrap items-center justify-between gap-3">
        <div>
          <p class="text-sm font-medium text-slate-900">Number of parts</p>
          <p class="text-xs text-slate-500">Each part must be at least one minute long.</p>
        </div>
        <div class="inline-flex items-center rounded-xl border border-slate-200 bg-white p-1 shadow-sm">
          <button
            type="button"
            class="flex h-9 w-9 items-center justify-center rounded-lg text-slate-600 transition hover:bg-slate-100 disabled:cursor-not-allowed disabled:opacity-40"
            :disabled="segmentCount <= 2"
            @click="decreaseSegmentCount"
          >
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
            </svg>
          </button>
          <span class="min-w-[2.5rem] text-center font-mono text-sm font-semibold text-slate-900">{{ segmentCount }}</span>
          <button
            type="button"
            class="flex h-9 w-9 items-center justify-center rounded-lg text-slate-600 transition hover:bg-slate-100 disabled:cursor-not-allowed disabled:opacity-40"
            :disabled="segmentCount >= maxSegmentCount"
            @click="increaseSegmentCount"
          >
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
          </button>
        </div>
      </div>

      <div class="space-y-3">
        <div class="flex items-center justify-between text-xs font-medium uppercase tracking-wide text-slate-500">
          <span>Timeline</span>
          <span>Drag handles to adjust cut points</span>
        </div>

        <div
          ref="timelineRef"
          class="relative h-16 select-none overflow-hidden rounded-2xl bg-slate-100 shadow-inner ring-1 ring-slate-200"
        >
          <div
            v-for="(segment, index) in segments"
            :key="`segment-block-${index}`"
            class="absolute inset-y-0 transition-[left,width] duration-150 ease-out"
            :class="[
              segmentStyle(index).light,
              segmentStyle(index).border,
              index === 0 ? 'rounded-l-2xl' : '',
              index === segments.length - 1 ? 'rounded-r-2xl border-r-0' : 'border-r',
            ]"
            :style="segmentBlockStyle(segment)"
          >
            <div class="flex h-full items-center justify-center px-2">
              <span class="truncate text-xs font-semibold" :class="segmentStyle(index).text">
                {{ segment.ratio.toFixed(1) }}%
              </span>
            </div>
          </div>

          <button
            v-for="(cutPoint, index) in cutPoints"
            :key="`cut-point-${index}`"
            type="button"
            class="absolute top-1/2 z-10 h-10 w-4 -translate-x-1/2 -translate-y-1/2 cursor-ew-resize rounded-full border-2 bg-white shadow-lg transition hover:scale-110 focus:outline-none focus:ring-2 focus:ring-offset-2"
            :class="[
              segmentStyle(index).handle,
              activeHandleIndex === index ? segmentStyle(index).ring : '',
              isDragging && activeHandleIndex === index ? 'scale-110' : '',
            ]"
            :style="{ left: `${cutPointPosition(cutPoint)}%` }"
            :aria-label="`Adjust cut point ${index + 1}`"
            @mousedown.prevent="startDragging(index, $event)"
            @touchstart.prevent="startDragging(index, $event)"
          >
            <span class="sr-only">Cut point {{ index + 1 }}</span>
          </button>
        </div>

        <div class="flex justify-between text-xs text-slate-500">
          <span>{{ formatTimeLabel(sessionStartMs) }}</span>
          <span>{{ formatTimeLabel(sessionEndMs) }}</span>
        </div>
      </div>

      <div class="space-y-3">
        <p class="text-sm font-medium text-slate-900">Session parts</p>

        <div
          v-for="(segment, index) in segments"
          :key="`segment-card-${index}`"
          class="segment-card overflow-hidden rounded-2xl border bg-white shadow-sm transition"
          :class="segmentStyle(index).border"
        >
          <div
            class="flex items-center gap-3 rounded-t-2xl px-4 py-3"
            :class="segmentStyle(index).light"
          >
            <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full text-sm font-bold text-white" :class="segmentStyle(index).bg">
              {{ index + 1 }}
            </div>
            <div class="min-w-0 flex-1">
              <p class="text-sm font-semibold" :class="segmentStyle(index).text">Part {{ index + 1 }}</p>
              <p class="font-mono text-xs" :class="segmentStyle(index).muted">{{ formatDuration(segment.endMs - segment.startMs) }}</p>
            </div>
            <div class="text-right text-xs" :class="segmentStyle(index).muted">
              <p>{{ formatTimeLabel(segment.startMs) }}</p>
              <p>{{ formatTimeLabel(segment.endMs) }}</p>
            </div>
          </div>

          <div class="grid gap-4 border-t p-4 md:grid-cols-2" :class="segmentStyle(index).border">
            <div class="flex min-w-0 flex-col gap-4">
              <div>
                <label class="mb-1.5 block text-xs font-medium uppercase tracking-wide text-slate-500">Ratio</label>
                <div class="relative">
                  <input
                    :value="segment.ratio.toFixed(1)"
                    type="number"
                    min="0"
                    max="100"
                    step="0.1"
                    class="split-field w-full pr-8"
                    @change="updateSegmentRatio(index, $event.target.value)"
                  />
                  <span class="pointer-events-none absolute inset-y-0 right-3 flex items-center text-sm text-slate-400">%</span>
                </div>
              </div>

              <div v-if="index < segmentCount - 1">
                <label class="mb-1.5 block text-xs font-medium uppercase tracking-wide text-slate-500">Cut at</label>
                <input
                  :value="toInputFormat(cutPoints[index])"
                  type="datetime-local"
                  class="split-field w-full"
                  :min="minCutInput(index)"
                  :max="maxCutInput(index)"
                  @change="updateCutPointTime(index, $event.target.value)"
                />
              </div>
            </div>

            <div class="flex min-w-0 flex-col gap-4">
              <div>
                <label class="mb-1.5 block text-xs font-medium uppercase tracking-wide text-slate-500">Sprint</label>
                <sprint-select
                  :sprints="sprints"
                  :sprint="segment.sprint_id"
                  compact
                  use-teleport
                  :on-change="(value) => updateSegmentAssignment(index, 'sprint_id', value)"
                />
              </div>

              <div>
                <label class="mb-1.5 block text-xs font-medium uppercase tracking-wide text-slate-500">Task</label>
                <task-select
                  :tasks="tasks"
                  :task="segment.task_id"
                  compact
                  use-teleport
                  :on-change="(value) => updateSegmentAssignment(index, 'task_id', value)"
                />
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <template #footer>
      <div class="flex items-center justify-between gap-3">
        <p class="text-xs text-slate-500">
          {{ segmentCount }} sessions will be created from this split.
        </p>
        <div class="flex gap-3">
          <button
            type="button"
            class="rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-blue-500/20"
            @click="handleClose"
          >
            Cancel
          </button>
          <button
            type="button"
            class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
            :disabled="!canSubmit"
            @click="handleConfirm"
          >
            Split into {{ segmentCount }} sessions
          </button>
        </div>
      </div>
    </template>
  </modal>
</template>

<script setup>
import { computed, onUnmounted, ref, watch } from 'vue'
import Modal from './Modal.vue'
import SprintSelect from '@/Shared/SprintSelect.vue'
import TaskSelect from '@/Shared/TaskSelect.vue'
import {
  MIN_SEGMENT_MS,
  applyCutPointTime,
  applySegmentRatio,
  buildSegments,
  constrainCutPoint,
  distributeEvenly,
  formatDateTimeLabel,
  formatDuration,
  formatTimeLabel,
  parseSessionTime,
  positionToTime,
  segmentStyle,
  toApiFormat,
  toInputFormat,
} from '@/composables/useSessionSplit.js'

const props = defineProps({
  isOpen: {
    type: Boolean,
    default: false,
  },
  session: {
    type: Object,
    default: null,
  },
  sprints: {
    type: Array,
    default: () => [],
  },
  tasks: {
    type: Array,
    default: () => [],
  },
  pendingTasks: {
    type: Array,
    default: () => [],
  },
  onClose: {
    type: Function,
    default: () => {},
  },
  onSubmit: {
    type: Function,
    default: () => {},
  },
})

const emit = defineEmits(['close', 'submit'])

const segmentCount = ref(2)
const cutPoints = ref([])
const assignments = ref([])
const timelineRef = ref(null)
const isDragging = ref(false)
const activeHandleIndex = ref(null)
const initialisedFromPendingTasks = ref(false)

const sessionStartMs = computed(() => (props.session ? parseSessionTime(props.session.started_at) : 0))
const sessionEndMs = computed(() => (props.session ? parseSessionTime(props.session.ended_at) : 0))
const totalDurationMs = computed(() => Math.max(0, sessionEndMs.value - sessionStartMs.value))
const maxSegmentCount = computed(() => {
  if (totalDurationMs.value <= 0) {
    return 2
  }

  return Math.max(2, Math.min(12, Math.floor(totalDurationMs.value / MIN_SEGMENT_MS)))
})

const pendingTaskCount = computed(() => (Array.isArray(props.pendingTasks) ? props.pendingTasks.length : 0))

const droppedPendingTaskCount = computed(() => Math.max(0, pendingTaskCount.value - maxSegmentCount.value))

const hasTruncatedPendingTasks = computed(() => droppedPendingTaskCount.value > 0)

const segments = computed(() => buildSegments(
  cutPoints.value,
  sessionStartMs.value,
  sessionEndMs.value,
  assignments.value,
))

const canSubmit = computed(() => {
  if (!props.session || totalDurationMs.value <= 0) {
    return false
  }

  return segments.value.every((segment) => segment.endMs - segment.startMs >= MIN_SEGMENT_MS)
})

function defaultAssignments(count) {
  return Array.from({ length: count }, () => ({
    sprint_id: props.session?.sprint_id ?? null,
    task_id: props.session?.task_id ?? null,
  }))
}

function resetState() {
  if (!props.session) {
    return
  }

  if (Array.isArray(props.pendingTasks) && props.pendingTasks.length >= 2) {
    const count = Math.min(props.pendingTasks.length, maxSegmentCount.value)

    initialisedFromPendingTasks.value = true
    segmentCount.value = count
    cutPoints.value = distributeEvenly(sessionStartMs.value, sessionEndMs.value, count)
    assignments.value = props.pendingTasks.slice(0, count).map((pendingTask) => ({
      sprint_id: props.session?.sprint_id ?? null,
      task_id: pendingTask.task_id ?? null,
    }))
    stopDragging()

    return
  }

  initialisedFromPendingTasks.value = false
  segmentCount.value = 2
  cutPoints.value = distributeEvenly(sessionStartMs.value, sessionEndMs.value, 2)
  assignments.value = defaultAssignments(2)
  stopDragging()
}

function increaseSegmentCount() {
  // In the pending-task flow the part count is fixed by the linked tasks, so the
  // controls are hidden; guard here too so the per-task assignments can't be clobbered.
  if (initialisedFromPendingTasks.value || segmentCount.value >= maxSegmentCount.value) {
    return
  }

  segmentCount.value += 1
  cutPoints.value = distributeEvenly(sessionStartMs.value, sessionEndMs.value, segmentCount.value)
  assignments.value = defaultAssignments(segmentCount.value)
}

function decreaseSegmentCount() {
  if (initialisedFromPendingTasks.value || segmentCount.value <= 2) {
    return
  }

  segmentCount.value -= 1
  cutPoints.value = distributeEvenly(sessionStartMs.value, sessionEndMs.value, segmentCount.value)
  assignments.value = defaultAssignments(segmentCount.value)
}

function cutPointPosition(cutPoint) {
  if (totalDurationMs.value <= 0) {
    return 0
  }

  return ((cutPoint - sessionStartMs.value) / totalDurationMs.value) * 100
}

function segmentBlockStyle(segment) {
  const left = ((segment.startMs - sessionStartMs.value) / totalDurationMs.value) * 100
  const width = ((segment.endMs - segment.startMs) / totalDurationMs.value) * 100

  return {
    left: `${left}%`,
    width: `${width}%`,
  }
}

function minCutInput(index) {
  const minMs = index === 0
    ? sessionStartMs.value + MIN_SEGMENT_MS
    : cutPoints.value[index - 1] + MIN_SEGMENT_MS

  return toInputFormat(minMs)
}

function maxCutInput(index) {
  const maxMs = index === cutPoints.value.length - 1
    ? sessionEndMs.value - MIN_SEGMENT_MS
    : cutPoints.value[index + 1] - MIN_SEGMENT_MS

  return toInputFormat(maxMs)
}

function updateSegmentRatio(index, value) {
  cutPoints.value = applySegmentRatio(
    index,
    value,
    cutPoints.value,
    sessionStartMs.value,
    sessionEndMs.value,
    segmentCount.value,
  )
}

function updateCutPointTime(index, value) {
  cutPoints.value = applyCutPointTime(
    index,
    value,
    cutPoints.value,
    sessionStartMs.value,
    sessionEndMs.value,
  )
}

function updateSegmentAssignment(index, field, value) {
  assignments.value = assignments.value.map((assignment, assignmentIndex) => {
    if (assignmentIndex !== index) {
      return assignment
    }

    return {
      ...assignment,
      [field]: value,
    }
  })
}

function startDragging(index, event) {
  isDragging.value = true
  activeHandleIndex.value = index
  document.addEventListener('mousemove', handleDocumentMove)
  document.addEventListener('mouseup', stopDragging)
  document.addEventListener('touchmove', handleDocumentMove, { passive: false })
  document.addEventListener('touchend', stopDragging)
  moveHandle(index, event)
}

function handleDocumentMove(event) {
  if (!isDragging.value || activeHandleIndex.value === null) {
    return
  }

  if (event.cancelable) {
    event.preventDefault()
  }

  moveHandle(activeHandleIndex.value, event)
}

function moveHandle(index, event) {
  if (!timelineRef.value) {
    return
  }

  const clientX = event.touches ? event.touches[0].clientX : event.clientX
  const rect = timelineRef.value.getBoundingClientRect()
  const nextTime = positionToTime(clientX, rect, sessionStartMs.value, sessionEndMs.value)

  cutPoints.value = cutPoints.value.map((cutPoint, cutIndex) => {
    if (cutIndex !== index) {
      return cutPoint
    }

    return constrainCutPoint(index, nextTime, cutPoints.value, sessionStartMs.value, sessionEndMs.value)
  })
}

function stopDragging() {
  isDragging.value = false
  activeHandleIndex.value = null
  document.removeEventListener('mousemove', handleDocumentMove)
  document.removeEventListener('mouseup', stopDragging)
  document.removeEventListener('touchmove', handleDocumentMove)
  document.removeEventListener('touchend', stopDragging)
}

function handleClose() {
  stopDragging()
  props.onClose()
  emit('close')
}

function handleConfirm() {
  if (!props.session || !canSubmit.value) {
    return
  }

  const payload = segments.value.map((segment) => ({
    started_at: toApiFormat(segment.startMs),
    ended_at: toApiFormat(segment.endMs),
    sprint_id: segment.sprint_id,
    task_id: segment.task_id,
  }))

  const fromPendingTasks = initialisedFromPendingTasks.value

  props.onSubmit(props.session, payload, fromPendingTasks)
  emit('submit', props.session, payload, fromPendingTasks)
  handleClose()
}

watch(() => props.isOpen, (isOpen) => {
  if (isOpen) {
    resetState()
  }
})

watch(() => props.session, () => {
  if (props.isOpen) {
    resetState()
  }
})

onUnmounted(() => {
  stopDragging()
})
</script>

<style scoped>
.split-field {
  @apply h-12 rounded-lg border border-slate-200 bg-white px-3 text-sm shadow-sm;
  @apply focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20;
}
</style>
