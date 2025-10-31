<script setup>
import { ref, onMounted, computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useClientApi } from '@/composables/clientApi'
import { useAbility } from '@casl/vue'


const route = useRoute()
const router = useRouter()
const { currentClient, fetchClient, updateClient, showAlert } = useClientApi()

const submitting = ref(false)
const formRef = ref(null)
const isEditMode = ref(false)
const loading = ref(true)
const fileInputRef = ref(null)

const isPasswordVisible = ref(false)
const isConfirmPasswordVisible = ref(false)
const profileImagePreview = ref('')
const showPasswordFields = ref(false)


const ability = useAbility()

const clientId = route.params.id


const form = ref({
  name: '',
  email: '',
  company_name: '',
  website: '',
  role:'2',
  designation: '',
  status: 1,
  profile_pic: '',
  phone: '',
  industry: '',
  city: '',
  state: '',
  zip_code: '',
  country: '',
  password: '',
  password_confirmation: '',
})


const rules = {
  required: value => !!value || 'This field is required',
  email: value => !value || /^\S+@\S+\.\S+$/.test(value) || 'Enter a valid email',
  website: value => !value || /^(https?:\/\/)?([\w\d-]+\.)+\w{2,}(\/.*)?$/.test(value) || 'Enter a valid URL',
  phone: value => !value || /^\+?[\d\s-]{7,15}$/.test(value) || 'Enter a valid phone number'
}

const cancelEdit = () => {
  isEditMode.value = false
  showPasswordFields.value = false
  profileImagePreview.value = currentClient.value.profile_pic
  form.value.password = ''
  form.value.password_confirmation = ''
}

