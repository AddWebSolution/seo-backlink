<script setup>
import { ref, computed, onMounted } from 'vue'
import { useClientApi } from "@/composables/clientApi"
import { useRouter, useRoute } from 'vue-router'

const router = useRouter()
const route = useRoute()
const { currentClient, fetchClient, updateClient, showAlert } = useClientApi()

const isEditMode = ref(false)
const loading = ref(true)
const submitting = ref(false)
const formRef = ref(null)
const fileInputRef = ref(null)

const isPasswordVisible = ref(false)
const isConfirmPasswordVisible = ref(false)
const profileImagePreview = ref('')
const showPasswordFields = ref(false)


const clientId = route.params.id


const form = ref({
  name: '',
  email: '',
  role: '',
  status: 1,
  profile_pic: '',
  phone: '',
  password: '',
  password_confirmation: '',
})

// Validation rules
const rules = {
  required: value => !!value || 'This field is required',
  email: value => !value || /^\S+@\S+\.\S+$/.test(value) || 'Enter a valid email',
  phone: value => !value || /^[0-9]{10}$/.test(value) || 'Enter a valid 10-digit phone number'
}

const passwordRules = [
  v => !v || v.length >= 8 || 'Password must be at least 8 characters',
  v => !v || /[A-Z]/.test(v) || 'Password must contain at least one uppercase letter',
  v => !v || /[a-z]/.test(v) || 'Password must contain at least one lowercase letter',
  v => !v || /[0-9]/.test(v) || 'Password must contain at least one number',
]

const confirmPasswordValidator = (value) => {
  if (showPasswordFields.value && form.value.password && !value) {
    return 'Confirm Password is required'
  }
  if (value && value !== form.value.password) {
    return 'Passwords do not match'
  }
  return true
}

const phoneValidator = (value) => {
  if (!value) return 'Phone number is required'
  const regex = /^[0-9]{10}$/
  if (!regex.test(value)) return 'Invalid 10-digit phone number'
  return true
}

const requiredValidator = (value) => {
  return !!value || 'This field is required'
}

const statusText = computed(() => {
  return currentClient.value.status == 1 ? 'Active' : 'Inactive'
})

const statusColor = computed(() => {
  return currentClient.value.status == 1 ? 'success' : 'error'
})

// Handle file upload
const handleFileSelect = (event) => {
  const file = event.target.files[0]
  if (file) {
    if (!file.type.startsWith('image/')) {
      showAlert('Please select a valid image file', 'error')
      return
    }

    if (file.size > 5 * 1024 * 1024) { // 5MB limit
      showAlert('Image size must be less than 5MB', 'error')
      return
    }

    const reader = new FileReader()
    reader.onload = (e) => {
      profileImagePreview.value = e.target.result
      form.value.profile_pic = e.target.result
    }
    reader.readAsDataURL(file)
  }
}

const removeProfileImage = () => {
  profileImagePreview.value = ''
  form.value.profile_pic = ''
  if (fileInputRef.value) {
    fileInputRef.value.value = ''
  }
}

const triggerFileInput = () => {
  fileInputRef.value?.click()
}

const enableEditMode = () => {
  isEditMode.value = true
  // Copy current data to form
  form.value = {
    name: currentClient.value.name,
    email: currentClient.value.email,
    role: currentClient.value.role.name,
    status: currentClient.value.status,
    profile_pic: currentClient.value.profile_pic,
    phone: currentClient.value.phone,
    password: '',
    password_confirmation: '',
  }
  profileImagePreview.value = currentClient.value.profile_pic
  showPasswordFields.value = false
}

const cancelEdit = () => {
  isEditMode.value = false
  showPasswordFields.value = false
  profileImagePreview.value = currentClient.value.profile_pic
  form.value.password = ''
  form.value.password_confirmation = ''
}

