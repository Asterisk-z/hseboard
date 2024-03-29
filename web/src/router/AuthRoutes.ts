import ResetInitiate from '@/views/authentication/ResetInitiate.vue';
import ResetOTP from '@/views/authentication/ResetOTP.vue';
import ResetSetPassword from '@/views/authentication/ResetSetPassword.vue';

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
        {
            name: 'Forgot Password',
            path: '/auth/initiate-reset-password',
            component: ResetInitiate
        },
        {
            name: 'Forgot Password OTP Login',
            path: '/auth/otp-reset-password',
            component: ResetOTP
        },
        {
            name: 'Forgot Password Set',
            path: '/auth/set-reset-password',
            component: ResetSetPassword
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
