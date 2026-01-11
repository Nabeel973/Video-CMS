import appSetting from '@/app-setting';
import { useAuthStore } from '@/stores/auth';
import { useAppStore } from '@/stores/index';
import { createRouter, createWebHistory, RouteRecordRaw } from 'vue-router';

import RolesList from "../views/admin/roles/index.vue";
import RolePermissions from "../views/admin/roles/RolePermissions.vue";
import UserManagementView from '../views/admin/users/index.vue';
import HomeView from '../views/index.vue';

const routes: RouteRecordRaw[] = [
    // ==========================================
    // CINEMA / PUBLIC MOVIE PAGES
    // ==========================================
    {
        path: '/movies',
        name: 'movies',
        component: () => import(/* webpackChunkName: "movies-page" */ '../views/cinema/MoviesPage.vue'),
        meta: { layout: 'cinema' },
    },
    {
        path: '/movies/:id',
        name: 'movie-details',
        component: () => import(/* webpackChunkName: "movie-details" */ '../views/cinema/MovieDetailsPage.vue'),
        meta: { layout: 'cinema' },
    },

    // ==========================================
    // ADMIN / DASHBOARD ROUTES
    // ==========================================
    // dashboard
    { 
        path: '/dashboard', 
        name: 'dashboard', 
        component: () => import(/* webpackChunkName: "dashboard" */ '../views/dashboard/index.vue'),
    },
    { path: '/', name: 'home', component: HomeView },
     // authentication

        {
        path: '/admin/roles',
        name: 'roles',
        component: RolesList,
        meta: { requiresAuth: true }
    },
    {
        path: '/admin/roles/:id/permissions',
        name: 'role-permissions',
        component: RolePermissions,
        meta: { requiresAuth: true }
    },
    {
        path: '/auth/boxed-signin',
        name: 'boxed-signin',
        component: () => import(/* webpackChunkName: "auth-boxed-signin" */ '../views/auth/boxed-signin.vue'),
        meta: { layout: 'auth' },
    },
    {
        path: '/auth/boxed-signup',
        name: 'boxed-signup',
        component: () => import(/* webpackChunkName: "auth-boxed-signup" */ '../views/auth/boxed-signup.vue'),
        meta: { layout: 'auth' },
    },
    {
        path: '/auth/boxed-lockscreen',
        name: 'boxed-lockscreen',
        component: () => import(/* webpackChunkName: "auth-boxed-lockscreen" */ '../views/auth/boxed-lockscreen.vue'),
        meta: { layout: 'auth' },
    },
    // {
    //     path: '/user-management',
    //     name: 'user-management',
    //     component: () => import(/* webpackChunkName: "auth-boxed-lockscreen" */UserManagementView ),
    //     meta: { layout: 'auth' },
    // },
    { path: '/user-management', name: 'user-management', component: UserManagementView },
    // Admin routes
    {
        path: '/admin/tags',
        name: 'admin-tags',
        component: () => import(/* webpackChunkName: "admin-tags" */ '../views/admin/tags/index.vue'),
    },
    {
        path: '/admin/genres',
        name: 'admin-genres',
        component: () => import(/* webpackChunkName: "admin-tags" */ '../views/admin/genres/index.vue'),
    },
    {
        path: '/admin/categories',
        name: 'admin-categories',
        component: () => import(/* webpackChunkName: "admin-tags" */ '../views/admin/categories/index.vue'),
    },
    // Add new route for roles management
    {
        path: '/admin/roles',
        name: 'admin-roles',
        component: () => import(/* webpackChunkName: "admin-roles" */ '../views/admin/roles/index.vue'),
    },
    {
        path: '/admin/advertisements',
        name: 'admin-advertisements',
        component: () => import(/* webpackChunkName: "admin-roles" */ '../views/admin/advertisements/index.vue'),
    },
    {
        path: '/admin/movies',
        name: 'admin-movies',
        component: () => import(/* webpackChunkName: "admin-roles" */ '../views/admin/movies/index.vue'),
    },
    {
        path: '/roles/:id/permissions',
        name: 'role-permissions',
        component: () => import('../views/RolePermissions.vue'),
        meta: {
            requiresAuth: true,
            title: 'Role Permissions'
        }
    }
];

const router = createRouter({
    history: createWebHistory(),
    linkExactActiveClass: 'active',
    routes,
    scrollBehavior(to, from, savedPosition) {
        if (savedPosition) {
            return savedPosition;
        } else {
            return { left: 0, top: 0 };
        }
    },
});

router.beforeEach(async (to, from, next) => {
    const store = useAppStore();
    const authStore = useAuthStore();
    
    // Public pages that don't require authentication
    const publicPages = ['/auth/boxed-signin', '/auth/boxed-signup'];
    
    // Check if the route is a cinema/public route (no auth required)
    const isCinemaRoute = to.meta?.layout === 'cinema';
    const isPublicPage = publicPages.includes(to.path) || isCinemaRoute;
    const authRequired = !isPublicPage;

    // Redirect authenticated users from home to dashboard
    if (to.path === '/' && authStore.isLoggedIn) {
        return next('/dashboard');
    }

    // Check authentication for protected routes
    if (authRequired && !authStore.isLoggedIn) {
        // If there's a token, try to fetch the user
        if (authStore.token) {
            await authStore.fetchUser();
            if (authStore.isLoggedIn) {
                // If user was trying to access home, redirect to dashboard
                if (to.path === '/') {
                    return next('/dashboard');
                }
                return next();
            }
        }
        return next('/auth/boxed-signin');
    }

    // Set layout based on route meta
    if (to?.meta?.layout == 'auth') {
        store.setMainLayout('auth');
    } else if (to?.meta?.layout == 'cinema') {
        store.setMainLayout('cinema');
    } else {
        store.setMainLayout('app');
    }
    next(true);
});
router.afterEach((to, from, next) => {
    appSetting.changeAnimation();
});
export default router;
