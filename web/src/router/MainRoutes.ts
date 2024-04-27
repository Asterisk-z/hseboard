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
            name: 'Create HSE Statistics',
            path: '/create-statistics',
            component: () => import('@/views/HSEStatistics/Create.vue')
        },
        {
            name: 'HSE Investigation',
            path: '/hse-investigation',
            component: () => import('@/views/HSEInvestigation/Index.vue')
        },
        {
            name: 'HSE investigating',
            path: '/hse-investigating/:observation_id',
            component: () => import('@/views/HSEInvestigation/Ongoing.vue')
        },
        {
            name: 'HSE investigated',
            path: '/hse-investigated/:investigation_id',
            component: () => import('@/views/HSEInvestigation/Done.vue')
        },
        {
            name: 'Audit Management',
            path: '/hse-audit',
            component: () => import('@/views/HSEAudit/Index.vue')
        },
        {
            name: 'Audit Document',
            path: '/hse-audit-documents',
            component: () => import('@/views/HSEAudit/Document.vue')
        },
        {
            name: 'Audit Template',
            path: '/hse-audit-templates',
            component: () => import('@/views/HSEAudit/Template.vue')
        },
        {
            name: ' Create Audit ',
            path: '/create-hse-audit-report',
            component: () => import('@/views/HSEAudit/Create.vue')
        },
        {
            name: ' Ongoing Audit ',
            path: '/ongoing-hse-audit-report/:main_audit_id',
            component: () => import('@/views/HSEAudit/Ongoing.vue')
        },
        {
            name: ' View Audit ',
            path: '/view-hse-audit-report/:main_audit_id',
            component: () => import('@/views/HSEAudit/View.vue')
        },
        {
            name: ' Report Audit ',
            path: '/report-hse-audit-report/:main_audit_id',
            component: () => import('@/views/HSEAudit/Report.vue')
        },
        {
            name: 'Full Report Audit ',
            path: '/full-report-hse-audit-report/:main_audit_id',
            component: () => import('@/views/HSEAudit/FullReport.vue')
        },
        {
            name: 'OSHA Compliance',
            path: '/osha-compliance',
            component: () => import('@/views/OSHACompliance/Index.vue')
        },
        {
            name: 'Facility Inspection',
            path: '/inspections',
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


