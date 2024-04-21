import { defineStore } from 'pinia';
import { router } from '@/router';
import { fetchWrapper } from '@/utils/helpers/fetch-wrapper';
import { toastWrapper } from '@/utils/helpers/toast-wrapper';
import { useOrganizationStore } from './organizationStore';
import axios from 'axios';
import { useAuthStore } from '@/stores/auth';


export const useAuditTemplateStore = defineStore({
    id: 'auditTemplate',
    state: () => ({
        // initialize state from local storage to enable user to stay logged in
        // @ts-ignore
        auditTypeTemplates: null as null || [],
        auditTemplates: null as null || [],
        auditTypes: null as null || [],
        auditOptions: null as null || [],
    }),
    getters: {
        
    },
    actions: {
        async getAuditOptions() {
            const data = await fetchWrapper
                .get(`audit-options/all`)
                .then((response: any) => {
                    return response.data
                }).catch((error: any) => {
                    console.log(error)
                });
            this.auditOptions = data;
        },
        async getAuditTypes() {
            const data = await fetchWrapper
                .get(`audit-types/all`)
                .then((response: any) => {
                    return response.data
                }).catch((error: any) => {
                    console.log(error)
                });
            this.auditTypes = data;
        },
        async getAuditTemplates() {

            const organizationStore = useOrganizationStore();
            const data = await fetchWrapper
                .get(`audit-templates/all/${organizationStore.active}`)
                .then((response: any) => {
                    return response.data
                }).catch((error: any) => {
                    console.log(error)
                });
            this.auditTemplates = data;
        },
        async getAuditTypeTemplate(type_id: number) {

            const organizationStore = useOrganizationStore();
            const data = await fetchWrapper
                .get(`audit-templates/single/${organizationStore.active}/${type_id}`)
                .then((response: any) => {
                    return response.data
                }).catch((error: any) => {
                    console.log(error)
                });
            return data;
        },
        async addAuditTemplate(values: any) {
            try {
                
                // const user = useAuthStore();
                // const data = await axios({
                //     method: 'post',
                //     url: `${import.meta.env.VITE_API_LINK}audit-templates/upload`,
                //     data: values,
                //     headers: { 
                //         'Content-Type': "multipart/form-data; charset=utf-8; boundary=" + Math.random().toString().substr(2),
                //         "Authorization": `Bearer ${user.accessToken}`,
                //         "Accept": "Application/json",
                //      },
                // }).catch((error: any) => {
                //         throw error;
                //     }).then((response: any) => {
                //         return response
                //     })
                const data = await fetchWrapper.post(`audit-templates/upload`, values)
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
        async removeAuditTemplate(values: any) {
            try {
                const data = await fetchWrapper.post(`audit-templates/delete`, values)
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
    }
});
