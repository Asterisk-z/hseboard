import { defineStore } from 'pinia';
import { router } from '@/router';
import { fetchWrapper } from '@/utils/helpers/fetch-wrapper';
import { toastWrapper } from '@/utils/helpers/toast-wrapper';
import { useOrganizationStore } from './organizationStore';
import { useAuthStore } from '@/stores/auth';
import axios from 'axios';


type observationType  = {
    organization_id?: string,
    observation_type: string,
    description: string,
    location_details: string,
    address: string,
    affected_workers?: string,
    date_time: string,
    severity: string,
    images?: any
};
type editObservationType = {
    organization_id?: string,
    observation_type: string,
    description: string,
    location_details: string,
    address: string,
    affected_workers?: string,
    date_time: string,
    severity: string,
    observation_id: string,
};

type removeObservationType = {
    observation_id: string,
};

export const useObservationStore = defineStore({
    id: 'observation',
    state: () => ({
        // initialize state from local storage to enable user to stay logged in
        // @ts-ignore
        observations: null as null || [],
    }),
    getters: {
        
    },
    actions: {
        async getObservations() {
            
            const data = await fetchWrapper
                .get(`observations/all`)
                .then((response: any) => {
                    return response.data
                }).catch((error: any) => {
                    console.log(error)
                });
            this.observations = data;
        },
        async addObservation(values: any) {
            try {
                // const data = await fetchWrapper.post(`observations/create`, values)
                //     .catch((error: any) => {
                //         throw error;
                //     }).then((response: any) => {
                //         return response
                //     })

                const user = useAuthStore();
                const data = await axios({
                    method: 'post',
                    url: `${import.meta.env.VITE_API_LINK}observations/create`,
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
        async editObservation(values: editObservationType) {
            try {
                const data = await fetchWrapper.post(`observations/update`, values)
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
        async removeObservation(values: removeObservationType) {
            try {
                const data = await fetchWrapper.post(`observations/delete`, values)
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
