import { useApi } from './useApi'

// 👉 List / index with filters + pagination
export function useReportIndex() {
    return useApi('api/report/get', {
        method: 'POST',
    })
}

// 👉 Show single domain by ID
export function useReportShow(id, body) {
  const state = useApi(
    computed(() => `api/report/backlinks/${id}`),
    { method: "POST", body }
  );

  return {
    ...state,
    refresh: () => state.execute(),
  };
}


// 👉 (Optional) Delete a domain
export function useReportDelete(id) {
    return useApi(`api/report/delete/${id}`, { method: 'POST' })
}
