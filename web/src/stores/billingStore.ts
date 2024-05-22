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

export const useBillingStore = defineStore({
    id: 'billing',
    state: () => ({
        // initialize state from local storage to enable user to stay logged in
        // @ts-ignore
        transaction: null as null || [],
        all_transactions: null as null || [],
        all_org_feature: null as null || [],
        plans: null as null || [],
        all_features: null as null || [],
        currencies: null as null || [],

        questions: null as null || [],
        
    }),
    getters: {
        
    },
    actions: {
        async initiatePayment(values: any) {
            try {
                const data = await fetchWrapper.post(`billing/initiate`, values)
                    .catch((error: any) => {
                        throw error;
                    }).then((response: any) => {
                        return response
                    })

                return toastWrapper.success(data?.message, data)
            } catch (error: any) {
                return toastWrapper.error(error, error)
            }

        },
        async verifyPayment(values: any) {
            try {
                const organizationStore = useOrganizationStore();
                const data = await fetchWrapper.post(`billing/verify/${organizationStore.active}`, values)
                    .catch((error: any) => {
                        throw error;
                    }).then((response: any) => {
                        return response
                    })

                return toastWrapper.success(data?.message, data)
            } catch (error: any) {
                return toastWrapper.error(error, error)
            }

        },
        async getTransaction(value: string) {
            const data = await fetchWrapper
                .get(`billing/transaction/${value}`)
                .then((response: any) => {
                    return response.data
                }).catch((error: any) => {
                    console.log(error)
                });
            this.transaction = data;
        },
        async getAllTransaction() {
            const organizationStore = useOrganizationStore();
            const data = await fetchWrapper
                .get(`billing/all-transactions/${organizationStore.active}`)
                .then((response: any) => {
                    return response.data
                }).catch((error: any) => {
                    console.log(error)
                });
            this.all_transactions = data;
        },
        async getAllOrganizationFeatures() {

            const organizationStore = useOrganizationStore();
            const data = await fetchWrapper
                .get(`billing/organization-features/${organizationStore.active}`)
                .then((response: any) => {
                    return response.data
                }).catch((error: any) => {
                    console.log(error)
                });
            this.all_org_feature = data;
        },
        async getAllPlans() {

            const organizationStore = useOrganizationStore();
            const data = await fetchWrapper
                .get(`billing/plans/${organizationStore.active}`)
                .then((response: any) => {
                    return response.data
                }).catch((error: any) => {
                    console.log(error)
                });
            this.plans = data;
        },
        async getAllFeatures() {

            const organizationStore = useOrganizationStore();
            const data = await fetchWrapper
                .get(`billing/features/${organizationStore.active}`)
                .then((response: any) => {
                    return response.data
                }).catch((error: any) => {
                    console.log(error)
                });
            this.all_features = data;
        },
        async getAllCurrencies() {

            const organizationStore = useOrganizationStore();
            const data = await fetchWrapper
                .get(`billing/currencies/${organizationStore.active}`)
                .then((response: any) => {
                    return response.data
                }).catch((error: any) => {
                    console.log(error)
                });
            this.currencies = data;
        },


    }
});
