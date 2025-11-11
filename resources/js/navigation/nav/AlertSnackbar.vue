<script setup>
import { useAlert } from '@/composables/useAlert'

const { visible, message, type, hideAlert } = useAlert()

const colors = {
  success: 'green',
  error: 'red',
  warning: 'orange',
  info: 'blue',
}
</script>

<template>
  <v-snackbar
    v-model="visible"
    :timeout="3000"
    :type="type"
    location="bottom right"
    rounded="lg"
    multi-line
  >
    {{ message }}

    <template #actions>
      <v-btn icon="tabler-x" size="small" @click="hideAlert" />
    </template>
  </v-snackbar>
</template>
