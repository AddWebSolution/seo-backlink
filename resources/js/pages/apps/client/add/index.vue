<script setup>
import { ref, computed, onMounted } from 'vue'
import { useClientApi } from "@/composables/clientApi"
import { useRouter } from 'vue-router'

const router = useRouter()
const { createClient, fetchClientList, showAlert } = useClientApi()

const form = ref({
  name: '',
  email: '',
  company_name: '',
  role: '3', 
  designation: '',
  status: 1,
  profile_pic: '',
  phone: '',
  password: '',
  password_confirmation: '',
})

const submitting = ref(false)
const formRef = ref(null)
const isPasswordVisible = ref(false)
const isConfirmPasswordVisible = ref(false)
const profileImagePreview = ref('')
const fileInputRef = ref(null)

const rules = {
  required: value => !!value || 'This field is required',
  email: value => !value || /^\S+@\S+\.\S+$/.test(value) || 'Enter a valid email',
  phone: value => !value || /^[0-9]{10}$/.test(value) || 'Enter a valid 10-digit phone number'
}

const passwordRules = [
  v => !!v || 'Password is required',
  v => (v && v.length >= 8) || 'Password must be at least 8 characters',
  v => /[A-Z]/.test(v) || 'Password must contain at least one uppercase letter',
  v => /[a-z]/.test(v) || 'Password must contain at least one lowercase letter',
  v => /[0-9]/.test(v) || 'Password must contain at least one number',
]

