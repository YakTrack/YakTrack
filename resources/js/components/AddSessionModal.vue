<template>
  <modal
    :is-open="isOpen"
    :title="title"
    max-width="2xl"
    scroll-behavior="body"
    :show-default-footer="false"
    :confirm-loading="isSubmitting"
    @close="handleClose"
  >
    <form v-if="form" class="space-y-4" @submit.prevent="handleSubmit">
      <div class="form-group">
        <label for="add-session-started-at">Started At</label>
        <input
          id="add-session-started-at"
          v-model="form.started_at"
          type="text"
          name="started_at"
          class="form-control"
          placeholder="YYYY-MM-DD HH:MM:SS"
        >
      </div>

      <div class="form-group">
        <label for="add-session-ended-at">Ended At</label>
        <input
          id="add-session-ended-at"
          v-model="form.ended_at"
          type="text"
          name="ended_at"
          class="form-control"
          placeholder="YYYY-MM-DD HH:MM:SS"
        >
      </div>

      <div class="form-group">
        <label for="add-session-task">Task</label>
        <task-select
          :tasks="tasks"
          :task="form.task_id"
          compact
          use-teleport
          :on-change="selectTask"
        />
      </div>

      <div class="form-group">
        <label for="add-session-sprint">Sprint</label>
        <sprint-select
          :sprints="filteredSprints"
          :sprint="form.sprint_id"
          compact
          use-teleport
          :on-change="selectSprint"
        />
      </div>

      <div class="form-group">
        <label for="add-session-invoice">Invoice</label>
        <invoice-select
          :invoices="invoices"
          :invoice="form.invoice_id"
          :on-change="selectInvoice"
        />
      </div>

      <div class="form-group">
        <label for="add-session-category">Session Category</label>
        <session-category-select
          :session-categories="sessionCategories"
          v-model="form.session_category_id"
        />
      </div>

      <div class="form-group">
        <label for="add-session-comment">Comment</label>
        <input
          id="add-session-comment"
          v-model="form.comment"
          type="text"
          name="comment"
          class="form-control"
          placeholder=""
        >
      </div>

      <div class="form-group">
        <label for="add-session-billable">Is Billable</label>
        <input
          id="add-session-billable"
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
          :disabled="isSubmitting || !form"
          @click="handleSubmit"
        >
          <i v-if="isSubmitting" class="fa fa-spinner fa-spin mr-2" aria-hidden="true"></i>
          Add Session
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

const props = defineProps({
  isOpen: {
    type: Boolean,
    default: false,
  },
  title: {
    type: String,
    default: 'Add Session',
  },
  initialForm: {
    type: Object,
    default: null,
  },
  tasks: {
    type: Array,
    default: () => [],
  },
  sprints: {
    type: Array,
    default: () => [],
  },
  invoices: {
    type: Array,
    default: () => [],
  },
  sessionCategories: {
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

const isSubmitting = ref(false)
const form = ref(null)

const filteredSprints = computed(() => {
  if (!form.value?.task_id) {
    return props.sprints
  }

  const selectedTask = props.tasks.find((task) => task.id == form.value.task_id)

  if (!selectedTask?.project) {
    return props.sprints
  }

  return props.sprints.filter((sprint) => sprintIncludesProject(sprint, selectedTask.project.id))
})

function sprintIncludesProject(sprint, projectId) {
  if (!sprint?.projects || !Array.isArray(sprint.projects)) {
    return false
  }

  return sprint.projects.some((project) => project.id == projectId)
}

function buildForm(initial) {
  return {
    started_at: initial?.started_at ?? null,
    ended_at: initial?.ended_at ?? null,
    task_id: initial?.task_id ?? null,
    sprint_id: initial?.sprint_id ?? null,
    invoice_id: initial?.invoice_id ?? null,
    comment: initial?.comment ?? null,
    is_billable: Boolean(initial?.is_billable),
    session_category_id: initial?.session_category_id ?? null,
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
    const selectedTask = props.tasks.find((task) => task.id == taskId)
    const selectedSprint = props.sprints.find((sprint) => sprint.id == form.value.sprint_id)

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
    form.value = buildForm(props.initialForm)
    isSubmitting.value = false
  } else {
    form.value = null
    isSubmitting.value = false
  }
})

defineExpose({
  resetSubmitting,
})
</script>
