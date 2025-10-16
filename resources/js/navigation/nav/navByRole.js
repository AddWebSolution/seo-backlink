import navConfig from './navConfig'

export function filterNav(items, ability) {
  const filtered = []
  
  for (let i = 0; i < items.length; i++) {
    const item = items[i]
    
    if (item.heading) {
      filtered.push(item)
      continue
    }
    
    // Safely check meta
    if (item.meta && item.meta.action && item.meta.subject && !ability.can(item.meta.action, item.meta.subject)) {
      continue
    }
    
    let newItem = { ...item }
    
    if (item.children) {
      newItem.children = filterNav(item.children, ability)
      
      if (newItem.children.length === 0) {
        continue
      }
    }
    
    filtered.push(newItem)
  }
  
  return filtered.filter((item, index, array) => {
    if (!item.heading) return true
    
    for (let i = index + 1; i < array.length; i++) {
      if (array[i].heading) break
      return true
    }
    
    return false
  })
}


// Don't export filtered nav directly at module level
// Export a function to get filtered nav instead
export function getFilteredNav(ability) {
  return filterNav(navConfig, ability)
}