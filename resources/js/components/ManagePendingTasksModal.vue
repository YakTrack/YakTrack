<template>
  <modal
    :is-open="isOpen"
    title="Manage linked tasks"
    description="Link the tasks you worked on at the same time during this session. It can then be split into one session per linked task."
    max-width="lg"
    :show-default-footer="false"
    @close="handleClose"
  >
    <div class="space-y-5">
      <div>
        <p class="mb-2 text-xs font-medium uppercase tracking-wide text-slate-500">Linked tasks</p>

        <p v-if="isLoading" class="text-sm text-slate-500">
          <i class="fa fa-spinner fa-spin mr-2" aria-hidden="true"></i>Loading…
        </p>

        <template v-else-if="pendingTasks.length === 0">
          <p class="text-sm text-slate-500">
            No tasks linked yet. Add one below.
          </p>
          <p v-if="hasCurrentTask" class="mt-1 text-xs text-slate-500">
            This session’s current task is included automatically as a part when you split.
          </p>
        </template>

        <ul v-else class="space-y-2">
          <li
            v-for="pendingTask in pendingTasks"
            :key="pendingTask.task_id"
            class="flex items-center justify-between gap-3 rounded-lg border border-slate-200 bg-white px-3 py-2 shadow-sm"
          >
            <div class="min-w-0">
              <p class="truncate text-sm font-medium text-slate-900">{{ pendingTask.task_name }}</p>
              <p v-if="pendingTask.client_name || pendingTask.project_name" class="truncate text-xs text-slate-500">
                {{ [pendingTask.client_name, pendingTask.project_name].filter(Boolean).join(' · ') }}
              </p>
            </div>
            <button
              type="button"
              class="shrink-0 rounded-md p-1.5 text-slate-400 transition hover:bg-red-50 hover:text-red-600 disabled:cursor-not-allowed disabled:opacity-50"
              :disabled="isSaving"
              :aria-label="`Remove ${pendingTask.task_name}`"
              @click="removeTask(pendingTask.task_id)"
            >
              <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </li>
        </ul>
      </div>

      <div>
        <label class="mb-1.5 block text-xs font-medium uppercase tracking-wide text-slate-500">Add a task</label>
        <div class="flex items-end gap-2">
          <div class="min-w-0 flex-1">
            <task-select
              :key="pickerKey"
              :tasks="availableTasks"
              :task="null"
              compact
              use-teleport
              :on-change="onSelectTask"
            />
          </div>
          <button
            type="button"
            class="shrink-0 rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
            :disabled="!selectedTaskId || isSaving"
            @click="addSelectedTask"
          >
            Add
          </button>
        </div>

        <p v-if="errorMessage" class="mt-2 text-sm text-red-600">{{ errorMessage }}</p>
      </div>
    </div>

    <template #footer>
      <div class="flex items-center justify-between gap-3">
        <p class="text-xs text-slate-500">
          {{ pendingTasks.length }} task{{ pendingTasks.length === 1 ? '' : 's' }} linked.
        </p>
        <button
          type="button"
          class="rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-blue-500/20"
          @click="handleClose"
        >
          Done
        </button>
      </div>
    </template>
  </modal>
</template>

<script setup>
import { computed, ref, watch } from 'vue'
import Modal from './Modal.vue'
import TaskSelect from '@/Shared/TaskSelect.vue'

const props = defineProps({
  isOpen: {
    type: Boolean,
    default: false,
  },
  session: {
    type: Object,
    default: null,
  },
  tasks: {
    type: Array,
    default: () => [],
  },
  onClose: {
    type: Function,
    default: () => {},
  },
})

const emit = defineEmits(['close', 'changed'])

const pendingTasks = ref([])
const selectedTaskId = ref(null)
const isLoading = ref(false)
const isSaving = ref(false)
const pickerKey = ref(0)
const didChange = ref(false)
const errorMessage = ref(null)

// Monotonic token: only the most recent load may write state. Guards against a
// stale response (from a previously opened session) resolving after the modal has
// been reopened for a different session and overwriting its pending tasks.
let loadRequestToken = 0

