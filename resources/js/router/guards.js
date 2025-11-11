import { canNavigate } from '@layouts/plugins/casl'
import { useAbility } from '@casl/vue'

export const setupGuards = router => {
  router.beforeEach(to => {
    const token = useCookie('accessToken').value
    const roleId = Number(useCookie('role_id').value)
    const isLoggedIn = !!token
    const ability = useAbility()

    if (to.meta?.public || to.name === 'login')
      return true

    if (!isLoggedIn)
      return { name: 'login', query: { to: to.fullPath } }

    const { action, subject, roles } = to.meta || {}

    // if (roles && !roles.includes(roleId)) {
    //   return { name: 'not-authorized' }
    // }

    if (action && subject) {
      const allowed = ability.can(action, subject)
      if (!allowed) return { name: 'not-authorized' }
    }

    if (!canNavigate(to)) {
      return { name: 'not-authorized' }
    }

    return true
  })
}
