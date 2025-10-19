<template>
    <div class="mb-6">
        <label 
            v-if="label" 
            :for="fieldId" 
            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
        >
            {{ label }}
            <span v-if="required" class="text-red-500 ml-1">*</span>
        </label>
        
        <div class="relative">
            <!-- Text Input -->
            <input
                v-if="type === 'text' || type === 'email' || type === 'password' || type === 'number'"
                :id="fieldId"
                :type="type"
                :value="modelValue"
                @input="$emit('update:modelValue', $event.target.value)"
                :placeholder="placeholder"
                :required="required"
                :disabled="disabled"
                :class="inputClasses"
            />
            
            <!-- Textarea -->
            <textarea
                v-else-if="type === 'textarea'"
                :id="fieldId"
                :value="modelValue"
                @input="$emit('update:modelValue', $event.target.value)"
                :placeholder="placeholder"
                :required="required"
                :disabled="disabled"
                :rows="rows"
                :class="inputClasses"
            ></textarea>
            
            <!-- Select -->
            <select
                v-else-if="type === 'select'"
                :id="fieldId"
                :value="modelValue"
                @change="$emit('update:modelValue', $event.target.value)"
                :required="required"
                :disabled="disabled"
                :class="inputClasses"
            >
                <option v-if="placeholder" value="">{{ placeholder }}</option>
                <option 
                    v-for="option in options" 
                    :key="getOptionValue(option)" 
                    :value="getOptionValue(option)"
                >
                    {{ getOptionLabel(option) }}
                </option>
            </select>
            
            <!-- Checkbox -->
            <div v-else-if="type === 'checkbox'" class="flex items-center">
                <input
                    :id="fieldId"
                    type="checkbox"
                    :checked="modelValue"
                    @change="$emit('update:modelValue', $event.target.checked)"
                    :required="required"
                    :disabled="disabled"
                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                />
                <label v-if="checkboxLabel" :for="fieldId" class="ml-2 block text-sm text-gray-700 dark:text-gray-300">
                    {{ checkboxLabel }}
                </label>
            </div>
            
            <!-- Custom slot for complex inputs -->
            <slot v-else></slot>
        </div>
        
        <!-- Help text -->
        <p v-if="help" class="mt-1 text-sm text-gray-500 dark:text-gray-400">
            {{ help }}
        </p>
        
        <!-- Error message -->
        <div v-if="error" class="mt-1 text-sm text-red-600 dark:text-red-400">
            {{ error }}
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
    modelValue: {
        type: [String, Number, Boolean, Array],
        default: ''
    },
    type: {
        type: String,
        default: 'text'
    },
    label: {
        type: String,
        default: ''
    },
    placeholder: {
        type: String,
        default: ''
    },
    help: {
        type: String,
        default: ''
    },
    error: {
        type: String,
        default: ''
    },
    required: {
        type: Boolean,
        default: false
    },
    disabled: {
        type: Boolean,
        default: false
    },
    rows: {
        type: Number,
        default: 3
    },
    options: {
        type: Array,
        default: () => []
    },
    optionValue: {
        type: String,
        default: 'id'
    },
    optionLabel: {
        type: String,
        default: 'name'
    },
    checkboxLabel: {
        type: String,
        default: ''
    }
})

const emit = defineEmits(['update:modelValue'])

const fieldId = computed(() => {
    return `field-${Math.random().toString(36).substr(2, 9)}`
})

const inputClasses = computed(() => {
    const baseClasses = 'block w-full rounded-md shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200'
    const stateClasses = props.error 
        ? 'border-red-300 dark:border-red-600' 
        : 'border-gray-300 dark:border-gray-600'
    const disabledClasses = props.disabled 
        ? 'bg-gray-50 dark:bg-gray-800 text-gray-500 dark:text-gray-400 cursor-not-allowed' 
        : 'bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100'
    
    return `${baseClasses} ${stateClasses} ${disabledClasses}`
})

const getOptionValue = (option) => {
    if (typeof option === 'string' || typeof option === 'number') {
        return option
    }
    return option[props.optionValue]
}

const getOptionLabel = (option) => {
    if (typeof option === 'string' || typeof option === 'number') {
        return option
    }
    return option[props.optionLabel]
}
</script>
