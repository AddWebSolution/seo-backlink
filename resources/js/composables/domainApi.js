import { useApi } from './useApi'

// 👉 List / index with filters + pagination
export function useDomainIndex() {
    return useApi('api/clientdomain/get', {
        method: 'POST',
    })
}

// 👉 List domains names list
export function useDomainList() {
    return useApi('api/clientdomain/domain/list', {
        method: 'POST',
    })
}

// 👉 Show single domain by ID
export function useDomainShow(id) {
    return useApi(`api/clientdomain/get/${id}`, { method: 'POST' })
}

// 👉 Store (create) a new domain
export function useDomainStore(payload) {
    return useApi('api/clientdomain/store', {
        method: 'POST',
        body: payload,
    })
}

// 👉 Update an existing domain
export function useDomainUpdate(id, payload) {
    return useApi(`api/clientdomain/update/${id}`, {
        method: 'POST',
        body: payload,
    })
}

// 👉 (Optional) Delete a domain
export function useDomainDelete(id) {
    return useApi(`api/clientdomain/delete/${id}`, { method: 'POST' })
}
