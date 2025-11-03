<script setup>
import { ref, computed, watch, nextTick, onMounted } from 'vue'
import { VForm } from 'vuetify/components/VForm'
import {useRolePermissions} from "@/composables/rolePermissionApi.js";
import { requiredValidator } from '@/@core/utils/validators'
import DialogCloseBtn from "@core/components/DialogCloseBtn.vue";

const props = defineProps({
  isDialogVisible: { type: Boolean, required: true },
  roleId: { type: [String, Number], required: true, default: 0 },
  roleName: { type: String, required: true, default: '' },
  permissions: { type: Array, required: true, default: [] },
})

const emit = defineEmits(['update:isDialogVisible', 'update:refresh'])

const { createRole, updateRole, fetchRoleDetails, fetchPermissions, permissions: permissionModules, loading } = useRolePermissions()

const isSelectAll = ref(false)
const role = ref('')
const refPermissionForm = ref()
const validationErrors = ref('')
const selectedPermissions = ref([])

onMounted(async () => {
  await fetchPermissions()
})

const allPermissions = computed(() =>
    permissionModules.value.flatMap(module => module.permissions.map(p => p.key))
)

const checkIfAllSelected = () => {
  const allPerms = allPermissions.value
  const selectedPerms = selectedPermissions.value
  if (!allPerms?.length || !selectedPerms?.length) return false
  return allPerms.length === selectedPerms.length && allPerms.every(p => selectedPerms.includes(p))
}

watch(
    () => props.isDialogVisible,
    async newValue => {
      if (newValue) {
        role.value = props.roleName
        selectedPermissions.value = []
        validationErrors.value = ''

        await fetchPermissions()


        // console.log(props.roleId)
        if (props.roleId > 0) {
          const roleDetails = await fetchRoleDetails(props.roleId)
          // console.log('Role details response:', roleDetails)
          if (roleDetails?.permissions) {
            selectedPermissions.value = roleDetails.permissions.map(p => p.name || p)
          }
          nextTick(() => (isSelectAll.value = checkIfAllSelected()))
        }

      }
    },
)

watch(isSelectAll, val => {
  if (val) {
    selectedPermissions.value = [...allPermissions.value]
  } else if (checkIfAllSelected()) {
    selectedPermissions.value = []
  }
})

watch(
    selectedPermissions,
    () => {
      const allSelected = checkIfAllSelected()
      if (isSelectAll.value !== allSelected) isSelectAll.value = allSelected
    },
    { deep: true },
)

const onSubmit = async () => {
  const roleData = {
    // id: props.roleId,
    name: role.value,
    permissions: selectedPermissions.value,
  }

  const { valid } = await refPermissionForm.value.validate()
  if (!valid) return

  try {
    if (props.roleId > 0) {
      await updateRole(props.roleId, roleData)
    } else {
      await createRole(roleData)
    }
    emit('update:refresh', true)
    emit('update:isDialogVisible', false)
  } catch (err) {
    validationErrors.value = err?.message || 'Something went wrong. Please try again.'
  }
}

const onReset = () => {
  emit('update:isDialogVisible', false)
  isSelectAll.value = false
  selectedPermissions.value = []
  role.value = ''
  validationErrors.value = ''
  refPermissionForm.value?.reset()
}
</script>

<template>
  <VDialog
      :width="$vuetify.display.smAndDown ? 'auto' : 900"
      :model-value="props.isDialogVisible"
      @update:model-value="onReset"
  >
    <DialogCloseBtn @click="onReset" />

    <VCard class="pa-sm-10 pa-2">
      <VCardText>
        <h4 class="text-h4 text-center mb-2">
          {{ props.roleId > 0 ? 'Edit' : 'Add New' }} Role
        </h4>
        <p class="text-body-1 text-center mb-6">Set Role Permissions</p>

        <VAlert
            v-if="validationErrors"
            class="mb-4"
            prominent
            type="error"
            icon="tabler-exclamation-circle"
            v-html="validationErrors"
        />

        <VForm ref="refPermissionForm" @submit.prevent="onSubmit">
          <AppTextField
              v-model="role"
              label="Role Name"
              :rules="[requiredValidator]"
              placeholder="Enter Role Name"
              class="mb-6"
          />

          <h5 class="text-h5 mt-6 mb-3">Assign Permissions</h5>

          <!-- Permissions Table -->
          <VTable class="permission-table text-no-wrap mb-6">
            <tr>
<!--              <td><h6 class="text-h6">Administrator Access</h6></td>-->
              <td colspan="3">
                <div class="d-flex justify-end">
                  <VCheckbox v-model="isSelectAll" label="Select All" />
                </div>
              </td>
            </tr>

            <template v-for="module in permissionModules" :key="module.module">
              <tr>
                <td><h6 class="text-h6">{{ module.module }}</h6></td>
                <td>
                  <div class="d-flex flex-wrap gap-2">
                    <VCheckbox
                        v-for="permission in module.permissions"
                        :key="permission.key"
                        v-model="selectedPermissions"
                        :label="permission.label"
                        :value="permission.key"
                    />
                  </div>
                </td>
              </tr>
            </template>
          </VTable>

          <div class="d-flex align-center justify-center gap-4">
            <VBtn type="submit" :loading="loading" :disabled="loading">
              {{ props.roleId > 0 ? 'Update' : 'Create' }} Role
            </VBtn>

            <VBtn color="secondary" variant="tonal" @click="onReset">Cancel</VBtn>
          </div>
        </VForm>
      </VCardText>
    </VCard>
  </VDialog>
</template>

<style lang="scss">
.permission-table {
  td {
    border-block-end: 1px solid rgba(var(--v-border-color), var(--v-border-opacity));
    padding-block: 0.5rem;

    .v-checkbox {
      min-inline-size: 4.75rem;
    }

    &:not(:first-child) {
      padding-inline: 0.5rem;
    }

    .v-label {
      white-space: nowrap;
    }
  }
}
</style>