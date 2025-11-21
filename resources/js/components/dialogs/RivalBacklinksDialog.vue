<script setup>
import { ref, watch, computed } from "vue";
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

// Split domains into rows of 3
const rows = computed(() => {
  const result = [];
  for (let i = 0; i < props.rivalBacklinks.length; i += 3) {
    result.push(props.rivalBacklinks.slice(i, i + 3));
  }
  return result;
});
</script>

<template>
  <VDialog v-model="localDialog" max-width="800">
    <DialogCloseBtn @click="closeDialog" />

    <VCard>
      <VCardTitle class="text-h6 pb-1 d-flex align-center gap-2">
        <VIcon icon="mdi-link-variant" size="22" color="primary" />
        Rival Backlinks
        <VChip v-if="!loading && rivalBacklinks.length > 0" size="small" color="primary" variant="tonal">
          {{ rivalBacklinks.length }}
        </VChip>
      </VCardTitle>

      <VCardText>
        <!-- Loading Section -->
        <div v-if="loading" class="text-center py-6">
          <VProgressCircular indeterminate size="28" color="primary" />
        </div>

        <!-- Domain Rows -->
        <div v-else class="domain-container">
          <div
              v-for="(row, rowIndex) in rows"
              :key="rowIndex"
              class="domain-row"
          >
            <a
                v-for="(domain, colIndex) in row"
                :key="colIndex"
                :href="`https://${domain}`"
                target="_blank"
                class="domain-item"
            >
              <VIcon icon="mdi-circle-small" size="16" class="domain-bullet" />
              <span class="domain-text">{{ domain }}</span>
              <VIcon icon="mdi-open-in-new" size="14" class="domain-link" />
            </a>
          </div>

          <!-- No Domains -->
          <div v-if="rivalBacklinks.length === 0" class="text-center pa-6">
            <VIcon icon="mdi-link-off" size="48" color="disabled" class="mb-2 opacity-50" />
            <div class="text-body-2 text-disabled">No domains found</div>
          </div>
        </div>
      </VCardText>

      <VCardActions class="pt-0">
        <VSpacer />
        <VBtn color="primary" variant="flat" size="small" @click="closeDialog">Close</VBtn>
      </VCardActions>
    </VCard>
  </VDialog>
</template>

<style scoped>
.domain-container {
  display: flex;
  flex-direction: column;
  gap: 2px;
  padding: 4px;
  min-height: 280px;
  max-height: 280px;
  overflow-y: auto;
}

.domain-row {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 12px;
  align-items: start;
}

.domain-item {
  display: flex;
  align-items: center;
  gap: 6px;
  padding: 5px 6px;
  text-decoration: none;
  color: inherit;
  border-radius: 4px;
  transition: background-color 0.15s ease;
  width: 100%;
  min-width: 0;
}

.domain-item:hover {
  background-color: rgba(var(--v-theme-primary), 0.08);
}

.domain-item:hover .domain-link {
  opacity: 1;
}

.domain-bullet {
  flex-shrink: 0;
  color: rgb(var(--v-theme-primary));
  opacity: 0.6;
}

.domain-text {
  flex: 1;
  font-size: 13px;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
  min-width: 0;
}

.domain-link {
  flex-shrink: 0;
  opacity: 0;
  transition: opacity 0.15s ease;
  color: rgb(var(--v-theme-primary));
}

</style>