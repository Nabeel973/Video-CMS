import { useAuthStore } from '@/stores/auth';
import axios from 'axios';

// Set base URL for API requests
axios.defaults.baseURL = '/api';  // Remove /api prefix since it's already added by Laravel

// Add a request interceptor to add the auth token to all requests
axios.interceptors.request.use(
    (config) => {
        const authStore = useAuthStore();
        const token = authStore.getToken;
        
        if (token) {
            config.headers.Authorization = `Bearer ${token}`;
        }
        
        return config;
    },
    (error) => {
        return Promise.reject(error);
    }
);

// Add a response interceptor to handle common errors
axios.interceptors.response.use(
    (response) => {
        return response;
    },
    (error) => {
        // Handle 401 Unauthorized errors (token expired or invalid)
        if (error.response && error.response.status === 401) {
            const authStore = useAuthStore();
            authStore.clearAuth();
            window.location.href = '/auth/boxed-signin';
        }
        
        return Promise.reject(error);
    }
);

export default axios; 