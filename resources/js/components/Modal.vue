<template>
  <Teleport to="body">
    <Transition
      name="modal"
      enter-active-class="transition-opacity duration-300 ease-out"
      enter-from-class="opacity-0"
      enter-to-class="opacity-100"
      leave-active-class="transition-opacity duration-200 ease-in"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div
        v-if="isOpen"
        class="fixed inset-0 z-50 flex items-center justify-center"
        @click.self="handleBackdropClick"
      >
        <!-- Backdrop -->
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" />
        
        <!-- Modal Container -->
        <Transition
          name="modal-content"
          enter-active-class="transition-all duration-300 ease-out"
          enter-from-class="opacity-0 scale-95 translate-y-4"
          enter-to-class="opacity-100 scale-100 translate-y-0"
          leave-active-class="transition-all duration-200 ease-in"
          leave-from-class="opacity-100 scale-100 translate-y-0"
          leave-to-class="opacity-0 scale-95 translate-y-4"
        >
          <div
            v-if="isOpen"
            class="relative bg-white rounded-xl shadow-2xl max-w-md w-full mx-4 max-h-[90vh] overflow-hidden"
            role="dialog"
            aria-modal="true"
            :aria-labelledby="titleId"
            :aria-describedby="descriptionId"
          >
            <!-- Header -->
            <div v-if="$slots.header || title" class="px-6 py-4 border-b border-gray-200">
              <div class="flex items-center justify-between">
                <div>
                  <h3 v-if="title" :id="titleId" class="text-lg font-semibold text-gray-900">
                    {{ title }}
                  </h3>
                  <slot name="header" />
                </div>
                <button
                  v-if="showCloseButton"
                  @click="close"
                  class="text-gray-400 hover:text-gray-600 transition-colors duration-200 p-1 rounded-lg hover:bg-gray-100"
                  aria-label="Close modal"
                >
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                  </svg>
                </button>
              </div>
            </div>

            <!-- Body -->
            <div class="px-6 py-4 overflow-y-auto max-h-[60vh]">
              <div v-if="description" :id="descriptionId" class="text-sm text-gray-600 mb-4">
                {{ description }}
              </div>
              <slot />
            </div>

            <!-- Footer -->
            <div v-if="$slots.footer || showDefaultFooter" class="px-6 py-4 border-t border-gray-200 bg-gray-50">
              <div class="flex justify-end space-x-3">
                <slot name="footer">
                  <button
                    v-if="showDefaultFooter"
                    @click="close"
                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200"
                  >
                    {{ cancelText }}
                  </button>
                  <button
                    v-if="showDefaultFooter"
                    @click="confirm"
                    class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200"
                  >
                    {{ confirmText }}
                  </button>
                </slot>
              </div>
            </div>
          </div>
        </Transition>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup>
import { computed, nextTick, onMounted, onUnmounted, ref, watch } from 'vue'

// Props
const props = defineProps({
  isOpen: {
    type: Boolean,
    default: false
  },
  title: {
    type: String,
    default: ''
  },
  description: {
    type: String,
    default: ''
  },
  showCloseButton: {
    type: Boolean,
    default: true
  },
  showDefaultFooter: {
    type: Boolean,
    default: false
  },
  cancelText: {
    type: String,
    default: 'Cancel'
  },
  confirmText: {
    type: String,
    default: 'Confirm'
  },
  closeOnBackdrop: {
    type: Boolean,
    default: true
  },
  closeOnEscape: {
    type: Boolean,
    default: true
  }
})

// Emits
const emit = defineEmits(['close', 'confirm'])

// Reactive state
const isOpen = ref(props.isOpen)

// Computed
const titleId = computed(() => `modal-title-${Math.random().toString(36).substr(2, 9)}`)
const descriptionId = computed(() => `modal-description-${Math.random().toString(36).substr(2, 9)}`)

// Methods
const close = () => {
  isOpen.value = false
  emit('close')
}

const confirm = () => {
  emit('confirm')
}

const handleBackdropClick = () => {
  if (props.closeOnBackdrop) {
    close()
  }
}

const handleEscapeKey = (event) => {
  if (props.closeOnEscape && event.key === 'Escape') {
    close()
  }
}

// Watchers
watch(() => props.isOpen, (newValue) => {
  isOpen.value = newValue
})

watch(isOpen, (newValue) => {
  if (newValue) {
    // Prevent body scroll when modal is open
    document.body.style.overflow = 'hidden'
    // Focus management
    nextTick(() => {
      const modalElement = document.querySelector('[role="dialog"]')
      if (modalElement) {
        modalElement.focus()
      }
    })
  } else {
    // Restore body scroll when modal is closed
    document.body.style.overflow = ''
  }
})

// Lifecycle
onMounted(() => {
  if (props.closeOnEscape) {
    document.addEventListener('keydown', handleEscapeKey)
  }
})

onUnmounted(() => {
  document.removeEventListener('keydown', handleEscapeKey)
  // Ensure body scroll is restored
  document.body.style.overflow = ''
})
</script>

<style scoped>
/* Additional custom styles can be added here if needed */
</style>

