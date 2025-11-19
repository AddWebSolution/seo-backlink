<script setup>
import { ref, watch } from "vue";
import DialogCloseBtn from "@core/components/DialogCloseBtn.vue";

const props = defineProps({
  modelValue: Boolean,
  rivalBacklinks: { type: Array, default: () => [] },
  loading: { type: Boolean, default: false },
});

const emit = defineEmits(["update:modelValue"]);
const localDialog = ref(props.modelValue);

watch(() => props.modelValue, v => (localDialog.value = v));
watch(localDialog, v => emit("update:modelValue", v));

const closeDialog = () => {
  emit("update:modelValue", false);
};
</script>

<template>
  <VDialog v-model="localDialog" max-width="550">
    <DialogCloseBtn @click="closeDialog" />

    <VCard>
      <VCardTitle class="text-h6 pb-1">
        Rival Backlink Domains
      </VCardTitle>

      <VCardText>
        <p class="text-body-2 mb-4 text-medium-emphasis">
          These are the domains linking to your rivals.
          Viewing them helps identify backlink opportunities.
        </p>

        <!-- Loading Section -->
        <div v-if="loading" class="text-center py-8">
          <VProgressCircular indeterminate size="32" />
        </div>

        <!-- Domain List -->
        <div v-else class="domain-list">
          <!-- Each Domain Block -->
          <div
              v-for="(domain, i) in rivalBacklinks"
              :key="i"
              class="domain-item d-flex align-center gap-3 pa-3 rounded"
          >
            <VIcon icon="mdi-web" size="20" class="text-primary" />
            <div class="text-body-1 font-weight-medium">{{ domain }}</div>
          </div>

          <!-- No Domains -->
          <div v-if="rivalBacklinks.length === 0" class="text-center pa-6 text-disabled">
            <VIcon icon="mdi-link-off" size="48" class="mb-2" />
            <div>No domains found</div>
          </div>
        </div>
      </VCardText>

      <VCardActions>
        <VSpacer />
        <VBtn color="primary" @click="closeDialog">Close</VBtn>
      </VCardActions>
    </VCard>
  </VDialog>
</template>

<style scoped>
.domain-list {
  max-height: 400px;
  overflow-y: auto;
}

.domain-item {
  border: 1px solid rgba(0,0,0,0.08);
  background: rgba(0, 0, 0, 0.02);
  transition: 0.2s ease;
}

.domain-item:hover {
  background-color: rgba(var(--v-theme-primary), 0.05);
  border-color: rgba(var(--v-theme-primary), 0.25);
}

.domain-item + .domain-item {
  margin-top: 10px;
}
</style>