import { canNavigate } from '@layouts/plugins/casl'


export const setupGuards = router => {
router.beforeEach((to) => {
  const token = useCookie('accessToken').value
  const roleId = Number(useCookie('role_id').value)
  const isLoggedIn = !!token

  if (to.meta.public || to.name === 'login') return true

  if (!isLoggedIn) return { name: 'login', query: { to: to.fullPath } }

  console.log('roles',to.meta.roles);

  if (to.meta.roles && !to.meta.roles.includes(roleId))
    return { name: 'not-authorized' }

  if (!canNavigate(to))
    return { name: 'not-authorized' }

  return true
})

}
