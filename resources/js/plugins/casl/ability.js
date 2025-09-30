// casl/ability.js
import { AbilityBuilder, PureAbility } from '@casl/ability'
// import { useAuthStore } from '@/stores/auth'

export const defineAbilitiesFor = (permissions = []) => {
  const { can, cannot, build } = new AbilityBuilder(PureAbility)

  cannot('manage', 'all')

  permissions.forEach(permission => {
    const [action, subject] = permission.split(' ')
    if (action && subject) {
      can(action, subject)
    }
  })

  return build({
    detectSubjectType: item => item.__caslSubject || item.constructor.name
  })
}

export const ability = defineAbilitiesFor([])

export const useAbility = () => {
  // const authStore = useAuthStore()

  const update = () => {
    ability.update(defineAbilitiesFor(authStore.permissions).rules)
  }

  return { ability, update }
}
