import { canNavigate } from '@layouts/plugins/casl'


export const setupGuards = router => {
  router.beforeEach((to) => {
    const token = useCookie('accessToken').value
    const role_id = useCookie('role_id').value
    const isLoggedIn = !!token

    if (to.meta.public || to.name === 'login') return

    if (!isLoggedIn) return { name: 'login', query: { to: to.fullPath } }

    // ACL check
    if (to.meta.roles && !to.meta.roles.includes(role_id)) {
      return { name: 'not-authorized' }
    }


    if (!canNavigate(to) && to.matched.length) {
      return { name: 'not-authorized' }
    }
  })
}
