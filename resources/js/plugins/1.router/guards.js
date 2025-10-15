// import { canNavigate } from '@layouts/plugins/casl'

// export const setupGuards = router => {
//   // 👉 router.beforeEach
//   // Docs: https://router.vuejs.org/guide/advanced/navigation-guards.html#global-before-guards
//   router.beforeEach(to => {

//     // console.log(router.getRoutes().map(r => r.name))


//     const token = useCookie('accessToken').value

//     const role_id = useCookie('role_id').value

//     // console.log('Navigating to:', to.fullPath)
//     // console.log('User token:', token)
//     const isLoggedIn = !!token


//     // console.log('Navigating to:', to.name)
//     // console.log('Can navigate?', canNavigate(to))
//     // console.log('isLoggedIn?', isLoggedIn)

//     // console.log('Route name:', to.name)


//     if (to.meta.public || to.name === 'login') return

//     if (!isLoggedIn) {
//       return { name: 'login', query: { to: to.fullPath !== '/' ? to.fullPath : undefined } }
//     }

//     if (!canNavigate(to) && to.matched.length) {
//       return { name: 'not-authorized' }
//     }
//   })

// }
