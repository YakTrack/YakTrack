<template>
    <modal
        :is-open="isOpen"
        :title="title"
        :description="description"
        :show-default-footer="true"
        :cancel-text="cancelText"
        :confirm-text="confirmText"
        :close-on-backdrop="true"
        :close-on-escape="true"
        @close="handleClose"
        @confirm="handleConfirm"
    />
</template>

<script>
import Modal from '@/components/Modal.vue';

export default {
    name: 'ConfirmModal',
    components: {
        Modal,
    },
    props: {
        isOpen: {
            type: Boolean,
            default: false,
        },
        title: {
            type: String,
            default: 'Confirm Action',
        },
        description: {
            type: String,
            default: 'Are you sure you want to proceed?',
        },
        cancelText: {
            type: String,
            default: 'Cancel',
        },
        confirmText: {
            type: String,
            default: 'Confirm',
        },
    },
    emits: ['close', 'confirm'],
    methods: {
        handleClose() {
            this.$emit('close');
        },
        handleConfirm() {
            this.$emit('confirm');
        },
    },
};
</script>

