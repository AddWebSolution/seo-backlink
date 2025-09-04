// src/apis/authApi.js
import { useApi } from './useApi'

// 👉 Login
export async function useAuthLogin(payload) {
    return useApi('api/auth/login', {
        method: 'POST',
        body: payload,
    })
}


// 👉 Register (if you have register API)
export function useAuthRegister(payload) {
    return useApi('api/auth/register', {
        method: 'POST',
        body: payload,
    })
}

// 👉 Logout
export function useAuthLogout() {
    return useApi('api/auth/logout', {
        method: 'POST',
    })
}

// 👉 Refresh token (if available in Laravel app)
export function useAuthRefresh() {
    return useApi('api/auth/refresh', {
        method: 'POST',
    })
}

// 👉 Get current authenticated user
export function useAuthUser() {
    return useApi('api/auth/me', {
        method: 'POST',
    })
}
