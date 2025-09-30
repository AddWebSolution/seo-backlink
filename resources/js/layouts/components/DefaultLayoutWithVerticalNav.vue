<script setup>
import navItems from '@/navigation/vertical'
import { themeConfig } from '@themeConfig'

// Components
import Footer from '@/layouts/components/Footer.vue'
import NavbarThemeSwitcher from '@/layouts/components/NavbarThemeSwitcher.vue'
import NavBarI18n from '@core/components/I18n.vue'
import UserProfile from '@/layouts/components/UserProfile.vue'


// @layouts plugin
import { VerticalNavLayout } from '@layouts'
import CustomAlert from './CustomAlert.vue'
</script>

<template>
  <VerticalNavLayout :nav-items="navItems">
    <!-- 👉 navbar -->
    <template #navbar="{ toggleVerticalOverlayNavActive }">
      <div class="d-flex h-100 align-center">
        <IconBtn id="vertical-nav-toggle-btn" class="ms-n3 d-lg-none" @click="toggleVerticalOverlayNavActive(true)">
          <VIcon size="26" icon="tabler-menu-2" />
        </IconBtn>

        <!-- <NavSearchBar class="ms-lg-n3" /> -->
        <VSpacer />
        <UserProfile/>

        <NavBarI18n v-if="themeConfig.app.i18n.enable && themeConfig.app.i18n.langConfig?.length"
          :languages="themeConfig.app.i18n.langConfig" />
        <NavbarThemeSwitcher />
        <!-- <NavbarShortcuts /> -->
        <!-- <NavBarNotifications class="me-1" /> -->
      </div>
    </template>

    <div class="d-flex align-center">
      <CustomAlert/>
    </div>

    <!-- 👉 Pages -->
    <slot />

    <!-- 👉 Footer -->
    <template #footer>
      <Footer />
    </template>

    <!-- 👉 Customizer -->
    <TheCustomizer />
  </VerticalNavLayout>
</template>
