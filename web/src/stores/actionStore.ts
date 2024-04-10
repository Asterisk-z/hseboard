import { defineStore } from 'pinia';
import { router } from '@/router';
import { fetchWrapper } from '@/utils/helpers/fetch-wrapper';
import { toastWrapper } from '@/utils/helpers/toast-wrapper';
import { useOrganizationStore } from './organizationStore';


type actionType = {
    organization_id: string,
    title: string,
    description: string,
    assignee_id: string,
    start_date: string,
    end_date: string,
    priority_id: string,
};
type editActionType = {
    organization_id: string,
    title: string,
    description: string,
    start_date: string,
    end_date: string,
    priority_id: string,
    action_id: string,
};

type removeActionType = {
    action_id: string,
};
type statusActionType = {
    action_id: string,
    status: string,
    message?: string,
};

export const useActionStore = defineStore({
    id: 'action',
    state: () => ({
        // initialize state from local storage to enable user to stay logged in
        // @ts-ignore
        actions: null as null || [],
    }),
    getters: {
        
    },
    actions: {
        async getActions() {
            
            const organizationStore = useOrganizationStore();
            const data = await fetchWrapper
                .get(`actions/all/${organizationStore.active}`)
                .then((response: any) => {
                    return response.data
                }).catch((error: any) => {
                    console.log(error)
                });
            this.actions = data;
        },
        async addAction(values: actionType) {
            try {
                const data = await fetchWrapper.post(`actions/create`, values)
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
        async editAction(values: editActionType) {
            try {
                const data = await fetchWrapper.post(`actions/update`, values)
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
        async deleteAction(values: removeActionType) {
            try {
                const data = await fetchWrapper.post(`actions/delete`, values)
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
        async statusUpdate(values: statusActionType) {
            try {
                const data = await fetchWrapper.post(`actions/status-update`, values)
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
