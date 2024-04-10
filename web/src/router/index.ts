import { createRouter, createWebHistory } from 'vue-router';
import MainRoutes from './MainRoutes';
import AuthRoutes from './AuthRoutes';
import { useAuthStore } from '@/stores/auth';

export const router = createRouter({
    history: createWebHistory(import.meta.env.BASE_URL),
    routes: [
        {
            path: '/:pathMatch(.*)*',
            component: () => import('@/views/authentication/Error.vue')
        },
        MainRoutes,
        AuthRoutes
    ]
});

router.beforeEach(async (to, from, next) => {
    // redirect to login page if not logged in and trying to access a restricted page
    const publicPages = ['/auth/login'];
    const authRequired = !publicPages.includes(to.path);
    const auth: any = useAuthStore();

    // console.log(to, publicPages, authRequired, auth.user)
    if (to.matched.some((record) => record.meta.requiresAuth)) {
        if (authRequired && !auth.user && !auth.accessToken) {
            auth.returnUrl = to.fullPath; 
            return next('/auth/login');
        } else next();
    } else {
        next();
    }
});

router.afterEach((to, from, failure) => {
    if (failure) console.log(`Failed to enter ${to.fullPath}`, failure)

    const auth: any = useAuthStore();

    if (auth.user || auth.hse_tok_passer) {
        const twentyMin = 1200000;
        setInterval(() => {
            auth.refresh();
        }, twentyMin)
    }


})
