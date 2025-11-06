<script setup>
import { useCookie } from '@/@core/composable/useCookie'
import { useAuthApi } from '@/composables/authApi'
import { VNodeRenderer } from '@layouts/components/VNodeRenderer'
import { themeConfig } from '@themeConfig'
import { useRoute } from 'vue-router'
import { VForm } from 'vuetify/components/VForm'
import useAuthStore from '@/router/store/auth'
import { useAlert } from '@/composables/useAlert'

const authStore = useAuthStore()

const { showAlert } = useAlert();

const { login: authLogin, loading: authLoading, error: authError } = useAuthApi()


definePage({
  meta: {
    layout: 'blank',
    unauthenticatedOnly: true,
  },
})

const isPasswordVisible = ref(false)
const route = useRoute()
const router = useRouter()

// Enhanced error handling
const errors = ref({
  email: undefined,
  password: undefined,
})

const errorMessage = ref('')
const showErrorChip = ref(false)

const refVForm = ref()

const credentials = ref({
  email: '',
  password: '',
})

const rememberMe = ref(false)
const isLoading = ref(false)

const login = async () => {
  try {
    isLoading.value = true
    const payload = { ...credentials.value }

    const res = await authLogin(payload)

    if (res?.statusCode.value !== 200) {
    } else {
      useCookie('accessToken').value = res.data.value.token;
      useCookie('role_id').value = res.data.value.user.role;
      useCookie('userData').value = JSON.parse(JSON.stringify(res.data.value))

      authStore.setUser(res.data.value)
      
      const redirectTo = route.query.to 
        ? decodeURIComponent(route.query.to)
        : '/dashboards/analytics'


      router.replace(redirectTo)
    }

  } catch (err) {
    console.error("Login failed:", err)
    showAlert(err.message || 'Unexpected error', 'error')
  } finally {
    isLoading.value = false
  }
}


const onSubmit = () => {
  refVForm.value?.validate().then(({ valid: isValid }) => {
    if (isValid)
      login()
  })
}


</script>

<template>
  <div class="auth-bg-container">
    <div class="auth-bg-image">
      <RouterLink to="/">
        <div class="auth-logo d-flex align-center gap-x-3">
          <VNodeRenderer :nodes="themeConfig.app.logo" />
          <h1 class="auth-title">
            {{ themeConfig.app.title }}
          </h1>
        </div>
      </RouterLink>
      <VRow no-gutters class="auth-wrapper d-flex align-center justify-center">
        <VCol class="d-flex align-center justify-center">
          <VCard flat :max-width="500" class="mt-12 mt-sm-0 pa-6 auth-card-shadow">
            <Transition name="slide-fade">
              <VChip v-if="showErrorChip" color="error" variant="elevated" class="mb-4 error-chip" closable
                @click:close="showErrorChip = false">
                <VIcon start icon="tabler-alert-circle" />
                {{ errorMessage }}
              </VChip>
            </Transition>

            <VCardText>
              <h4 class="text-h3 mb-2 text-center">
                <span class="text-capitalize text-primary">{{ themeConfig.app.title }}</span>
              </h4>
              <p class="text-body-1 text-center text-medium-emphasis mb-6">
                Please sign in to your account to continue
              </p>
            </VCardText>

            <VCardText>
              <VForm ref="refVForm" @submit.prevent="onSubmit">
                <VRow>
                  <!-- Email Field -->
                  <VCol cols="12">
                    <AppTextField v-model="credentials.email" label="Email Address" placeholder="johndoe@email.com"
                      type="email" autofocus :rules="[requiredValidator, emailValidator]" :error-messages="errors.email"
                      :disabled="isLoading" prepend-inner-icon="tabler-mail" />
                  </VCol>

                  <!-- Password Field -->
                  <VCol cols="12">
                    <AppTextField v-model="credentials.password" label="Password" placeholder="Enter your password"
                      :rules="[requiredValidator]" :type="isPasswordVisible ? 'text' : 'password'"
                      autocomplete="current-password" :error-messages="errors.password" :disabled="isLoading"
                      prepend-inner-icon="tabler-lock"
                      :append-inner-icon="isPasswordVisible ? 'tabler-eye-off' : 'tabler-eye'"
                      @click:append-inner="isPasswordVisible = !isPasswordVisible" />

                    <div class="d-flex align-center flex-wrap justify-space-between my-6">
                      <VCheckbox v-model="rememberMe" label="Remember me" :disabled="isLoading" color="primary" />
                      <RouterLink class="text-primary text-decoration-none" :to="{ name: 'forgot-password' }">
                        Forgot Password?
                      </RouterLink>
                    </div>

                    <VBtn block type="submit" :loading="isLoading"
                      size="large" color="primary" class="mb-4">
                      <VIcon start icon="tabler-login" />
                      Sign In
                    </VBtn>
                  </VCol>

                  <!-- Create Account Section (commented out as in original) -->
                  <VCol cols="12" class="text-center">
                    <span class="text-body-2">New on our platform?</span>
                    <RouterLink class="text-primary ms-1 text-decoration-none" :to="{ name: 'register' }">
                      Create an account
                    </RouterLink>
                  </VCol>
                </VRow>
              </VForm>
            </VCardText>
          </VCard>
        </VCol>
      </VRow>
    </div>
  </div>
</template>

<style lang="scss" scoped>
@use "@core-scss/template/pages/page-auth";

.auth-bg-container {
  position: relative;
  min-height: 100vh;
  width: 100%;
  overflow: hidden;
}


.auth-bg-image {
  position: absolute;
  inset: 0;
  background: url('../src/assets/glass.jpg') center/cover no-repeat;
  z-index: 1;
}

.auth-bg-overlay {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: linear-gradient(135deg, rgba(103, 58, 183, 0.8) 0%, rgba(63, 81, 181, 0.8) 100%);
}

.auth-illustration {
  position: relative;
  z-index: 2;
}

.auth-bg-content {
  position: relative;
  z-index: 2;
  max-width: 400px;
}

.auth-card-shadow {
  box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
  border-radius: 12px;
}

.error-chip {
  width: 100%;
  justify-content: flex-start;
  height: auto;
  min-height: 48px;
  padding: 12px 16px;
  white-space: normal;
  word-wrap: break-word;
}

// Transition animations
.slide-fade-enter-active,
.slide-fade-leave-active {
  transition: all 0.3s ease;
}

.slide-fade-enter-from {
  transform: translateY(-20px);
  opacity: 0;
}

.slide-fade-leave-to {
  transform: translateY(-20px);
  opacity: 0;
}

// Enhanced form styling
.v-text-field {
  .v-field {
    border-radius: 8px;
  }
}

.v-btn {
  border-radius: 8px;
  text-transform: none;
  font-weight: 600;
}
</style>