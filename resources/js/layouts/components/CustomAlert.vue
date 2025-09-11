<template>
  <VAlert
    v-if="visible"
    :type="type"
    dense
    closable
    title="Alert"
    variant="elevated"
    style="position: fixed; top: 150px; left: 50%; transform: translateX(-50%); max-width: 600px; width: 90%; z-index: 1000;"
  >
    <span class="text-body-1">{{ message }}</span>
  </VAlert>
</template>   


<script setup>
import { useAlert } from '@/composables/useAlert.js';

const { visible, message, type } = useAlert();
</script>