<script setup>
import { VForm } from 'vuetify/components/VForm'

const props = defineProps({
  isDialogVisible: {
    type: Boolean,
    required: true,
  },
  roleId: {
    type: [String, Number],
    required: true,
    default: 0,
  },
  roleName: {
    type: String,
    required: true,
    default: '',
  },
  permissions: {
    type: Array,
    required: true,
    default: [],
  },
})

const emit = defineEmits([
  'update:isDialogVisible',
  'update:refresh',
])

// Reactive variables
const isSelectAll = ref(false)
const role = ref('')
const refPermissionForm = ref()
const loading = ref(false)
const validationErrors = ref('')
const selectedPermissions = ref([])

const permissionModules = ref([
  {
    "module": "Role & Permissions",
    "permissions": [
      { key: "role_permission_list", label: "List" },
      { key: "role_permission_view", label: "View" },
      { key: "role_permission_create", label: "Create" },
      { key: "role_permission_edit", label: "Edit" },
      { key: "role_permission_delete", label: "Delete" },
    ],
  },
  {
    "module": "User Profile",
    "permissions": [
      { key: "profile_view", label: "View" },
      { key: "profile_edit", label: "Edit" },
      { key: "profile_delete", label: "Delete" },
    ],
  },
  {
    "module": "Products",
    "permissions": [
      { key: "product_list", label: "List" },
      { key: "product_view", label: "View" },
      { key: "product_create", label: "Create" },
      { key: "product_edit", label: "Edit" },
      { key: "product_delete", label: "Delete" },
    ],
  },
  {
    "module": "Licenses",
    "permissions": [
      { key: "license_list", label: "List" },
      { key: "license_view", label: "View" },
      { key: "license_create", label: "Create" },
      { key: "license_edit", label: "Edit" },
      { key: "license_delete", label: "Delete" },
    ],
  },
  {
    "module": "Tickets",
    "permissions": [
      { key: "ticket_list", label: "List" },
      { key: "ticket_view", label: "View" },
      { key: "ticket_create", label: "Create" },
      { key: "ticket_edit", label: "Edit" },
      { key: "ticket_delete", label: "Delete" },
    ],
  },
  {
    "module": "Reports",
    "permissions": [
      { key: "report_view", label: "View" },
      { key: "report_export", label: "Export" },
    ],
  },
])

const allPermissions = computed(() => {
  let all = []
  permissionModules.value.forEach(module => {
    module.permissions.forEach(permission => {
      all.push(permission.key)
    })
  })

  return all
})

const checkIfAllSelected = () => {
  const allPerms = allPermissions.value
  const selectedPerms = selectedPermissions.value

  if (!allPerms || !selectedPerms ||
      allPerms.length === 0 || selectedPerms.length === 0) {
    return false
  }

  return allPerms.length === selectedPerms.length &&
      allPerms.every(perm => selectedPerms.includes(perm))
}

watch(
    () => props.isDialogVisible,
    newValue => {
      if (newValue) {
        role.value = props.roleName
        selectedPermissions.value = []

        if (props.roleId > 0 && props.permissions.length > 0) {
          selectedPermissions.value = props.permissions.map(p => p.name || p)

          nextTick(() => {
            isSelectAll.value = checkIfAllSelected()
          })
        }

        validationErrors.value = ''
      }
    },
)

watch(isSelectAll, val => {
  if (val) {
    selectedPermissions.value = [...allPermissions.value]
  } else {
    if (checkIfAllSelected()) {
      selectedPermissions.value = []
    }
  }
})

watch(selectedPermissions, () => {
  const allSelected = checkIfAllSelected()
  if (isSelectAll.value !== allSelected) {
    isSelectAll.value = allSelected
  }
}, { deep: true })

const storeRolePermissions = async roleData => {
  try {
    loading.value = true

    const url = roleData.id && roleData.id > 0 ? `/roles/${roleData.id}` : '/roles'
    const method = roleData.id && roleData.id > 0 ? 'POST' : 'POST'

    const response = await $api(url, {
      method,
      body: roleData,
    })

    loading.value = false

    if (response.success) {
      validationErrors.value = ''
      emit('update:refresh', true)
      emit('update:isDialogVisible', false)
    } else {
      let errorMessage = ''

      if (response.data?.name?.[0]) {
        errorMessage += response.data.name[0] + '<br>'
      }

      if (response.data?.permission?.[0]) {
        errorMessage += response.data.permission[0]
      }

      validationErrors.value = errorMessage
    }
  } catch (err) {
    loading.value = false
    validationErrors.value = 'Something went wrong. Please try again.'
    console.error('Error saving role:', err)
  }
}

const onSubmit = () => {
  const roleData = {
    id: props.roleId,
    name: role.value,
    permission: selectedPermissions.value,
  }

  refPermissionForm.value?.validate().then(async ({ valid: isValid }) => {
    if (isValid) {
      await storeRolePermissions(roleData)
    }
  })
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
    <!-- Dialog close button -->
    <DialogCloseBtn @click="onReset" />

    <VCard class="pa-sm-10 pa-2">
      <VCardText>
        <!-- Title -->
        <h4 class="text-h4 text-center mb-2">
          {{ props.roleId > 0 ? 'Edit' : 'Add New' }} Role
        </h4>
        <p class="text-body-1 text-center mb-6">
          Set Role Permissions
        </p>

        <!-- Validation Errors -->
        <VAlert
            v-if="validationErrors"
            class="mb-4"
            prominent
            type="error"
            icon="tabler-exclamation-circle"
            v-html="validationErrors"
        />

        <!-- Form -->
        <VForm
            ref="refPermissionForm"
            @submit.prevent="onSubmit"
        >
          <!-- Role name input -->
          <AppTextField
              v-model="role"
              label="Role Name"
              :rules="[requiredValidator]"
              placeholder="Enter Role Name"
              class="mb-6"
          />

          <h5 class="text-h5 mt-6 mb-3">
            Assign Permissions
          </h5>

          <!-- Permissions Table -->
          <VTable class="permission-table text-no-wrap mb-6">
            <!-- Select All Row -->
            <tr>
              <td>
                <h6 class="text-h6">
                  Administrator Access
                </h6>
              </td>
              <td colspan="3">
                <div class="d-flex justify-end">
                  <VCheckbox
                      v-model="isSelectAll"
                      label="Select All"
                  />
                </div>
              </td>
            </tr>

            <!-- Permission modules -->
            <template
                v-for="module in permissionModules"
                :key="module.module"
            >
              <tr>
                <td>
                  <h6 class="text-h6">
                    {{ module.module }}
                  </h6>
                </td>
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

          <!-- Action buttons -->
          <div class="d-flex align-center justify-center gap-4">
            <VBtn
                type="submit"
                :loading="loading"
                :disabled="loading"
            >
              {{ props.roleId > 0 ? 'Update' : 'Create' }} Role
            </VBtn>

            <VBtn
                color="secondary"
                variant="tonal"
                @click="onReset"
            >
              Cancel
            </VBtn>
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