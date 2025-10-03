import { defineStore } from 'pinia'
import { ability, updateAbilities } from '@/plugins/casl/ability'

const useAuthStore = defineStore('auth', {
  state: () => ({
    user: null,
    token: null,
    permissions: [],
  }),
  actions: {
    setUser(data) {
      this.user = JSON.parse(JSON.stringify(data.user)) 
      this.token = data.token
      this.permissions = data.user.roles?.flatMap(r => r.permissions.map(p => p.name)) || []
      updateAbilities()
    },
    logout() {
      this.user = null
      this.token = null
      this.permissions = []
      ability.update([])
    },
  },
  getters: {
    isSuperAdmin: state => state.user?.roles?.some(r => r.name == 'super_admin'),
    isClient: state => state.user?.roles?.some(r => r.name == 'client'),
  },
  persist: true, 
})

export default useAuthStore


