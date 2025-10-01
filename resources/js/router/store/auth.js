import { defineStore } from 'pinia'
// import ability from '@/casl/ability'

// export const useAuthStore = defineStore('auth', {
//   state: () => ({
//     user: null,
//     token: null,
//     permissions: [],
//   }),
//   actions: {
//     setUser(data) {
//       this.user = data.user
//       this.token = data.token
//       this.permissions = data.user.roles?.flatMap(r => r.permissions.map(p => p.name)) || []

//       ability.update(this.permissions)
//     },
//     logout() {
//       this.user = null
//       this.token = null
//       this.permissions = []
//       ability.update([])
//     },
//   },
//   getters: {
//     isSuperAdmin: state => state.user?.roles?.some(r => r.name === 'super_admin'),
//     isClient: state => state.user?.roles?.some(r => r.name === 'client'),
//   },
// })