const confirmPasswordValidator = (value) => {
  if (!value) return 'Confirm Password is required'
  if (value !== form.value.password) return 'Passwords do not match'
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

const handleSubmit = async () => {
  const { valid } = await formRef.value.validate()
  if (!valid) return

  submitting.value = true
  try {
    await createClient(form.value)
    showAlert('Client created successfully!', 'success')
    router.push({ name: 'apps-client-list' })
  } catch (err) {
    console.error(err)
    showAlert(err.response?.data?.message || 'Failed to create client.', 'error')
  } finally {
    submitting.value = false
  }
}

onMounted(async () => {
  await fetchClientList()
})
</script>

<template>
  <!-- Header -->
  <VCard class="mb-6" elevation="0" style="border: 1px solid rgba(var(--v-border-color), var(--v-border-opacity));">
    <VCardText class="pa-6">
      <div class="d-flex align-center">
        <VAvatar size="56" color="primary" variant="tonal" class="me-4">
           <VIcon icon="tabler-user" ></VIcon>
        </VAvatar>
        <div>
          <h1 class="text-h4 font-weight-bold mb-1">Add New Client</h1>
          <p class="text-body-2 text-medium-emphasis mb-0">
            Create a new client account with secure credentials
          </p>
        </div>
      </div>
    </VCardText>
  </VCard>

  <!-- Form -->
  <VCard elevation="0" style="border: 1px solid rgba(var(--v-border-color), var(--v-border-opacity));">
    <VCardText class="pa-6">
      <VForm ref="formRef" @submit.prevent="handleSubmit">
        <VRow>
          <!-- Profile Picture Upload Section -->
          <VCol cols="12" class="mb-4">
            <div class="border rounded pa-6" style="border: 2px dashed rgba(var(--v-border-color), var(--v-border-opacity));">
              <h3 class="text-h6 font-weight-semibold mb-4">Profile Picture</h3>
              
              <div class="d-flex align-center flex-wrap gap-4">
                <!-- Preview or Placeholder -->
                <VAvatar
                  size="120"
                  :color="profileImagePreview ? 'transparent' : 'grey-lighten-3'"
                  class="elevation-2"
                >
                  <VImg
                    v-if="profileImagePreview"
                    :src="profileImagePreview"
                    cover
                  />
                  <span v-else class="text-h3 text-grey">
                    {{ form.name ? form.name.charAt(0).toUpperCase() : '?' }}
                  </span>
                </VAvatar>

                <!-- Upload Controls -->
                <div class="flex-grow-1">
                  <input
                    ref="fileInputRef"
                    type="file"
                    accept="image/*"
                    style="display: none"
                    @change="handleFileSelect"
                  />
                  
                  <div class="d-flex gap-3 mb-2">
                    <VBtn
                      color="primary"
                      variant="tonal"
                      prepend-icon="tabler-upload"
                      @click="triggerFileInput"
                    >
                      Upload Photo
                    </VBtn>
                    
                    <VBtn
                      v-if="profileImagePreview"
                      color="error"
                      variant="outlined"
                      prepend-icon="tabler-x"
                      @click="removeProfileImage"
                    >
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

          <!-- Name -->
          <VCol cols="12" md="6">
            <AppTextField
              v-model="form.name"
              label="Full Name"
              placeholder="Enter full name"
              :rules="[rules.required]"
              prepend-inner-icon="tabler-user"
              variant="outlined"
              density="comfortable"
            />
          </VCol>

          <!-- Email -->
          <VCol cols="12" md="6">
            <AppTextField
              v-model="form.email"
              label="Email Address"
              placeholder="john@example.com"
              type="email"
              :rules="[rules.required, rules.email]"
              prepend-inner-icon="tabler-mail"
              variant="outlined"
              density="comfortable"
            />
          </VCol>

          <!-- Phone -->
          <VCol cols="12" md="6">
            <AppTextField
              v-model="form.phone"
              label="Phone Number"
              placeholder="Enter 10-digit number"
              :rules="[requiredValidator, phoneValidator]"
              prepend-inner-icon="tabler-phone"
              variant="outlined"
              density="comfortable"
            />
          </VCol>

          <!-- Designation -->
          <VCol cols="12" md="6">
            <AppTextField
              v-model="form.designation"
              label="Designation"
              placeholder="e.g., Manager, Director"
              prepend-inner-icon="tabler-briefcase"
              variant="outlined"
              density="comfortable"
            />
          </VCol>

          <!-- Company Information Section -->
          <VCol cols="12" class="mt-4">
            <h3 class="text-h6 font-weight-semibold mb-4">Company Information</h3>
          </VCol>

          <!-- Company Name -->
          <VCol cols="12" md="6">
            <AppTextField
              v-model="form.company_name"
              label="Company Name"
              placeholder="Enter company name"
              prepend-inner-icon="tabler-building"
              variant="outlined"
              density="comfortable"
            />
          </VCol>

          <!-- Role (Hidden or Display Only) -->
          <VCol cols="12" md="6">
            <AppTextField
              model-value="Client"
              label="Role"
              prepend-inner-icon="tabler-shield"
              variant="outlined"
              density="comfortable"
              readonly
              disabled
            />
          </VCol>

          <!-- Status -->
          <VCol cols="12" md="6">
            <AppSelect
              v-model="form.status"
              :items="[
                { title: 'Active', value: 1 },
                { title: 'Inactive', value: 2 }
              ]"
              label="Account Status"
              prepend-inner-icon="tabler-circle-dot"
              variant="outlined"
              density="comfortable"
            />
          </VCol>

          <!-- Security Section -->
          <VCol cols="12" class="mt-4">
            <h3 class="text-h6 font-weight-semibold mb-4">Security Credentials</h3>
          </VCol>

          <!-- Password -->
          <VCol cols="12" md="6">
            <AppTextField
              v-model="form.password"
              :rules="passwordRules"
              label="Password"
              placeholder="Enter password"
              :type="isPasswordVisible ? 'text' : 'password'"
              autocomplete="new-password"
              prepend-inner-icon="tabler-lock"
              :append-inner-icon="isPasswordVisible ? 'tabler-eye-off' : 'tabler-eye'"
              @click:append-inner="isPasswordVisible = !isPasswordVisible"
              variant="outlined"
              density="comfortable"
            />
          </VCol>

          <!-- Confirm Password -->
          <VCol cols="12" md="6">
            <AppTextField
              v-model="form.password_confirmation"
              :rules="[requiredValidator, confirmPasswordValidator]"
              label="Confirm Password"
              placeholder="Re-enter password"
              :type="isConfirmPasswordVisible ? 'text' : 'password'"
              autocomplete="new-password"
              prepend-inner-icon="tabler-lock-check"
              :append-inner-icon="isConfirmPasswordVisible ? 'tabler-eye-off' : 'tabler-eye'"
              @click:append-inner="isConfirmPasswordVisible = !isConfirmPasswordVisible"
              variant="outlined"
              density="comfortable"
            />
          </VCol>

          <!-- Password Requirements -->
          <VCol cols="12">
            <VAlert
              type="info"
              variant="tonal"
              density="compact"
              class="mb-0"
            >
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

          <!-- Action Buttons -->
          <VDivider class="mt-6" />
          <VCol cols="12" class="mt-6 d-flex justify-end">
            <div class=" mt-4 d-flex space-between gap-4">
              <VBtn
                :loading="submitting"
                color="primary"
                type="submit"
                prepend-icon="tabler-check"
              >
                Create Client
              </VBtn>
              
              <VBtn
                variant="flat"
                color="error"
                prepend-icon="tabler-x"
                @click="router.back()"
              >
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
.text-medium-emphasis {
  color: rgba(var(--v-theme-on-surface), var(--v-medium-emphasis-opacity));
}

.gap-3 {
  gap: 12px;
}

.gap-4 {
  gap: 16px;
}
</style>