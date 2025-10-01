import { defineStore, storeToRefs } from 'pinia'
import { useLayoutConfigStore, cookieRef } from '@layouts/stores/config'
import { ability, updateAbilities } from '@/plugins/casl/ability'

const useAuthStore = defineStore('auth', () => {
  const user = cookieRef('auth-user', null)  
  const token = cookieRef('auth-token', null)       
  const permissions = cookieRef('auth-permissions', [])

  const { appContentLayoutNav, isVerticalNavCollapsed } = storeToRefs(useLayoutConfigStore())

  const setUser = (data) => {
    user.value = JSON.parse(JSON.stringify(data.user))
    token.value = data.token
    permissions.value = data.user.roles?.flatMap(r => r.permissions.map(p => p.name)) || []

    updateAbilities()
  }

  const logout = () => {
    user.value = null
    token.value = null
    permissions.value = []

    ability.update([]) 
  }

  const isSuperAdmin = computed(() => user.value?.roles?.some(r => r.name === 'super_admin'))
  const isClient = computed(() => user.value?.roles?.some(r => r.name === 'client'))

  return {
    user,
    token,
    permissions,
    appContentLayoutNav,
    isVerticalNavCollapsed,
    setUser,
    logout,
    isSuperAdmin,
    isClient,
  }
})

export default useAuthStore
