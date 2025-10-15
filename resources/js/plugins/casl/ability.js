// import { AbilityBuilder, PureAbility } from '@casl/ability'
// import useAuthStore from '@/router/store/auth'

// export const defineAbilitiesFor = (permissions = []) => {
//   const { can, cannot, build } = new AbilityBuilder(PureAbility)

//   cannot('manage', 'all') 

//   permissions.forEach(permission => {
//     const [action, subject] = permission.split(' ')
//     if (action && subject) can(action, subject)
//   })

//   return build({
//     detectSubjectType: item => item.__caslSubject || item.constructor.name
//   })
// }

// export const ability = new PureAbility([])

// export const updateAbilities = () => {
//   const authStore = useAuthStore()
//   ability.update(defineAbilitiesFor(authStore.permissions || []).rules)

// }