// const handleSubmit = async () => {
//   const { valid } = await formRef.value.validate()
//   if (!valid) return
//
//   submitting.value = true
//   try {
//     // Prepare data - only include password if it's being changed
//     const updateData = { ...form.value }
//     if (!showPasswordFields.value || !updateData.password) {
//       delete updateData.password
//       delete updateData.password_confirmation
//     }
//     await updateClient(currentClient.value.id, updateData)
//     showAlert('Client updated successfully!', 'success')
//
//     await loadClientData()
//     isEditMode.value = false
//     showPasswordFields.value = false
//   } catch (err) {
//     console.error(err)
//     showAlert(err.response?.data?.message || 'Failed to update client.', 'error')
//   } finally {
//     submitting.value = false
//   }
// }

const loadClientData = async () => {
  loading.value = true
  try {
    const response = await fetchClient(clientId)
    currentClient.value = response.data.value
    profileImagePreview.value = response.data.profile_pic
  } catch (err) {
    console.error(err)
    showAlert('Failed to load client data.', 'error')
    router.push({ name: 'apps-users-list' })
  } finally {
    loading.value = false
  }
}

const formatDate = (dateString) => {
  if (!dateString) return 'N/A'
  return new Date(dateString).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

onMounted(async () => {
  await loadClientData()
})
</script>

<template>
  <div v-if="loading" class="d-flex justify-center align-center" style="min-height: 400px;">
    <VProgressCircular indeterminate color="primary" size="64" />
  </div>

  <div v-else>

    <!-- Header -->
    <VCard class="mb-6" elevation="0" style="border: 1px solid rgba(var(--v-border-color), var(--v-border-opacity));">
      <VCardText class="pa-6">
        <VRow align="center" justify="space-between">
          <!-- Left Section: Avatar and Titles -->
          <VCol cols="12" md="8" class="d-flex align-center flex-wrap gap-4">
            <VAvatar size="56" color="primary" variant="tonal" class="me-4">
              <VIcon icon="tabler-user" ></VIcon>
            </VAvatar>
            <div>
              <h1 class="text-h4 font-weight-bold mb-1">
                {{ isEditMode ? 'Edit User' : 'User Details' }}
              </h1>
              <p class="text-body-2 text-medium-emphasis mb-0">
                {{ isEditMode ? 'Update user information' : 'View and manage user information' }}
              </p>
            </div>
            <VChip class="mt-1 ml-4" :color="statusColor" variant="tonal" size="large">
              {{ statusText }}
            </VChip>
          </VCol>

          <!-- Right Section: Back Button -->
          <VCol cols="12" md="4" class="d-flex justify-end">
            <VBtn variant="flat" @click="router.push({ name: 'apps-users-list' })">
              <VIcon icon="tabler-arrow-left" class="me-2" />
              Back to List
            </VBtn>
          </VCol>
        </VRow>
      </VCardText>
    </VCard>


    <!-- View Mode -->
    <VCard v-if="!isEditMode" elevation="0"
      style="border: 1px solid rgba(var(--v-border-color), var(--v-border-opacity));">
      <VCardText class="pa-6">
        <VRow>
          <!-- Profile Picture Section -->
          <VCol cols="12" class="mb-4">
            <div class="border rounded pa-6 text-center"
              style="border: 2px dashed rgba(var(--v-border-color), var(--v-border-opacity));">
              <VAvatar size="140" :color="currentClient.profile_pic ? 'transparent' : 'primary'" variant="tonal"
                class="mb-4 elevation-3">
                <VImg v-if="currentClient.profile_pic" :src="currentClient.profile_pic" cover />
                <span v-else class="text-h2 font-weight-bold">
                  {{ currentClient.name ? currentClient.name.charAt(0).toUpperCase() : '?' }}
                </span>
              </VAvatar>
              <h2 class="text-h5 font-weight-bold mb-1">{{ currentClient.name }}</h2>
<!--              <p class="text-body-2 text-medium-emphasis">{{ currentClient.designation }}</p>-->
            </div>
          </VCol>

          <!-- Personal Information Section -->
          <VCol cols="12">
            <h3 class="text-h6 font-weight-semibold mb-4">Personal Information</h3>
            <VDivider class="mb-4" />
          </VCol>

          <VCol cols="12" md="6">
            <div class="info-item mb-6">
              <div class="d-flex align-center mb-2">
                <VIcon icon="tabler-user" size="20" class="me-2 text-medium-emphasis" />
                <span class="text-caption text-medium-emphasis font-weight-medium">Full Name</span>
              </div>
              <p class="text-body-1 font-weight-medium mb-0 pl-7">{{ currentClient.name }}</p>
            </div>
          </VCol>

          <VCol cols="12" md="6">
            <div class="info-item mb-6">
              <div class="d-flex align-center mb-2">
                <VIcon icon="tabler-mail" size="20" class="me-2 text-medium-emphasis" />
                <span class="text-caption text-medium-emphasis font-weight-medium">Email Address</span>
              </div>
              <p class="text-body-1 font-weight-medium mb-0 pl-7">{{ currentClient.email }}</p>
            </div>
          </VCol>

          <VCol cols="12" md="6">
            <div class="info-item mb-6">
              <div class="d-flex align-center mb-2">
                <VIcon icon="tabler-phone" size="20" class="me-2 text-medium-emphasis" />
                <span class="text-caption text-medium-emphasis font-weight-medium">Phone Number</span>
              </div>
              <p class="text-body-1 font-weight-medium mb-0 pl-7">{{ currentClient.phone || 'N/A' }}</p>
            </div>
          </VCol>

          <VCol cols="12" md="6">
            <div class="info-item mb-6">
              <div class="d-flex align-center mb-2">
                <VIcon icon="tabler-shield" size="20" class="me-2 text-medium-emphasis" />
                <span class="text-caption text-medium-emphasis font-weight-medium">Role</span>
              </div>
              <p class="text-body-1 font-weight-medium mb-0 pl-7">{{ currentClient.role.name || 'N/A' }}</p>
            </div>
          </VCol>

          <!-- Account Information Section -->
          <VCol cols="12" class="mt-2">
            <h3 class="text-h6 font-weight-semibold mb-4">Account Information</h3>
            <VDivider class="mb-4" />
          </VCol>

          <VCol cols="12" md="6">
            <div class="info-item mb-6">
              <div class="d-flex align-center mb-2">
                <VIcon icon="tabler-calendar-plus" size="20" class="me-2 text-medium-emphasis" />
                <span class="text-caption text-medium-emphasis font-weight-medium">Created At</span>
              </div>
              <p class="text-body-1 font-weight-medium mb-0 pl-7">{{ formatDate(currentClient.created_at) }}</p>
            </div>
          </VCol>

          <VCol cols="12" md="6">
            <div class="info-item mb-6">
              <div class="d-flex align-center mb-2">
                <VIcon icon="tabler-calendar-event" size="20" class="me-2 text-medium-emphasis" />
                <span class="text-caption text-medium-emphasis font-weight-medium">Last Updated</span>
              </div>
              <p class="text-body-1 font-weight-medium mb-0 pl-7">{{ formatDate(currentClient.updated_at) }}</p>
            </div>
          </VCol>
        </VRow>
      </VCardText>
    </VCard>

    <!-- Edit Mode -->
<!--    <VCard v-else elevation="0" style="border: 1px solid rgba(var(&#45;&#45;v-border-color), var(&#45;&#45;v-border-opacity));">-->
<!--      <VCardText class="pa-6">-->
<!--        <VForm ref="formRef" @submit.prevent="handleSubmit">-->
<!--          <VRow>-->
<!--            &lt;!&ndash; Profile Picture Upload Section &ndash;&gt;-->
<!--            <VCol cols="12" class="mb-4">-->
<!--              <div class="border rounded pa-6"-->
<!--                style="border: 2px dashed rgba(var(&#45;&#45;v-border-color), var(&#45;&#45;v-border-opacity));">-->
<!--                <h3 class="text-h6 font-weight-semibold mb-4">Profile Picture</h3>-->

<!--                <div class="d-flex align-center flex-wrap gap-4">-->
<!--                  <VAvatar size="120" :color="profileImagePreview ? 'transparent' : 'grey-lighten-3'"-->
<!--                    class="elevation-2">-->
<!--                    <VImg v-if="profileImagePreview" :src="profileImagePreview" cover />-->
<!--                    <span v-else class="text-h3 text-grey">-->
<!--                      {{ form.name ? form.name.charAt(0).toUpperCase() : '?' }}-->
<!--                    </span>-->
<!--                  </VAvatar>-->

<!--                  <div class="flex-grow-1">-->
<!--                    <input ref="fileInputRef" type="file" accept="image/*" style="display: none"-->
<!--                      @change="handleFileSelect" />-->

<!--                    <div class="d-flex gap-3 mb-2">-->
<!--                      <VBtn color="primary" variant="tonal" prepend-icon="tabler-upload" @click="triggerFileInput">-->
<!--                        Upload Photo-->
<!--                      </VBtn>-->

<!--                      <VBtn v-if="profileImagePreview" color="error" variant="outlined" prepend-icon="tabler-x"-->
<!--                        @click="removeProfileImage">-->
<!--                        Remove-->
<!--                      </VBtn>-->
<!--                    </div>-->

<!--                    <p class="text-caption text-medium-emphasis mb-0">-->
<!--                      Allowed JPG, PNG or JPEG. Max size of 5MB-->
<!--                    </p>-->
<!--                  </div>-->
<!--                </div>-->
<!--              </div>-->
<!--            </VCol>-->

<!--            &lt;!&ndash; Personal Information Section &ndash;&gt;-->
<!--            <VCol cols="12">-->
<!--              <h3 class="text-h6 font-weight-semibold mb-4">Personal Information</h3>-->
<!--            </VCol>-->

<!--            <VCol cols="12" md="6">-->
<!--              <AppTextField v-model="form.name" label="Full Name" placeholder="Enter full name"-->
<!--                :rules="[rules.required]" prepend-inner-icon="tabler-user" variant="outlined" density="comfortable" />-->
<!--            </VCol>-->

<!--            <VCol cols="12" md="6">-->
<!--              <AppTextField v-model="form.email" label="Email Address" placeholder="john@example.com" type="email"-->
<!--                :rules="[rules.required, rules.email]" prepend-inner-icon="tabler-mail" variant="outlined"-->
<!--                density="comfortable" />-->
<!--            </VCol>-->

<!--            <VCol cols="12" md="6">-->
<!--              <AppTextField v-model="form.phone" label="Phone Number" placeholder="Enter 10-digit number"-->
<!--                :rules="[requiredValidator, phoneValidator]" prepend-inner-icon="tabler-phone" variant="outlined"-->
<!--                density="comfortable" />-->
<!--            </VCol>-->

<!--            <VCol cols="12" md="6">-->
<!--              <AppTextField v-model="form.designation" label="Designation" placeholder="e.g., Manager, Director"-->
<!--                prepend-inner-icon="tabler-briefcase" variant="outlined" density="comfortable" />-->
<!--            </VCol>-->

<!--            &lt;!&ndash; Company Information Section &ndash;&gt;-->
<!--            <VCol cols="12" class="mt-4">-->
<!--              <h3 class="text-h6 font-weight-semibold mb-4">Company Information</h3>-->
<!--            </VCol>-->

<!--            <VCol cols="12" md="6">-->
<!--              <AppTextField v-model="form.company_name" label="Company Name" placeholder="Enter company name"-->
<!--                prepend-inner-icon="tabler-building" variant="outlined" density="comfortable" />-->
<!--            </VCol>-->

<!--            <VCol cols="12" md="6">-->
<!--              <AppTextField model-value="Client" label="Role" prepend-inner-icon="tabler-shield" variant="outlined"-->
<!--                density="comfortable" readonly disabled />-->
<!--            </VCol>-->

<!--            <VCol cols="12" md="6">-->
<!--              <AppSelect v-model="form.status" :items="[-->
<!--                { title: 'Active', value: 1 },-->
<!--                { title: 'Inactive', value: 2 }-->
<!--              ]" label="Account Status" prepend-inner-icon="tabler-circle-dot" variant="outlined"-->
<!--                density="comfortable" />-->
<!--            </VCol>-->

