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
            component: () => import('@/views/Dashboard.vue')
        },
        {
            name: 'Team Member',
            path: '/team-member',
            component: () => import('@/views/Team/Index.vue')
        },
        {
            name: 'Requests Team Member',
            path: '/organization-requests',
            component: () => import('@/views/Team/Request.vue')
        },
        {
            name: 'Inbox',
            path: '/inbox',
            component: () => import('@/views/Chat/Index.vue')
        },
        {
            name: 'Notifications',
            path: '/notifications',
            component: () => import('@/views/Notification/Index.vue')
        },
        {
            name: 'Observations',
            path: '/observations',
            component: () => import('@/views/Observation/Index.vue'),
        },
        {
            name: 'Create Observation',
            path: '/create-observation',
            component: () => import('@/views/Observation/Create.vue')
        },
        {
            name: 'Actions',
            path: '/actions',
            component: () => import('@/views/Action/Index.vue')
        },
        {
            name: 'HSE Statistics',
            path: '/hse-statistics',
            component: () => import('@/views/HSEStatistics/Index.vue')
        },
        {
            name: 'HSE Investigation',
            path: '/hse-investigation',
            component: () => import('@/views/HSEInvestigation/Index.vue')
        },
        {
            name: 'HSE Audit',
            path: '/hse-audit',
            component: () => import('@/views/HSEAudit/Index.vue')
        },
        {
            name: 'OSHA Compliance',
            path: '/osha-compliance',
            component: () => import('@/views/OSHACompliance/Index.vue')
        },
        {
            name: 'Facility Inspection',
            path: '/inspection',
            component: () => import('@/views/Inspection/Index.vue')
        },
        {
            name: 'Job Hazard Analysis',
            path: '/hazard-analysis',
            component: () => import('@/views/JobAnalysis/Index.vue')
        },
        {
            name: 'Permit to Work System',
            path: '/work-permit',
            component: () => import('@/views/WorkPermit/Index.vue')
        },
        {
            name: 'Billing',
            path: '/billing',
            component: () => import('@/views/Billing/Index.vue')
        },
        {
            name: 'Account Setting',
            path: '/account-setting',
            component: () => import('@/views/Settings/Account.vue')
        },
        {
            name: 'Business Setting',
            path: '/business-setting',
            component: () => import('@/views/Settings/Business.vue')
        },
    ]
};

export default MainRoutes;


