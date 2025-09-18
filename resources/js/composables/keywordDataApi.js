import { useApi } from './useApi'

// // 👉 List / index with filters + pagination
// export function useDomainIndex() {
//     return useApi('api/clientdomain/get', {
//         method: 'POST',
//     })
// }

// 👉 Show single domain by ID
export function useKeywordShow(id) {
    return useApi(`api/keyworddatum/get/${id}`, { method: 'POST' })
}

