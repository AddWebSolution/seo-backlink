<template>
  <VAlert
    v-if="visible"
    :type="type"
    dense
    closable
    variant="flat"
    style="position: fixed; bottom: 20px; right: 20px; max-width: 600px; width: auto; z-index: 1000;"
  >
    <span class="text-body-1 text-white">{{ message }}</span>
  </VAlert>
</template>

<script setup>
import { useAlert } from '@/composables/useAlert.js'

const { visible, message, type } = useAlert()
</script>
