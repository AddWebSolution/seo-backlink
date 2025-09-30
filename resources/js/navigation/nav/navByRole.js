import navConfig from './navConfig'
import { useCookie } from '@/@core/composable/useCookie'

const roleMap = {
  1: 'super_admin',
  3: 'client',
}

const roleId = parseInt(useCookie('role_id').value) || 3
const roleName = roleMap[roleId] || 'client'

function filterNavByRole(items, role) {
  return items
    .reduce((acc, item, index, array) => {
      if (item.heading && !item.roles) {
        const nextItem = array[index + 1]
        if (nextItem && (!nextItem.roles || nextItem.roles.includes(role))) {
          acc.push(item)
        }
        return acc
      }

      if (item.roles && !item.roles.includes(role)) return acc

      let newItem = { ...item }
      if (item.children) {
        newItem.children = filterNavByRole(item.children, role)
        if (newItem.children.length === 0) return acc
      }

      acc.push(newItem)
      return acc
    }, [])
}

export default filterNavByRole(navConfig, roleName)
