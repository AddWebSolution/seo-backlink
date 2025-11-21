<script setup>
import { ref, watch, computed } from "vue";
import DialogCloseBtn from "@core/components/DialogCloseBtn.vue";
import { VIcon } from 'vuetify/components/VIcon'

const props = defineProps({
  modelValue: Boolean,
  rivalBacklinks: { type: Object, default: () => ({}) },
  loading: { type: Boolean, default: false },
});

const emit = defineEmits(["update:modelValue"]);
const localDialog = ref(props.modelValue);
const selectedCompany = ref(null);

watch(() => props.modelValue, v => {
  localDialog.value = v;
});

watch(localDialog, v => emit("update:modelValue", v));

watch(
    () => props.rivalBacklinks,
    (newVal) => {
      const keys = Object.keys(newVal);
      if (keys.length > 0) {
        if (!selectedCompany.value || !newVal[selectedCompany.value]) {
          selectedCompany.value = keys[0];
        }
      } else {
        selectedCompany.value = null;
      }
    },
    { deep: true, immediate: true }
);

const closeDialog = () => {
  emit("update:modelValue", false);
};

const companies = computed(() => Object.keys(props.rivalBacklinks));

const currentDomains = computed(() => {
  if (!selectedCompany.value) return [];
  return props.rivalBacklinks[selectedCompany.value] || [];
});

const totalCount = computed(() => {
  return Object.values(props.rivalBacklinks).reduce(
      (sum, domains) => sum + (domains?.length || 0),
      0
  );
});
</script>

<template>
  <VDialog v-model="localDialog" max-width="900">
    <DialogCloseBtn @click="closeDialog" />

    <VCard>
      <VCardTitle class="text-h6 pb-1 d-flex align-center gap-2">
        Rivel Referring Domains

        <VChip v-if="!loading && totalCount > 0" size="small" color="primary" variant="tonal">
          {{ totalCount }} total
        </VChip>
      </VCardTitle>

      <VCardText>
        <div v-if="loading" class="text-center py-6">
          <VProgressCircular indeterminate size="28" color="primary" />
        </div>

        <template v-else>
          <div class="layout-wrapper">

            <!-- LEFT -->
            <div class="company-sidebar">
              <VChip
                  v-for="company in companies"
                  :key="company"
                  :color="selectedCompany === company ? 'primary' : 'default'"
                  :variant="selectedCompany === company ? 'flat' : 'tonal'"
                  class="company-sidebar-item"
                  @click="selectedCompany = company"
              >
                <div class="flex align-center justify-between w-100">
                  <span class="company-text">{{ company }} ({{ rivalBacklinks[company].length }})</span>
                </div>

              </VChip>
            </div>

            <div class="with-divider"></div>
            <!-- RIGHT -->
            <div class="domain-right-panel">
              <div v-if="currentDomains.length > 0" class="domain-container">
                <div class="domain-flex">
                  <a
                      v-for="(domain, i) in currentDomains"
                      :key="i"
                      :href="`https://${domain}`"
                      target="_blank"
                      class="domain-item"
                  >
                    <VIcon icon="tabler-link" size="16" />
                    <span class="domain-text font-weight-bold text-h6">{{ domain }}</span>
                  </a>
                </div>

              </div>

              <div v-else class="text-center pa-6 text-disabled">No domains</div>
            </div>

          </div>
        </template>
      </VCardText>

      <VCardActions class="pt-0">
        <VSpacer />
        <VBtn color="primary" variant="flat" size="small" @click="closeDialog">
          Close
        </VBtn>
      </VCardActions>
    </VCard>
  </VDialog>
</template>

<style scoped>
/* Main layout */
.layout-wrapper {
  display: flex;
  gap: 20px;
  height: 320px;
}

/* LEFT */
.company-sidebar {
  width: 240px;
  display: flex;
  flex-direction: column;
  gap: 12px;
  padding-right: 6px;
  overflow-y: auto;
}

.company-sidebar-item {
  width: 100%;
  cursor: pointer;
}

.company-sidebar::-webkit-scrollbar {
  width: 6px;
}

.company-sidebar::-webkit-scrollbar-thumb {
  background: rgba(var(--v-theme-primary), 0.3);
  border-radius: 8px;
}

.with-divider {
  border-left: 1px solid rgba(0, 0, 0, 0.12);
  padding-left: 10px;
  margin-left: 10px;
}

/* RIGHT */
.domain-right-panel {
  flex: 1;
  overflow: hidden;
}

.domain-container {
  display: flex;
  flex-direction: column;
  gap: 4px;
  padding: 6px;
  max-height: 300px;
  overflow-y: auto;
}

.domain-flex {
  display: flex;
  flex-wrap: wrap;
  gap: 10px;
  padding-right: 10px;
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
}

.domain-item:hover {
  background-color: rgba(var(--v-theme-primary), 0.08);
}

.domain-text {
  flex: 1;
  font-size: 13px;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}
</style>