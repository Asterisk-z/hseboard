const AuthRoutes = {
    path: '/auth',
    component: () => import('@/layouts/blank/BlankLayout.vue'),
    meta: {
        requiresAuth: false
    },
    children: [
        {
            name: 'Authentication Login',
            path: '/auth/login',
            component: () => import('@/views/authentication/Login.vue')
        },
        {
            name: 'Authentication Register',
            path: '/auth/register',
            component: () => import('@/views/authentication/Register.vue')
        },
        // {
        //     name: 'Side Login',
        //     path: '/auth/login',
        //     component: () => import('@/views/authentication/SideLogin.vue')
        // },
        {
            name: 'Error',
            path: '/auth/404',
            component: () => import('@/views/authentication/Error.vue')
        },
    ]
};

export default AuthRoutes;
