import appSetting from '@/app-setting';
import { useAuthStore } from '@/stores/auth';
import { useAppStore } from '@/stores/index';
import { createRouter, createWebHistory, RouteRecordRaw } from 'vue-router';

import HomeView from '../views/index.vue';
import UserManagementView from '../views/user-index.vue';

const routes: RouteRecordRaw[] = [
    // dashboard
    { path: '/', name: 'home', component: HomeView },
     // authentication
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
        meta: { layout: 'auth' },
    },
    {
        path: '/admin/genres',
        name: 'admin-genres',
        component: () => import(/* webpackChunkName: "admin-tags" */ '../views/admin/genres/index.vue'),
        meta: { layout: 'auth' },
    },
    {
        path: '/admin/releases',
        name: 'admin-releases',
        component: () => import(/* webpackChunkName: "admin-tags" */ '../views/admin/releases/index.vue'),
        meta: { layout: 'auth' },
    },
    {
        path: '/admin/categories',
        name: 'admin-categories',
        component: () => import(/* webpackChunkName: "admin-tags" */ '../views/admin/categories/index.vue'),
        meta: { layout: 'auth' },
    },
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
    const publicPages = ['/auth/boxed-signin', '/auth/boxed-signup'];
    const authRequired = !publicPages.includes(to.path);

    if (authRequired && !authStore.isLoggedIn) {
        // If there's a token, try to fetch the user
        if (authStore.token) {
            await authStore.fetchUser();
            if (authStore.isLoggedIn) {
                return next();
            }
        }
        return next('/auth/boxed-signin');
    }

    if (to?.meta?.layout == 'auth') {
        store.setMainLayout('auth');
    } else {
        store.setMainLayout('app');
    }
    next(true);
});
router.afterEach((to, from, next) => {
    appSetting.changeAnimation();
});
export default router;
