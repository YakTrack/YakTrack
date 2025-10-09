<template>
  <modal
    :is-open="isOpen"
    title="Split Session"
    description="Enter the time when you want to split this session. The original session will end at this time, and a new session will start from this time."
    :show-default-footer="true"
    cancel-text="Cancel"
    confirm-text="Split Session"
    :close-on-backdrop="true"
    :close-on-escape="true"
    @close="handleClose"
    @confirm="handleConfirm"
  >
    <div class="space-y-4">
      <div>
        <label for="split_time" class="block text-sm font-medium text-gray-700 mb-2">
          Split Time
        </label>
        <input
          id="split_time"
          v-model="splitTime"
          type="datetime-local"
          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors duration-200"
          :min="session ? session.startedAtInputFormat : ''"
          :max="session ? session.endedAtInputFormat : ''"
          required
        />
        <p v-if="!splitTime" class="mt-1 text-sm text-red-600">
          Please select a split time.
        </p>
      </div>
      
      <div v-if="session" class="bg-gray-50 p-4 rounded-lg">
        <h4 class="text-sm font-medium text-gray-900 mb-2">Session Details</h4>
        <div class="text-sm text-gray-600 space-y-1">
          <div v-if="session.task_name">
            <span class="font-medium">Task:</span> {{ session.task_name }}
          </div>
          <div>
            <span class="font-medium">Duration:</span> {{ session.durationForHumans }}
          </div>
          <div>
            <span class="font-medium">Start:</span> {{ formatDateTime(session.started_at) }}
          </div>
          <div>
            <span class="font-medium">End:</span> {{ formatDateTime(session.ended_at) }}
          </div>
        </div>
      </div>
    </div>
  </modal>
</template>

<script setup>
import { computed, ref, watch } from 'vue'
import Modal from './Modal.vue'

// Props
const props = defineProps({
  isOpen: {
    type: Boolean,
    default: false
  },
  session: {
    type: Object,
    default: null
  },
  onClose: {
    type: Function,
    default: () => {}
  },
  onSubmit: {
    type: Function,
    default: () => {}
  }
})

// Emits
const emit = defineEmits(['close', 'submit'])

// Reactive state
const splitTime = ref(null)

// Computed
const isValidSplitTime = computed(() => {
  if (!props.session || !splitTime.value) return false
  
  const splitDateTime = new Date(splitTime.value)
  const startDateTime = new Date(props.session.started_at)
  const endDateTime = new Date(props.session.ended_at)
  
  return splitDateTime > startDateTime && splitDateTime < endDateTime
})

// Methods
const calculateDefaultSplitTime = (session) => {
  if (!session?.started_at || !session?.ended_at) {
    return null
  }
  
  const startTime = new Date(session.started_at)
  const endTime = new Date(session.ended_at)
  const midPoint = new Date((startTime.getTime() + endTime.getTime()) / 2)
  
  // Format for datetime-local input
  const year = midPoint.getFullYear()
  const month = String(midPoint.getMonth() + 1).padStart(2, '0')
  const day = String(midPoint.getDate()).padStart(2, '0')
  const hours = String(midPoint.getHours()).padStart(2, '0')
  const minutes = String(midPoint.getMinutes()).padStart(2, '0')
  
  return `${year}-${month}-${day}T${hours}:${minutes}`
}

const formatDateTime = (dateString) => {
  if (!dateString) return 'N/A'
  return new Date(dateString).toLocaleString()
}

const handleClose = () => {
  splitTime.value = null
  props.onClose()
  emit('close')
}

const handleConfirm = () => {
  if (!props.session || !splitTime.value || !isValidSplitTime.value) {
    return
  }
  
  props.onSubmit(props.session, splitTime.value)
  emit('submit', props.session, splitTime.value)
  handleClose()
}

// Watchers
watch(() => props.isOpen, (newValue) => {
  if (newValue && props.session) {
    splitTime.value = calculateDefaultSplitTime(props.session)
  }
})

watch(() => props.session, (newSession) => {
  if (props.isOpen && newSession) {
    splitTime.value = calculateDefaultSplitTime(newSession)
  }
})
</script>
