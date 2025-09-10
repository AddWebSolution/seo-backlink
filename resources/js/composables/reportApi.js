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

// Export reports 
export function useExportReport(requestBody) {
  return fetch('/api/report/export', {
    method: 'POST',
    headers: {
      'Accept': 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
      'Content-Type': 'application/json',
      'Authorization': `Bearer ${useCookie("accessToken").value}`,
    },
    body: JSON.stringify(requestBody)
  }).then(res => {
    if (!res.ok) throw new Error(`HTTP ${res.status}: ${res.statusText}`)
    return res.blob() 
  });
}


