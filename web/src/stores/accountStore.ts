import { defineStore } from 'pinia';
import { router } from '@/router';
import { fetchWrapper } from '@/utils/helpers/fetch-wrapper';
import { toastWrapper } from '@/utils/helpers/toast-wrapper';
import { useOrganizationStore } from './organizationStore';
import { useAuthStore } from '@/stores/auth';
import axios from 'axios';


type startMainAuditType = {
    organization_id: string,
    observation_id: string,
};

type MainAuditType = {
    organization_id: string,
    title: string,
    description: string,
    assignee_id: string,
    start_date: string,
    end_date: string,
    priority_id: string,
};
type editMainAuditType = {
    organization_id: string,
    title: string,
    description: string,
    start_date: string,
    end_date: string,
    priority_id: string,
    MainAudit_id: string,
};

type removeMainAuditType = {
    MainAudit_id: string,
};
type statusMainAuditType = {
    MainAudit_id: string,
    status: string,
    message?: string,
};

export const useAccountStore = defineStore({
    id: 'account',
    state: () => ({
        // initialize state from local storage to enable user to stay logged in
        // @ts-ignore
        userDetail: null as null || [],

    }),
    getters: {
        
    },
    actions: {
        async getUserDetail() {
            const data = await fetchWrapper
                .get(`auth/profile`)
                .then((response: any) => {
                    return response
                }).catch((error: any) => {
                    console.log(error)
                });
            this.userDetail = data;
        },
        async updateToken() {
            const data = await fetchWrapper
                .post(`auth/reset-token`)
                .then((response: any) => {
                    return response
                }).catch((error: any) => {
                    console.log(error)
                });
            this.userDetail = data;
        },
        async updateDetail(values : any) {
            const data = await fetchWrapper
                .post(`auth/update-details`, values)
                .then((response: any) => {
                    return response
                }).catch((error: any) => {
                    console.log(error)
                });
            this.userDetail = data;
        },
        async uploadLogo(values: any) {
            try {

                const user = useAuthStore();
                const data = await axios({
                    method: 'post',
                    url: `${import.meta.env.VITE_API_LINK}auth/upload-logo`,
                    data: values,
                    headers: {
                        'Content-Type': "multipart/form-data; charset=utf-8; boundary=" + Math.random().toString().substr(2),
                        "Authorization": `Bearer ${user.accessToken}`,
                        "Accept": "Application/json",
                    },
                }).catch((error: any) => {
                    throw error;
                }).then((response: any) => {
                    return response
                })

                return toastWrapper.success(data?.message, data)
            } catch (error: any) {
                return toastWrapper.error(error, error)
            }

        },



        
































    }
});