const handleFileSelect = (event) => {
  const file = event.target.files[0]
  if (file) {
    if (!file.type.startsWith('image/')) {
      showAlert('Please select a valid image file', 'error')
      return
    }
    
    if (file.size > 5 * 1024 * 1024) { 
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

const confirmPasswordValidator = (value) => {
  if (showPasswordFields.value && form.value.password && !value) {
    return 'Confirm Password is required'
  }
  if (value && value !== form.value.password) {
    return 'Passwords do not match'
  }
  return true
}

const statusText = computed(() => currentClient.value?.status === 1 ? 'Active' : 'Inactive')
const statusColor = computed(() => currentClient.value?.status === 1 ? 'success' : 'error')


const loadClientData = async () => {
  loading.value = true
  try {
    const response = await fetchClient(clientId)
    currentClient.value = response.data.value
    profileImagePreview.value = response.data.profile_pic
    Object.assign(form.value, currentClient.value)
  } catch (err) {
    console.error(err)
  } finally {
    loading.value = false
  }
}

const triggerFileInput = () => {
  fileInputRef.value?.click()
}

const removeProfileImage = () => {
  profileImagePreview.value = ''
  form.value.profile_pic = ''
  if (fileInputRef.value) {
    fileInputRef.value.value = ''
  }
}

const handleSubmit = async () => {
  const { valid } = await formRef.value.validate()
  if (!valid) return
  submitting.value = true
  try {
    const updateData = { ...form.value }
    if (!showPasswordFields.value || !updateData.password) {
      delete updateData.password
      delete updateData.password_confirmation
    }
    await updateClient(currentClient.value.id, updateData)
    await loadClientData()
    showPasswordFields.value = false
  } catch (err) {
    console.error(err)
  } finally {
    submitting.value = false
  }
}

onMounted(async () => {
  await loadClientData()
})
</script>

<template>

  <!-- Header Section -->
  <VCard class="mb-5 pa-8 overflow-hidden" elevation="2">
    <VRow align="center">
      <VCol cols="12" md="8">
        <div class="d-flex align-center">
          <VAvatar size="64" color="primary" variant="elevated" class="me-4">
            <VIcon icon="tabler-user" ></VIcon>
          </VAvatar>
          <div>
            <h1 class="text-h3 font-weight-bold mb-1">Edit Client</h1>
            <p class="text-body-1 text-medium-emphasis mb-0">
              Edit the details of the client and manage their information.
            </p>
          </div>
          <VChip class="mr-2 ma-5 " :color="statusColor" variant="tonal" size="large">
            {{ statusText }}
          </VChip>
        </div>
      </VCol>
      <VCol cols="12" md="4" class="text-md-end">
        <div class="d-flex gap-2 justify-md-end">
          <VBtn variant="flat" @click="router.push({ name: 'apps-client-list' })">
            <VIcon icon="tabler-arrow-left" class="me-2" />
            Back to List
          </VBtn>
        </div>
      </VCol>
    </VRow>
  </VCard>

  <VCard style="border: 1px solid rgba(var(--v-border-color), var(--v-border-opacity));">
    <VCardText class="pa-6">
      <VForm ref="formRef" @submit.prevent="handleSubmit">
        <VRow>
          <VCol cols="12" class="mb-4">
            <div class="border rounded pa-6"
              style="border: 2px dashed rgba(var(--v-border-color), var(--v-border-opacity));">
              <h3 class="text-h6 font-weight-semibold mb-4">Profile Picture</h3>

              <div class="d-flex align-center flex-wrap gap-4">
                <VAvatar size="120" :color="profileImagePreview ? 'transparent' : 'grey-lighten-3'" class="elevation-2">
                  <VImg v-if="profileImagePreview" :src="profileImagePreview" cover />
                  <span v-else class="text-h3 text-grey">
                    {{ form.name ? form.name.charAt(0).toUpperCase() : '?' }}
                  </span>
                </VAvatar>

                <div class="flex-grow-1">
                  <input ref="fileInputRef" type="file" accept="image/*" style="display: none"
                    @change="handleFileSelect" />

                  <div class="d-flex gap-3 mb-2">
                    <VBtn color="primary" variant="tonal" prepend-icon="tabler-upload" @click="triggerFileInput">
                      Upload Photo
                    </VBtn>

                    <VBtn v-if="profileImagePreview" color="error" variant="outlined" prepend-icon="tabler-x"
                      @click="removeProfileImage">
                      Remove
                    </VBtn>
                  </div>

                  <p class="text-caption text-medium-emphasis mb-0">
                    Allowed JPG, PNG or JPEG. Max size of 5MB
                  </p>
                </div>
              </div>
            </div>
          </VCol>

          <!-- Personal Information Section -->
          <VCol cols="12">
            <h3 class="text-h6 font-weight-semibold mb-4">Personal Information</h3>
          </VCol>

          <VCol cols="12" md="6">
            <AppTextField v-model="form.name" label="Full Name" placeholder="Enter full name" :rules="[rules.required]"
              prepend-inner-icon="tabler-user" variant="outlined" density="comfortable" />
          </VCol>

          <VCol cols="12" md="6">
            <AppTextField v-model="form.email" label="Email Address" placeholder="john@example.com" type="email"
              :rules="[rules.required, rules.email]" prepend-inner-icon="tabler-mail" variant="outlined"
              density="comfortable" />
          </VCol>

          <VCol cols="12" md="6">
            <AppTextField v-model="form.phone" label="Phone Number" placeholder="Enter 10-digit number"
              :rules="[requiredValidator, phoneValidator]" prepend-inner-icon="tabler-phone" variant="outlined"
              density="comfortable" />
          </VCol>

          <VCol cols="12" md="6">
            <AppTextField v-model="form.designation" label="Designation" placeholder="e.g., Manager, Director"
              prepend-inner-icon="tabler-briefcase" variant="outlined" density="comfortable" />
          </VCol>

          <!-- Company Information Section -->
          <VCol cols="12" class="mt-4">
            <h3 class="text-h6 font-weight-semibold mb-4">Company Information</h3>
          </VCol>

          <VCol cols="12" md="6">
            <AppTextField v-model="form.company_name" label="Company Name" placeholder="Enter company name"
              prepend-inner-icon="tabler-building" variant="outlined" density="comfortable" />
          </VCol>

          <VCol cols="12" md="6">
            <AppTextField model-value="Client" label="Role" prepend-inner-icon="tabler-shield" variant="outlined"
              density="comfortable" readonly disabled />
          </VCol>

          <VCol cols="12" md="6">
            <AppSelect v-model="form.status" :items="[
                  { title: 'Active', value: 1 },
                  { title: 'Inactive', value: 2 }
                ]" label="Account Status" prepend-inner-icon="tabler-circle-dot" variant="outlined"
              density="comfortable" />
          </VCol>

          <!-- Change Password Section -->
          <VCol v-if="ability.can('update', 'client')" cols="12" class="mt-4">
            <div class="d-flex align-center justify-space-between mb-4">
              <h3 class="text-h6 font-weight-semibold mb-0">Change Password</h3>
              <VSwitch v-model="showPasswordFields" label="Update Password" color="primary" hide-details
                density="compact" />
            </div>
          </VCol>

          <template v-if="showPasswordFields">
            <VCol cols="12" md="6">
              <AppTextField v-model="form.password" :rules="passwordRules" label="New Password"
                placeholder="Enter new password" :type="isPasswordVisible ? 'text' : 'password'"
                autocomplete="new-password" prepend-inner-icon="tabler-lock"
                :append-inner-icon="isPasswordVisible ? 'tabler-eye-off' : 'tabler-eye'"
                @click:append-inner="isPasswordVisible = !isPasswordVisible" variant="outlined" density="comfortable" />
            </VCol>

            <VCol cols="12" md="6">
              <AppTextField v-model="form.password_confirmation" :rules="[confirmPasswordValidator]"
                label="Confirm New Password" placeholder="Re-enter new password"
                :type="isConfirmPasswordVisible ? 'text' : 'password'" autocomplete="new-password"
                prepend-inner-icon="tabler-lock-check"
                :append-inner-icon="isConfirmPasswordVisible ? 'tabler-eye-off' : 'tabler-eye'"
                @click:append-inner="isConfirmPasswordVisible = !isConfirmPasswordVisible" variant="outlined"
                density="comfortable" />
            </VCol>

            <VCol cols="12">
              <VAlert type="info" variant="tonal" density="compact" class="mb-0">
                <div class="text-caption">
                  <strong>Password Requirements:</strong>
                  <ul class="mt-1 mb-0 pl-4">
                    <li>At least 8 characters long</li>
                    <li>Contains uppercase and lowercase letters</li>
                    <li>Contains at least one number</li>
                  </ul>
                </div>
              </VAlert>
            </VCol>
          </template>

          <!-- Action Buttons -->
          <VDivider class="mb-6" />
          <VCol cols="12" class="mt-6 d-flex justify-end">
            <div class="d-flex  space-between gap-4">
              <VBtn :loading="submitting" color="primary" type="submit"
                prepend-icon="tabler-device-floppy">
                Save Changes
              </VBtn>

              <VBtn variant="flat" color="error" prepend-icon="tabler-x" @click="router.back()"
                :disabled="submitting">
                Cancel
              </VBtn>
            </div>
          </VCol>
        </VRow>
      </VForm>
    </VCardText>
  </VCard>
</template>

<style scoped>
.pa-6 {
  padding: 24px;
}
</style>
