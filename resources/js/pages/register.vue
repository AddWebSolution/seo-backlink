<script setup>
import { VForm } from 'vuetify/components/VForm'
import AuthProvider from '@/views/pages/authentication/AuthProvider.vue'
import { VNodeRenderer } from '@layouts/components/VNodeRenderer'
import { themeConfig } from '@themeConfig'
import authV2RegisterIllustrationBorderedDark from '@images/pages/auth-v2-register-illustration-bordered-dark.png'
import authV2RegisterIllustrationBorderedLight from '@images/pages/auth-v2-register-illustration-bordered-light.png'
import authV2RegisterIllustrationDark from '@images/pages/auth-v2-register-illustration-dark.png'
import authV2RegisterIllustrationLight from '@images/pages/auth-v2-register-illustration-light.png'
import authV2MaskDark from '@images/pages/misc-mask-dark.png'
import authV2MaskLight from '@images/pages/misc-mask-light.png'

const imageVariant = useGenerateImageVariant(authV2RegisterIllustrationLight, authV2RegisterIllustrationDark, authV2RegisterIllustrationBorderedLight, authV2RegisterIllustrationBorderedDark, true)
const authThemeMask = useGenerateImageVariant(authV2MaskLight, authV2MaskDark)

const { register: authRegister, loading: authLoading, error: authError, showAlert } = useAuthApi()

const route = useRoute()
const router = useRouter()

definePage({
  meta: {
    layout: 'blank',
    unauthenticatedOnly: true,
  },
})


const refVForm = ref(null)

const form = reactive({
  name: '',
  email: '',
  phone : '',
  password: '',
  password_confirmation: '',
})


const isPasswordVisible = ref(false)


const confirmPasswordValidator = (value) => {
  if (!value) return 'Confirm Password is required'
  if (value !== form.password) return 'Passwords do not match'
  return true
}

const phoneValidator = (value) => {
  if (!value) return 'Phone number is required'
  const regex = /^[0-9]{10}$/  
  if (!regex.test(value)) return 'Invalid phone number'
  return true
}



const registerUser= async () => {
  try {
    authLoading.value = true
    const payload =  { ...form }

    const res = await authRegister(payload)

    if (res?.statusCode.value !== 201) {
    } else {
      const redirectTo = route.query.to 
        ? decodeURIComponent(route.query.to) 
        : '/login'

      console.log("Redirecting to:", redirectTo)

      router.replace(redirectTo)
    }

  } catch (err) {
    console.error("Login failed:", err)
    showAlert(err.message || 'Unexpected error', 'error')
  } finally {
    authLoading.value = false
  }
}



const onSubmit = () => {
  refVForm.value?.validate().then(({ valid: isValid }) => {
    if (isValid)
      registerUser()
  })
}
</script>

<template>
  <RouterLink to="/">
    <div class="auth-logo d-flex align-center gap-x-3">
      <VNodeRenderer :nodes="themeConfig.app.logo" />
      <h1 class="auth-title">
        {{ themeConfig.app.title }}
      </h1>
    </div>
  </RouterLink>

  <VRow no-gutters class="auth-wrapper bg-surface">
    <VCol md="8" class="d-none d-md-flex">
      <div class="position-relative bg-background w-100 me-0">
        <div class="d-flex align-center justify-center w-100 h-100" style="padding-inline: 100px;">
          <VImg max-width="500" :src="imageVariant" class="auth-illustration mt-16 mb-2" />
        </div>

        <img class="auth-footer-mask" :src="authThemeMask" alt="auth-footer-mask" height="280" width="100">
      </div>
    </VCol>

    <VCol cols="12" md="4" class="auth-card-v2 d-flex align-center justify-center"
      style="background-color: rgb(var(--v-theme-surface));">
      <VCard flat :max-width="500" class="mt-12 mt-sm-0 pa-4">
        <VCardText>
          <h4 class="text-h4 mb-1">
            Adventure starts here 🚀
          </h4>
          <p class="mb-0">
            Make your app management easy and fun!
          </p>
        </VCardText>

        <VCardText>
          <VForm ref="refVForm" @submit.prevent="onSubmit">
            <VRow>
              <!-- Username -->
              <VCol cols="12">
                <AppTextField v-model="form.name" :rules="[requiredValidator]" autofocus label="Username"
                  placeholder="Johndoe" />
              </VCol>

              <!-- email -->
              <VCol cols="12">
                <AppTextField v-model="form.email" :rules="[requiredValidator, emailValidator]" label="Email"
                  type="email" placeholder="johndoe@email.com" />
              </VCol>

              <VCol cols="12">
                <AppTextField v-model="form.phone" :rules="[requiredValidator, phoneValidator]" label="Phone Number"
                  placeholder="XXXXXXXXXX" type="tel" autocomplete="tel" prepend-inner-icon="tabler-phone" />
              </VCol>

              <!-- password -->
              <VCol cols="12">
                <AppTextField v-model="form.password" :rules="[requiredValidator]" label="Password"
                  placeholder="············" :type="isPasswordVisible ? 'text' : 'password'" autocomplete="new-password"
                  :append-inner-icon="isPasswordVisible ? 'tabler-eye-off' : 'tabler-eye'"
                  @click:append-inner="isPasswordVisible = !isPasswordVisible" />

                <!-- Confirm Password -->
                <AppTextField class="mb-5" v-model="form.password_confirmation" :rules="[requiredValidator, matchPasswordValidator]"
                  label="Confirm Password" placeholder="············" :type="isPasswordVisible ? 'text' : 'password'"
                  autocomplete="new-password" :append-inner-icon="isPasswordVisible ? 'tabler-eye-off' : 'tabler-eye'"
                  @click:append-inner="isPasswordVisible = !isPasswordVisible" />

                  <VBtn block @click="onSubmit" :loading="authLoading" :disabled="authLoading">
                    Sign up
                  </VBtn>
                </VCol>

              <!-- create account -->
              <VCol cols="12" class="text-center text-base">
                <span class="d-inline-block">Already have an account?</span>
                <RouterLink class="text-primary ms-1 d-inline-block" :to="{ name: 'login' }">
                  Sign in instead
                </RouterLink>
              </VCol>

              <VCol cols="12" class="d-flex align-center">
                <VDivider />
                <span class="mx-4">or</span>
                <VDivider />
              </VCol>

              <!-- auth providers -->
              <VCol cols="12" class="text-center">
                <AuthProvider />
              </VCol>
            </VRow>
          </VForm>
        </VCardText>
      </VCard>
    </VCol>
  </VRow>
</template>

<style lang="scss">
@use "@core-scss/template/pages/page-auth";
</style>
