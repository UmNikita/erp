import axios from 'axios';

const api = axios.create({ baseURL: '/api/v1/tasks', withCredentials: true, headers: { 'Content-Type': 'application/json' } });
let refreshing: Promise<void> | null = null;
api.interceptors.response.use(undefined, async (error) => {
    const request = error.config;
    if (error.response?.status !== 401 || request?._retried) return Promise.reject(error);
    request._retried = true;
    refreshing ??= axios.post('/api/token/refresh', {}, { withCredentials: true }).then(() => undefined).finally(() => { refreshing = null; });
    await refreshing;
    return api(request);
});
export default api;
