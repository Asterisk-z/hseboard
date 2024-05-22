import { defineStore } from 'pinia';

import { fetchWrapper } from '@/utils/helpers/fetch-wrapper';

export const useOpenLinksStore = defineStore({
    id: 'openLinks',
    state: () => ({
        industries: null as null || [],
        accountTypes: null as null || [],
        accountRoles: null as null || [],
        countries: null as null || [],
        priorities: null as null || [],
        observationTypes: null as null || [],
        notifications: null as null || [],
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
        async getAccountRoles() {
            try {

                const data = await fetchWrapper
                    .get(`account-roles`)
                    .then((response: any) => {
                        return response.data
                    }).catch((error: any) => {
                        console.log(error)
                    });
                this.accountRoles = data;
            } catch (error) {

            }

        },
        async getObservationTypes() {
            try {

                const data = await fetchWrapper
                    .get(`observation-types`)
                    .then((response: any) => {
                        return response.data
                    }).catch((error: any) => {
                        console.log(error)
                    });
                this.observationTypes = data;
            } catch (error) {

            }

        },
        async getPriorities() {
            try {

                const data = await fetchWrapper
                    .get(`priorities`)
                    .then((response: any) => {
                        return response.data
                    }).catch((error: any) => {
                        console.log(error)
                    });
                this.priorities = data;
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
        async getNotifications() {
            // this.countries = { loading: true };
            const data = await fetchWrapper
                .get(`notifications`)
                .then((response: any) => {
                    return response.data
                }).catch((error: any) => {
                    console.log(error)
                });
            this.notifications = data;
        },
    }
});
