const MainRoutes = {
    path: '/main',
    meta: {
        requiresAuth: true
    },
    redirect: '/main',
    component: () => import('@/layouts/full/FullLayout.vue'),
    children: [
        {
            path: '/',
            redirect: '/dashboard'
        },
        {
            name: 'Dashboard',
            path: '/dashboard',
            component: () => import('@/views/StarterPage.vue')
        },
    ]
};

export default MainRoutes;
