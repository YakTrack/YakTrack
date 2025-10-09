# Modal Component

A modern, accessible Vue 3 modal component built with Composition API and modern Vue 3 patterns.

## Features

- **Modern Vue 3**: Built with Composition API and `<script setup>`
- **Accessible**: Full ARIA support, keyboard navigation, focus management
- **Animated**: Smooth transitions with Vue 3's `<Transition>` component
- **Teleported**: Renders outside the component tree to avoid z-index issues
- **Customizable**: Flexible props for different use cases
- **Type Safe**: Full TypeScript support with proper prop validation

## Usage

### Basic Usage

```vue
<template>
  <div>
    <button @click="showModal = true">Open Modal</button>
    
    <modal
      :is-open="showModal"
      title="Confirm Action"
      description="Are you sure you want to proceed?"
      @close="showModal = false"
      @confirm="handleConfirm"
    />
  </div>
</template>

<script setup>
import { ref } from 'vue'
import Modal from '@/components/Modal.vue'

const showModal = ref(false)

const handleConfirm = () => {
  console.log('Confirmed!')
  showModal.value = false
}
</script>
```

### With Custom Content

```vue
<template>
  <modal
    :is-open="isOpen"
    title="Custom Modal"
    @close="close"
  >
    <div class="space-y-4">
      <p>This is custom content inside the modal.</p>
      <input v-model="inputValue" placeholder="Enter something..." />
    </div>
    
    <template #footer>
      <button @click="close" class="btn-secondary">Cancel</button>
      <button @click="save" class="btn-primary">Save</button>
    </template>
  </modal>
</template>
```

### With Default Footer

```vue
<template>
  <modal
    :is-open="isOpen"
    title="Delete Item"
    description="This action cannot be undone."
    :show-default-footer="true"
    cancel-text="Cancel"
    confirm-text="Delete"
    @close="close"
    @confirm="confirmDelete"
  />
</template>
```

## Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `isOpen` | Boolean | `false` | Controls modal visibility |
| `title` | String | `''` | Modal title (optional) |
| `description` | String | `''` | Modal description (optional) |
| `showCloseButton` | Boolean | `true` | Show close button in header |
| `showDefaultFooter` | Boolean | `false` | Show default footer with Cancel/Confirm buttons |
| `cancelText` | String | `'Cancel'` | Text for cancel button |
| `confirmText` | String | `'Confirm'` | Text for confirm button |
| `closeOnBackdrop` | Boolean | `true` | Close modal when clicking backdrop |
| `closeOnEscape` | Boolean | `true` | Close modal when pressing Escape key |

## Events

| Event | Payload | Description |
|-------|---------|-------------|
| `close` | - | Emitted when modal is closed |
| `confirm` | - | Emitted when confirm button is clicked |

## Slots

| Slot | Description |
|------|-------------|
| `default` | Main modal content |
| `header` | Custom header content (replaces title) |
| `footer` | Custom footer content (replaces default footer) |

## Accessibility Features

- **ARIA attributes**: Proper `role="dialog"`, `aria-modal`, `aria-labelledby`, `aria-describedby`
- **Focus management**: Automatically focuses the modal when opened
- **Keyboard navigation**: Escape key closes the modal
- **Screen reader support**: Proper labeling and descriptions
- **Body scroll lock**: Prevents background scrolling when modal is open

## Styling

The modal uses Tailwind CSS classes and can be customized by:

1. **CSS Custom Properties**: Override CSS variables for colors and spacing
2. **Tailwind Classes**: Modify the component to use different Tailwind classes
3. **CSS Modules**: Use CSS modules for component-specific styling

## Examples

### Split Session Modal

```vue
<template>
  <modal
    :is-open="isOpen"
    title="Split Session"
    description="Enter the time when you want to split this session."
    :show-default-footer="true"
    cancel-text="Cancel"
    confirm-text="Split Session"
    @close="close"
    @confirm="handleSplit"
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
          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
          :min="session?.startedAtInputFormat"
          :max="session?.endedAtInputFormat"
        />
      </div>
    </div>
  </modal>
</template>
```

### Confirmation Dialog

```vue
<template>
  <modal
    :is-open="isOpen"
    title="Delete Confirmation"
    description="Are you sure you want to delete this item? This action cannot be undone."
    :show-default-footer="true"
    cancel-text="Cancel"
    confirm-text="Delete"
    @close="close"
    @confirm="confirmDelete"
  />
</template>
```

## Migration from Old Modal

To migrate from the old event-based modal:

1. Replace the old modal component with `Modal`
2. Convert event listeners to callback props
3. Use `isOpen` prop instead of event-based opening/closing
4. Handle `@close` and `@confirm` events instead of global events

```vue
<!-- Old way -->
<modal
  open-on="session.split"
  close-on="session.split-cancelled"
  :on-submit="splitSession"
>
  <!-- content -->
</modal>

<!-- New way -->
<modal
  :is-open="showSplitModal"
  @close="closeSplitModal"
  @confirm="handleSplit"
>
  <!-- content -->
</modal>
```

