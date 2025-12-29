<template>
  <div class="gherkin-text font-mono whitespace-pre-wrap" :class="textSizeClass">
    <span v-for="(part, index) in highlightedParts" :key="index" :class="part.class">
      {{ part.text }}
    </span>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  text: {
    type: String,
    required: true,
  },
  size: {
    type: String,
    default: 'xs',
    validator: (value) => ['xs', 'sm', 'base'].includes(value),
  },
})

const textSizeClass = computed(() => {
  return {
    xs: 'text-xs',
    sm: 'text-sm',
    base: 'text-base',
  }[props.size]
})

const highlightedParts = computed(() => {
  if (!props.text) {
    return []
  }

  const parts = []
  const lines = props.text.split('\n')

  lines.forEach((line, lineIndex) => {
    if (lineIndex > 0) {
      parts.push({ text: '\n', class: '' })
    }

    const trimmedLine = line.trim()

    // Empty line
    if (!trimmedLine) {
      parts.push({ text: line, class: '' })
      return
    }

    // Comments
    if (trimmedLine.startsWith('#')) {
      parts.push({ text: line, class: 'text-gray-500 dark:text-gray-500' })
      return
    }

    // Tags
    if (trimmedLine.startsWith('@')) {
      parts.push({ text: line, class: 'text-purple-600 dark:text-purple-400' })
      return
    }

    // Keywords with colons (Feature:, Scenario:, etc.)
    const keywordMatch = trimmedLine.match(/^(Feature|Scenario|Scenario Outline|Background|Examples):\s*(.*)$/i)
    if (keywordMatch) {
      const indent = line.match(/^(\s*)/)?.[1] || ''
      parts.push({ text: indent, class: '' })
      parts.push({ text: keywordMatch[1] + ':', class: 'text-blue-600 dark:text-blue-400 font-semibold' })
      if (keywordMatch[2]) {
        parts.push({ text: ' ' + keywordMatch[2], class: 'text-gray-900 dark:text-white' })
      }
      return
    }

    // Step keywords (Given, When, Then, And, But)
    const stepMatch = trimmedLine.match(/^(Given|When|Then|And|But)\s+(.+)$/i)
    if (stepMatch) {
      const indent = line.match(/^(\s*)/)?.[1] || ''
      parts.push({ text: indent, class: '' })
      parts.push({ text: stepMatch[1], class: 'text-green-600 dark:text-green-400 font-semibold' })
      parts.push({ text: ' ' + stepMatch[2], class: 'text-gray-900 dark:text-white' })
      return
    }

    // Data tables (lines starting with |)
    if (trimmedLine.startsWith('|')) {
      const indent = line.match(/^(\s*)/)?.[1] || ''
      const tableContent = trimmedLine
      const cells = tableContent.split('|').slice(1, -1) // Remove empty first and last elements
      
      parts.push({ text: indent + '|', class: 'text-gray-600 dark:text-gray-400' })
      cells.forEach((cell) => {
        parts.push({ text: cell, class: 'text-gray-900 dark:text-white' })
        parts.push({ text: '|', class: 'text-gray-600 dark:text-gray-400' })
      })
      return
    }

    // Regular text
    parts.push({ text: line, class: 'text-gray-900 dark:text-white' })
  })

  return parts
})
</script>