<!--            &lt;!&ndash; Change Password Section &ndash;&gt;-->
<!--            <VCol cols="12" class="mt-4">-->
<!--              <div class="d-flex align-center justify-space-between mb-4">-->
<!--                <h3 class="text-h6 font-weight-semibold mb-0">Change Password</h3>-->
<!--                <VSwitch v-model="showPasswordFields" label="Update Password" color="primary" hide-details-->
<!--                  density="compact" />-->
<!--              </div>-->
<!--            </VCol>-->

<!--            <template v-if="showPasswordFields">-->
<!--              <VCol cols="12" md="6">-->
<!--                <AppTextField v-model="form.password" :rules="passwordRules" label="New Password"-->
<!--                  placeholder="Enter new password" :type="isPasswordVisible ? 'text' : 'password'"-->
<!--                  autocomplete="new-password" prepend-inner-icon="tabler-lock"-->
<!--                  :append-inner-icon="isPasswordVisible ? 'tabler-eye-off' : 'tabler-eye'"-->
<!--                  @click:append-inner="isPasswordVisible = !isPasswordVisible" variant="outlined"-->
<!--                  density="comfortable" />-->
<!--              </VCol>-->

<!--              <VCol cols="12" md="6">-->
<!--                <AppTextField v-model="form.password_confirmation" :rules="[confirmPasswordValidator]"-->
<!--                  label="Confirm New Password" placeholder="Re-enter new password"-->
<!--                  :type="isConfirmPasswordVisible ? 'text' : 'password'" autocomplete="new-password"-->
<!--                  prepend-inner-icon="tabler-lock-check"-->
<!--                  :append-inner-icon="isConfirmPasswordVisible ? 'tabler-eye-off' : 'tabler-eye'"-->
<!--                  @click:append-inner="isConfirmPasswordVisible = !isConfirmPasswordVisible" variant="outlined"-->
<!--                  density="comfortable" />-->
<!--              </VCol>-->

