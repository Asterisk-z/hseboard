import { defineStore } from 'pinia';
import { router } from '@/router';
import { fetchWrapper } from '@/utils/helpers/fetch-wrapper';
import { toastWrapper } from '@/utils/helpers/toast-wrapper';
import { useOrganizationStore } from './organizationStore';
import axios from 'axios';
import { useAuthStore } from '@/stores/auth';


export const useAuditDocumentStore = defineStore({
    id: 'auditDocument',
    state: () => ({
        // initialize state from local storage to enable user to stay logged in
        // @ts-ignore
        auditDocuments: null as null || [],
    }),
    getters: {
        
    },
    actions: {
        async getAuditDocuments() {
            
            const data = await fetchWrapper
                .get(`audit-documents/all`)
                .then((response: any) => {
                    return response.data
                }).catch((error: any) => {
                    console.log(error)
                });
            this.auditDocuments = data;
        },
        async addAuditDocument(values: any) {
            try {
                const user = useAuthStore();
                const data = await axios({
                    method: 'post',
                    url: `${import.meta.env.VITE_API_LINK}audit-documents/upload`,
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
                // const data = await fetchWrapper.post(`audit-documents/upload`, values)
                //     .catch((error: any) => {
                //         throw error;
                //     }).then((response: any) => {
                //         return response
                //     })

                return toastWrapper.success(data?.message, data)
            } catch (error: any) {
                return toastWrapper.error(error, error)
            }

        },
        async removeAuditDocument(values: any) {
            try {
                const data = await fetchWrapper.post(`audit-documents/delete`, values)
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
