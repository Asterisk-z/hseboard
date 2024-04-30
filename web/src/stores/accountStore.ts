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




































    }
});
