import { defineStore } from 'pinia';
import { router } from '@/router';
import { fetchWrapper } from '@/utils/helpers/fetch-wrapper';
import { toastWrapper } from '@/utils/helpers/toast-wrapper';
import { useAuthStore } from '@/stores/auth';

export const useDashboardStore = defineStore({
    id: 'dashboard',
    state: () => ({
        data: null as null || [],
    }),
    actions: {
        async getData() {

            const data = await fetchWrapper
                .get(`dashboard`)
                .then((response: any) => {
                    return response.data
                }).catch((error: any) => {
                    console.log(error)
                });
            this.data = data;
        },
    }
});
 