import { useApi } from './useApi'

// 👉 List / index with filters + pagination
export function useReportIndex() {
    return useApi('api/report/get', {
        method: 'POST',
    })
}

// 👉 Show single domain by ID
export function useReportShow(id) {
    return useApi(`api/report/backlinks/${id}`, { method: 'GET' })
}


// 👉 (Optional) Delete a domain
export function useReportDelete(id) {
    return useApi(`api/report/delete/${id}`, { method: 'POST' })
}
