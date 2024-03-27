import { defineStore } from 'pinia';

import { fetchWrapper } from '@/utils/helpers/fetch-wrapper';

export const useOpenLinksStore = defineStore({
    id: 'openLinks',
    state: () => ({
        industries: null as null || [],
        accountTypes:  null as null || [],
        countries:  null as null || [],
    }),
    actions: {
        async getIndustries() {
            // this.industries = { loading: true };
            const data = await fetchWrapper
                .get(`industries`)
                .then((response: any) => {
                    return response.data
                }).catch((error: any) => {
                    console.log(error)
                });
            this.industries = data;
        },
        async getAccountTypes() {
            try {
                
                const data = await fetchWrapper
                    .get(`account-types`)
                    .then((response: any) => {
                        return response.data
                    }).catch((error: any) => {
                        console.log(error)
                    });
                this.accountTypes = data;
            } catch (error) {

            }
            
        },
        async getCountries() {
            // this.countries = { loading: true };
            const data = await fetchWrapper
                .get(`countries`)
                .then((response: any) => {
                    return response.data
                }).catch((error: any) => {
                    console.log(error)
                });
            this.countries = data;
        },
    }
});