<!--              <VCol cols="12">-->
<!--                <VAlert type="info" variant="tonal" density="compact" class="mb-0">-->
<!--                  <div class="text-caption">-->
<!--                    <strong>Password Requirements:</strong>-->
<!--                    <ul class="mt-1 mb-0 pl-4">-->
<!--                      <li>At least 8 characters long</li>-->
<!--                      <li>Contains uppercase and lowercase letters</li>-->
<!--                      <li>Contains at least one number</li>-->
<!--                    </ul>-->
<!--                  </div>-->
<!--                </VAlert>-->
<!--              </VCol>-->
<!--            </template>-->

<!--            &lt;!&ndash; Action Buttons &ndash;&gt;-->
<!--            <VDivider class="mb-6" />-->
<!--            <VCol cols="12" class="mt-6 d-flex justify-end">-->
<!--              <div class="d-flex  space-between gap-4">-->
<!--                <VBtn :loading="submitting" color="primary" type="submit" size="large"-->
<!--                  prepend-icon="tabler-device-floppy">-->
<!--                  Save Changes-->
<!--                </VBtn>-->

<!--                <VBtn variant="flat" color="error" size="large" prepend-icon="tabler-x" @click="cancelEdit"-->
<!--                  :disabled="submitting">-->
<!--                  Cancel-->
<!--                </VBtn>-->
<!--              </div>-->
<!--            </VCol>-->
<!--          </VRow>-->
<!--        </VForm>-->
<!--      </VCardText>-->
<!--    </VCard>-->
  </div>
</template>

<style scoped>
.text-medium-emphasis {
  color: rgba(var(--v-theme-on-surface), var(--v-medium-emphasis-opacity));
}

.gap-3 {
  gap: 12px;
}

.gap-4 {
  gap: 16px;
}

.info-item {
  transition: all 0.2s ease;
}

.info-item:hover {
  transform: translateX(4px);
}
</style>