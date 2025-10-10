<script setup>
import congoImg from '@images/illustrations/congo-illustration.png'
import useAuthStore from '@/router/store/auth'
import { useRouter } from 'vue-router'
import { ref } from 'vue'

const authStore = useAuthStore()
const router = useRouter()
const user = authStore.user

// Sample data for widgets - replace with your actual data
const stats = ref({
  totalClients: 156,
  activeDomains: 234,
  totalReports: 89,
  pendingTasks: 12
})

const recentActivity = ref([
  { title: 'New client registered', time: '2 hours ago', icon: 'tabler-user-plus', color: 'success' },
  { title: 'Report generated', time: '5 hours ago', icon: 'tabler-file-text', color: 'info' },
  { title: 'Domain verified', time: '1 day ago', icon: 'tabler-shield-check', color: 'primary' },
  { title: 'User updated profile', time: '2 days ago', icon: 'tabler-edit', color: 'warning' }
])

const quickActions = ref(
  user.role == 1 
    ? [
        { title: 'Add Client', icon: 'tabler-user-plus', color: 'primary', route: 'apps-client-add' },
        { title: 'View Reports', icon: 'tabler-chart-bar', color: 'success', route: 'apps-reports' },
        { title: 'Manage Users', icon: 'tabler-users', color: 'info', route: 'apps-user-list' },
        { title: 'Settings', icon: 'tabler-settings', color: 'warning', route: 'settings' }
      ]
    : [
        { title: 'My Domains', icon: 'tabler-world', color: 'primary', route: 'apps-clientdomain-list' },
        { title: 'View Reports', icon: 'tabler-chart-bar', color: 'success', route: 'apps-reports' },
        { title: 'Analytics', icon: 'tabler-chart-line', color: 'info', route: 'analytics' },
        { title: 'Support', icon: 'tabler-help', color: 'warning', route: 'support' }
      ]
)

</script>

<template>
  <div class="dashboard-container">
    <VCard class="welcome-card pa-6 elevation-3" 
           :style="{ background: 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)' }">
      <VRow align="center">
        <VCol cols="12" md="8">
          <div class="text-white">
            <div class="d-flex align-center mb-2">
              <VIcon icon="tabler-sparkles" size="28" class="me-2" />
              <span class="text-overline font-weight-bold">{{ user.role == 1 ? 'ADMIN PORTAL' : 'CLIENT PORTAL' }}</span>
            </div>
            <h3 class="text-h3 font-weight-bold mb-3">
              Welcome back, {{ user.name }}! 👋
            </h3>
            <p class="text-body-1 mb-4 text-white" style="opacity: 0.95">
              {{ user.role == 1
                ? 'You have full access to manage users, clients, and reports.'
                : 'You can view reports and track your performance insights.' }}
            </p>
            <!-- <VBtn 
              color="white" 
              variant="flat" 
              size="large"
              class="text-none px-6"
              :prepend-icon="user.role == 1 ? 'tabler-user' : 'tabler-world'"
              @click="router.push({ name: user.role == 1 ? 'apps-client-list' : 'apps-clientdomain-list' })">
              {{ user.role == 1 ? 'Clients' : 'Domains' }}
            </VBtn> -->
          </div>
        </VCol>
        <VCol cols="12" md="4" class="d-none d-md-block">
          <div class="text-center">
            <VImg :src="congoImg" max-width="220" class="mx-auto illustration-bounce" />
          </div>
        </VCol>
      </VRow>
    </VCard>
  </div>
</template>

<style lang="scss" scoped>
.dashboard-container {
  padding: 0;
}

.welcome-card {
  position: relative;
  overflow: hidden;
  border-radius: 12px !important;
  
  &::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -10%;
    width: 400px;
    height: 400px;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 50%;
  }
}

.illustration-bounce {
  animation: float 3s ease-in-out infinite;
}

@keyframes float {
  0%, 100% {
    transform: translateY(0px);
  }
  50% {
    transform: translateY(-10px);
  }
}

.stat-card {
  border-radius: 12px !important;
  transition: all 0.3s ease;
  border: 1px solid rgba(0, 0, 0, 0.05);
  
  &:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12) !important;
  }
}

.action-card {
  border-radius: 12px !important;
  transition: all 0.3s ease;
  border: 1px solid rgba(0, 0, 0, 0.05);
  
  &:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
  }
}

.chart-container {
  padding: 20px 0;
}

.performance-bar-wrapper {
  height: 150px;
  width: 40px;
  margin: 0 auto;
  background: rgba(0, 0, 0, 0.04);
  border-radius: 20px;
  display: flex;
  align-items: flex-end;
  overflow: hidden;
}

.performance-bar {
  width: 100%;
  border-radius: 20px 20px 0 0;
  transition: height 0.6s ease;
  animation: growBar 1s ease-out;
}

@keyframes growBar {
  from {
    height: 0%;
  }
}

.cursor-pointer {
  cursor: pointer;
}
</style>