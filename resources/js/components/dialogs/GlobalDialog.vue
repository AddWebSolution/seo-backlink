<script setup>
import { ref } from 'vue'

const emit = defineEmits(['confirm', 'cancel'])

const isDialogVisible = ref(false)

// Use reactive state instead of props for dynamic values
const dialogOptions = ref({
  title: 'Confirm Action',
  message: 'Are you sure you want to proceed?',
  confirmText: 'Confirm',
  cancelText: 'Cancel',
  confirmColor: 'error',
  type: 'warning'
})

const open = (options = {}) => {
  // Update dialog options
  dialogOptions.value = {
    ...dialogOptions.value,
    ...options
  }

  isDialogVisible.value = true

  return new Promise((resolve) => {
    const handleConfirm = () => {
      isDialogVisible.value = false
      emit('confirm')
      resolve(true)
    }

    const handleCancel = () => {
      isDialogVisible.value = false
      emit('cancel')
      resolve(false)
    }

    window._confirmDialogHandlers = {
      confirm: handleConfirm,
      cancel: handleCancel
    }
  })
}

const close = () => {
  isDialogVisible.value = false
}

const handleConfirm = () => {
  if (window._confirmDialogHandlers?.confirm) {
    window._confirmDialogHandlers.confirm()
  }
}

const handleCancel = () => {
  if (window._confirmDialogHandlers?.cancel) {
    window._confirmDialogHandlers.cancel()
  }
}

defineExpose({ open, close })
</script>

<template>
  <VDialog
      v-model="isDialogVisible"
      persistent
      max-width="500"
  >
    <DialogCloseBtn @click="handleCancel" />

    <VCard>
      <VCardText class="d-flex align-start gap-3">
        <VIcon
            :color="dialogOptions.type === 'error' ? 'error' : dialogOptions.type === 'warning' ? 'warning' : 'info'"
            size="32"
            class="mt-1"
        >
          {{
            dialogOptions.type === 'error' ? 'tabler-trash' :
                dialogOptions.type === 'warning' ? 'tabler-alert-triangle' :
                    'tabler-info-circle'
          }}
        </VIcon>

        <div class="flex-grow-1">
          <h5 class="text-h5 mb-2">
            {{ dialogOptions.title }}
          </h5>
          <p class="text-body-1">
            {{ dialogOptions.message }}
          </p>
        </div>
      </VCardText>

      <VCardText class="d-flex justify-end gap-3 flex-wrap pt-0">
        <VBtn
            color="secondary"
            variant="tonal"
            @click="handleCancel"
        >
          {{ dialogOptions.cancelText }}
        </VBtn>
        <VBtn
            :color="dialogOptions.confirmColor"
            @click="handleConfirm"
        >
          {{ dialogOptions.confirmText }}
        </VBtn>
      </VCardText>
    </VCard>
  </VDialog>
</template>