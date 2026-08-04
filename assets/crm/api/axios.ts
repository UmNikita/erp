import axios from 'axios'

const api = axios.create({
    baseURL: '/api/v1/crm',
    withCredentials: true,
    headers: {
        'Content-Type': 'application/json'
    }
})

let isRefreshing = false;
api.interceptors.response.use(
    response => response,

    async error => {
        if (error.response?.status === 401 && !isRefreshing) {
            isRefreshing = true;
            await axios.post('/api/token/refresh', {}, {
                withCredentials: true
            })

            return api.request(error.config)
        }

        return Promise.reject(error)
    }
)
export default api;