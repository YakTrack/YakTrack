<template>
  <modal
    :is-open="isOpen"
    title="Edit Session"
    max-width="2xl"
    scroll-behavior="body"
    :show-default-footer="false"
    :confirm-loading="isSubmitting"
    @close="handleClose"
  >
    <div v-if="isLoading" class="flex justify-center py-12">
      <i class="fa fa-spinner fa-spin text-2xl text-gray-400" aria-hidden="true"></i>
    </div>

    <form v-else-if="form" class="space-y-4" @submit.prevent="handleSubmit">
      <div class="form-group">
        <label for="edit-session-started-at">Started At</label>
        <input
          id="edit-session-started-at"
          v-model="form.started_at"
          type="text"
          name="started_at"
          class="form-control"
          placeholder="YYYY-MM-DD HH:MM:SS"
        >
      </div>

      <div class="form-group">
        <label for="edit-session-ended-at">Ended At</label>
        <input
          id="edit-session-ended-at"
          v-model="form.ended_at"
          type="text"
          name="ended_at"
          class="form-control"
          placeholder="YYYY-MM-DD HH:MM:SS"
        >
      </div>

      <div class="form-group">
        <label for="edit-session-task">Task</label>
        <task-select
          :tasks="tasks"
          :task="form.task_id"
          compact
          use-teleport
          :on-change="selectTask"
        />
      </div>

      <div class="form-group">
        <label for="edit-session-sprint">Sprint</label>
        <sprint-select
          :sprints="filteredSprints"
          :sprint="form.sprint_id"
          compact
          use-teleport
          :on-change="selectSprint"
        />
      </div>

      <div class="form-group">
        <label for="edit-session-invoice">Invoice</label>
        <invoice-select
          :invoices="invoices"
          :invoice="form.invoice_id"
          :on-change="selectInvoice"
        />
      </div>

      <div class="form-group">
        <label for="edit-session-category">Session Category</label>
        <session-category-select
          :session-categories="sessionCategories"
          v-model="form.session_category_id"
        />
      </div>

      <div class="form-group">
        <label for="edit-session-comment">Comment</label>
        <input
          id="edit-session-comment"
          v-model="form.comment"
          type="text"
          name="comment"
          class="form-control"
          placeholder=""
        >
      </div>

      <div class="form-group">
        <label for="edit-session-billable">Is Billable</label>
        <input
          id="edit-session-billable"
          v-model="form.is_billable"
          type="checkbox"
          class="form-checkbox"
          name="is_billable"
        >
      </div>
    </form>

    <template #footer>
      <div class="flex justify-end gap-3">
        <button
          type="button"
          class="rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-blue-500/20 disabled:cursor-not-allowed disabled:opacity-50"
          :disabled="isSubmitting"
          @click="handleClose"
        >
          Cancel
        </button>
        <button
          type="button"
          class="inline-flex items-center rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
          :disabled="isSubmitting || isLoading || !form"
          @click="handleSubmit"
        >
          <i v-if="isSubmitting" class="fa fa-spinner fa-spin mr-2" aria-hidden="true"></i>
          Update
        </button>
      </div>
    </template>
  </modal>
</template>

<script setup>
import { computed, ref, watch } from 'vue'
import Modal from './Modal.vue'
import TaskSelect from '@/Shared/TaskSelect.vue'
import SprintSelect from '@/Shared/SprintSelect.vue'
import InvoiceSelect from '@/Shared/InvoiceSelect.vue'
import SessionCategorySelect from '@/Shared/SessionCategorySelect.vue'
import dateTime from '@/filters/DateTime'

const props = defineProps({
  isOpen: {
    type: Boolean,
    default: false,
  },
  session: {
    type: Object,
    default: null,
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

const isLoading = ref(false)
const isSubmitting = ref(false)
const form = ref(null)
const tasks = ref([])
const invoices = ref([])
const sprints = ref([])
const sessionCategories = ref([])

const filteredSprints = computed(() => {
  if (!form.value?.task_id) {
    return sprints.value
  }

  const selectedTask = tasks.value.find((task) => task.id == form.value.task_id)

  if (!selectedTask?.project) {
    return sprints.value
  }

  return sprints.value.filter((sprint) => sprintIncludesProject(sprint, selectedTask.project.id))
})

function sprintIncludesProject(sprint, projectId) {
  if (!sprint?.projects || !Array.isArray(sprint.projects)) {
    return false
  }

  return sprint.projects.some((project) => project.id == projectId)
}

function buildForm(session) {
  return {
    started_at: dateTime.toDateTimeString(new Date(session.localStartedAt)),
    ended_at: session.localEndedAt ? dateTime.toDateTimeString(new Date(session.localEndedAt)) : null,
    task_id: session.task_id,
    sprint_id: session.sprint_id,
    invoice_id: session.invoice_id,
    comment: session.comment,
    is_billable: Boolean(session.is_billable),
    session_category_id: session.session_category_id,
  }
}

async function loadFormData() {
  if (!props.session) {
    return
  }

  isLoading.value = true
  form.value = null

  try {
    const response = await fetch(route('session.edit-form', props.session.id), {
      headers: {
        Accept: 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
      },
      credentials: 'same-origin',
    })

    if (!response.ok) {
      throw new Error('Failed to load session form')
    }

    const data = await response.json()

    tasks.value = data.tasks
    invoices.value = data.invoices
    sprints.value = data.sprints
    sessionCategories.value = data.sessionCategories
    form.value = buildForm(data.session)
  } finally {
    isLoading.value = false
  }
}

function selectSprint(sprintId) {
  form.value.sprint_id = sprintId
}

function selectInvoice(invoiceId) {
  form.value.invoice_id = invoiceId
}

function selectTask(taskId) {
  form.value.task_id = taskId

  if (taskId && form.value.sprint_id) {
    const selectedTask = tasks.value.find((task) => task.id == taskId)
    const selectedSprint = sprints.value.find((sprint) => sprint.id == form.value.sprint_id)

    if (selectedTask?.project && selectedSprint && !sprintIncludesProject(selectedSprint, selectedTask.project.id)) {
      form.value.sprint_id = null
    }
  }
}

function handleClose() {
  if (isSubmitting.value) {
    return
  }

  props.onClose()
  emit('close')
}

function handleSubmit() {
  if (!form.value || isSubmitting.value) {
    return
  }

  isSubmitting.value = true
  props.onSubmit({ ...form.value })
  emit('submit', { ...form.value })
}

function resetSubmitting() {
  isSubmitting.value = false
}

watch(() => props.isOpen, (isOpen) => {
  if (isOpen) {
    loadFormData()
  } else {
    form.value = null
    resetSubmitting()
  }
})

watch(() => props.session, () => {
  if (props.isOpen) {
    loadFormData()
  }
})

defineExpose({
  resetSubmitting,
})
</script>
