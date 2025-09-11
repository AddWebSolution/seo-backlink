<template>
  <VAlert v-if="visible" :type="type" class="my-4" dense text>
    {{ message }}
  </VAlert>
</template>

<script setup>
import { ref, provide } from 'vue';

const visible = ref(false);
const message = ref('');
const type = ref('info');
let timeout = null;

const showAlert = (msg, alertType = 'info', duration = 3000) => {
  message.value = msg;
  type.value = alertType;
  visible.value = true;

  if (timeout) clearTimeout(timeout);

  timeout = setTimeout(() => {
    visible.value = false;
  }, duration);
};

provide('showAlert', showAlert);
</script>
