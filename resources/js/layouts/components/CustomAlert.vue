<template>
  <VAlert v-if="visible" :type="type" class="my-4" dense text>
    {{ message }}
  </VAlert>
</template>

<script setup>
import { useAlert } from '@/composables/useAlert.js';

const { visible, message, type } = useAlert();
</script>