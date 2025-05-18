import axios from 'axios';
import { defineStore } from 'pinia';

interface User {
    id: number;
    name: string;
    email: string;
    role: string;
    permissions: string[];
}

interface AuthState {
    user: User | null;
    token: string | null;
    isAuthenticated: boolean;
    errors: Record<string, string[]>;
}

export const useAuthStore = defineStore('auth', {
    state: (): AuthState => ({
        user: null,
        token: localStorage.getItem('token'),
        isAuthenticated: false,
        errors: {},
    }),

    getters: {
        getUser: (state) => state.user,
        getToken: (state) => state.token,
        isLoggedIn: (state) => state.isAuthenticated,
        getErrors: (state) => state.errors,
        hasErrors: (state) => Object.keys(state.errors).length > 0,
        getUserRole: (state) => state.user?.role || null,
        getUserPermissions: (state) => state.user?.permissions || [],
        hasPermission: (state) => (permission: string) => {
            return state.user?.permissions?.includes(permission) || false;
        },
        hasRole: (state) => (role: string) => {
            return state.user?.role === role;
        },
    },

    actions: {
        async login(email: string, password: string) {
            try {
                this.clearErrors();
                const response = await axios.post('/auth/login', {
                    email,
                    password,
                });

                const { user, token } = response.data;
                
                // Ensure user object has the required properties
                const userData: User = {
                    id: user.id,
                    name: user.name,
                    email: user.email,
                    role: user.role || 'user', // Default to 'user' if role is not provided
                    permissions: user.permissions || [], // Default to empty array if permissions are not provided
                };
                
                this.setAuth(userData, token);
                return { success: true };
            } catch (error: any) {
                if (error.response?.status === 422) {
                    // Validation errors
                    this.errors = error.response.data.errors || {};
                    return {
                        success: false,
                        error: 'Please check your input and try again.',
                        validationErrors: this.errors,
                    };
                } else if (error.response?.status === 401) {
                    // Authentication error
                    return {
                        success: false,
                        error: 'Invalid email or password.',
                    };
                } else {
                    // Other errors
                    return {
                        success: false,
                        error: error.response?.data?.message || 'An error occurred during login. Please try again later.',
                    };
                }
            }
        },

        async register(name: string, email: string, password: string, password_confirmation: string) {
            try {
                this.clearErrors();
                const response = await axios.post('/auth/register', {
                    name,
                    email,
                    password,
                    password_confirmation,
                });

                const { user, token } = response.data;
                
                // Ensure user object has the required properties
                const userData: User = {
                    id: user.id,
                    name: user.name,
                    email: user.email,
                    role: user.role || 'user', // Default to 'user' if role is not provided
                    permissions: user.permissions || [], // Default to empty array if permissions are not provided
                };
                
                this.setAuth(userData, token);
                return { success: true };
            } catch (error: any) {
                if (error.response?.status === 422) {
                    // Validation errors
                    this.errors = error.response.data.errors || {};
                    return {
                        success: false,
                        error: 'Please check your input and try again.',
                        validationErrors: this.errors,
                    };
                } else {
                    // Other errors
                    return {
                        success: false,
                        error: error.response?.data?.message || 'An error occurred during registration. Please try again later.',
                    };
                }
            }
        },

        async logout() {
            try {
                await axios.post('/auth/logout', {}, {
                    headers: {
                        Authorization: `Bearer ${this.token}`,
                    },
                });
                this.clearAuth();
                return { success: true };
            } catch (error: any) {
                return {
                    success: false,
                    error: error.response?.data?.message || 'An error occurred during logout. Please try again later.',
                };
            }
        },

        async fetchUser() {
            try {
                const response = await axios.get('/auth/user', {
                    headers: {
                        Authorization: `Bearer ${this.token}`,
                    },
                });
                
                // Ensure user object has the required properties
                const userData: User = {
                    id: response.data.id,
                    name: response.data.name,
                    email: response.data.email,
                    role: response.data.role || 'user', // Default to 'user' if role is not provided
                    permissions: response.data.permissions || [], // Default to empty array if permissions are not provided
                };
                
                this.user = userData;
                this.isAuthenticated = true;
            } catch (error) {
                this.clearAuth();
            }
        },

        setAuth(user: User, token: string) {
            this.user = user;
            this.token = token;
            this.isAuthenticated = true;
            localStorage.setItem('token', token);
            axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
        },

        clearAuth() {
            this.user = null;
            this.token = null;
            this.isAuthenticated = false;
            localStorage.removeItem('token');
            delete axios.defaults.headers.common['Authorization'];
        },

        clearErrors() {
            this.errors = {};
        },

        // Check if user has specific permission
        can(permission: string): boolean {
            return this.user?.permissions?.includes(permission) || false;
        },

        // Check if user has specific role
        is(role: string): boolean {
            return this.user?.role === role;
        },
    },
});