async function extractErrorMessage(response, fallback) {
  try {
    const data = await response.json()

    if (data?.errors) {
      const firstField = Object.values(data.errors)[0]

      if (Array.isArray(firstField) && firstField.length > 0) {
        return firstField[0]
      }
    }

    if (data?.message) {
      return data.message
    }
  } catch (error) {
    // Response had no JSON body; fall through to the generic message.
  }

  return fallback
}

const availableTasks = computed(() => {
  const linkedIds = new Set(pendingTasks.value.map((pendingTask) => pendingTask.task_id))

  return props.tasks.filter((task) => !linkedIds.has(task.id))
})

const hasCurrentTask = computed(() => Boolean(props.session?.task_id))

const csrfToken = () => document.querySelector('meta[name="csrf-token"]')?.content ?? ''

function resetPicker() {
  selectedTaskId.value = null
  pickerKey.value += 1
}

function onSelectTask(value) {
  selectedTaskId.value = value
  errorMessage.value = null
}

async function loadPendingTasks() {
  if (!props.session) {
    return
  }

  const token = ++loadRequestToken
  const sessionId = props.session.id

  isLoading.value = true

  try {
    const response = await fetch(route('session.pending-tasks.index', sessionId), {
      headers: {
        Accept: 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
      },
      credentials: 'same-origin',
    })

    if (token !== loadRequestToken) {
      return
    }

    if (!response.ok) {
      const message = await extractErrorMessage(response, 'Could not load linked tasks. Please try again.')

      if (token !== loadRequestToken) {
        return
      }

      errorMessage.value = message

      return
    }

    const data = await response.json()

    if (token !== loadRequestToken) {
      return
    }

    pendingTasks.value = data.pending_tasks
  } catch (error) {
    if (token === loadRequestToken) {
      errorMessage.value = 'Could not load linked tasks. Please try again.'
    }
  } finally {
    if (token === loadRequestToken) {
      isLoading.value = false
    }
  }
}

async function addSelectedTask() {
  if (!props.session || !selectedTaskId.value || isSaving.value) {
    return
  }

  isSaving.value = true
  errorMessage.value = null

  try {
    const response = await fetch(route('session.pending-tasks.store', props.session.id), {
      method: 'POST',
      headers: {
        Accept: 'application/json',
        'Content-Type': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-TOKEN': csrfToken(),
      },
      credentials: 'same-origin',
      body: JSON.stringify({ task_id: selectedTaskId.value }),
    })

    if (!response.ok) {
      errorMessage.value = await extractErrorMessage(response, 'This task could not be linked.')

      return
    }

    const data = await response.json()
    pendingTasks.value = data.pending_tasks
    didChange.value = true
    resetPicker()
  } catch (error) {
    errorMessage.value = 'Something went wrong. Please try again.'
  } finally {
    isSaving.value = false
  }
}

async function removeTask(taskId) {
  if (!props.session || isSaving.value) {
    return
  }

  isSaving.value = true
  errorMessage.value = null

  try {
    const response = await fetch(route('session.pending-tasks.destroy', { session: props.session.id, task: taskId }), {
      method: 'DELETE',
      headers: {
        Accept: 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-TOKEN': csrfToken(),
      },
      credentials: 'same-origin',
    })

    if (!response.ok) {
      errorMessage.value = await extractErrorMessage(response, 'This task could not be removed.')

      return
    }

    const data = await response.json()
    pendingTasks.value = data.pending_tasks
    didChange.value = true
  } catch (error) {
    errorMessage.value = 'Something went wrong. Please try again.'
  } finally {
    isSaving.value = false
  }
}

function handleClose() {
  if (didChange.value) {
    emit('changed')
  }

  props.onClose()
  emit('close')
}

watch(() => props.isOpen, (isOpen) => {
  if (isOpen) {
    didChange.value = false
    errorMessage.value = null
    pendingTasks.value = []
    resetPicker()
    loadPendingTasks()
  }
})
</script>
