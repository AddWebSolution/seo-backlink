<script setup>
import { ref, watch, computed } from "vue";
import DialogCloseBtn from "@core/components/DialogCloseBtn.vue";

const props = defineProps({
  modelValue: Boolean,
  users: { type: Array, default: () => [] },
  assigned: { type: Array, default: () => [] },
});

const emit = defineEmits(["update:modelValue", "assign"]);

const localDialog = ref(props.modelValue);
watch(() => props.modelValue, val => (localDialog.value = val));
watch(localDialog, val => emit("update:modelValue", val));

const selectedUsers = ref([]);
const searchQuery = ref("");

watch(() => props.assigned, (val) => {
  selectedUsers.value = val.length ? [...val] : [];
}, { immediate: true });

// Filter users based on search
const filteredUsers = computed(() => {
  if (!searchQuery.value) return props.users;
  const query = searchQuery.value.toLowerCase();
  return props.users.filter(user =>
      user.name.toLowerCase().includes(query) ||
      (user.email && user.email.toLowerCase().includes(query))
  );
});

// Toggle all users
const selectAll = computed({
  get: () => selectedUsers.value.length === props.users.length && props.users.length > 0,
  set: (val) => {
    selectedUsers.value = val ? props.users.map(u => u.id) : [];
  }
});

const closeDialog = () => {
  selectedUsers.value = [];
  searchQuery.value = "";
  emit("update:modelValue", false);
};

const assignUsers = () => {
  emit("assign", selectedUsers.value);
  closeDialog();
};
</script>

<template>
  <VDialog v-model="localDialog" max-width="550">
    <DialogCloseBtn @click="closeDialog" />
    <VCard>
      <VCardTitle class="text-h6 pb-2">Assign Users</VCardTitle>

      <VCardText>
        <!-- Search Bar -->
        <VTextField
            v-model="searchQuery"
            placeholder="Search users..."
            prepend-inner-icon="tabler-search"
            variant="outlined"
            density="compact"
            class="mb-3"
            clearable
        />

        <!-- Select All -->
        <div class="d-flex align-center mb-2 pa-2 rounded border">
          <VCheckbox
              v-model="selectAll"
              density="compact"
              hide-details
              class="flex-grow-0"
          />
          <span class="text-subtitle-2 font-weight-medium">Select All Users</span>
          <VSpacer />
        </div>

        <!-- User List -->
        <div class="user-list">
          <div
              v-for="user in filteredUsers"
              :key="user.id"
              class="user-item d-flex align-center pa-3 rounded cursor-pointer"
              :class="{ 'selected': selectedUsers.includes(user.id) }"
              @click="() => {
              const idx = selectedUsers.indexOf(user.id);
              if (idx > -1) selectedUsers.splice(idx, 1);
              else selectedUsers.push(user.id);
            }"
          >
            <VCheckbox
                :model-value="selectedUsers.includes(user.id)"
                density="compact"
                hide-details
                class="flex-grow-0"
                @click.stop
                @update:model-value="(val) => {
                const idx = selectedUsers.indexOf(user.id);
                if (val && idx === -1) selectedUsers.push(user.id);
                else if (!val && idx > -1) selectedUsers.splice(idx, 1);
              }"
            />

            <div class="flex-grow-1">
              <div class="text-body-1 font-weight-medium">{{ user.name }} (<span class=" text-disabled">{{ user.email }}</span>)</div>
            </div>
          </div>

          <div v-if="filteredUsers.length === 0" class="text-center pa-6 text-disabled">
            <VIcon icon="tabler-user-search" size="48" class="mb-2" />
            <div>No users found</div>
          </div>
        </div>
      </VCardText>

      <VCardActions class="pt-0">
        <VSpacer />
        <VBtn color="secondary" variant="outlined" @click="closeDialog">
          Cancel
        </VBtn>
        <VBtn
            color="primary"
            @click="assignUsers"
        >
          Assign ({{ selectedUsers.length }})
        </VBtn>
      </VCardActions>
    </VCard>
  </VDialog>
</template>

<style scoped>
.user-list {
  max-height: 400px;
  overflow-y: auto;
}

.user-item {
  border: 1px solid transparent;
  transition: all 0.2s;
}

.user-item:hover {
  background-color: rgba(var(--v-theme-primary), 0.05);
  border-color: rgba(var(--v-theme-primary), 0.2);
}

.user-item.selected {
  background-color: rgba(var(--v-theme-primary), 0.08);
  border-color: rgba(var(--v-theme-primary), 0.3);
}

.user-item + .user-item {
  margin-top: 8px;
}
</style>