import { defineStore } from 'pinia';
import { router } from '@/router';
import { fetchWrapper } from '@/utils/helpers/fetch-wrapper';
import { toastWrapper } from '@/utils/helpers/toast-wrapper';
import { useOrganizationStore } from './organizationStore';


type sendOfferType = {
    type: string,
    organization_id: string,
    message: string,
    recipient_email?: string,
};

type actionOfferType = {
    offer_id: string,
    status: string
};


export const useOfferStore = defineStore({
    id: 'offer',
    state: () => ({
        // initialize state from local storage to enable user to stay logged in
        // @ts-ignore
        offers: null as null || [],
    }),
    getters: {
        
    },
    actions: {
        async getOffers() {
            const data = await fetchWrapper
                .get(`offers`)
                .then((response: any) => {
                    return response.data
                }).catch((error: any) => {
                    console.log(error)
                });
            this.offers = data;
        },
        async sendOffer(values: sendOfferType) {
            try {
                const data = await fetchWrapper.post(`offers/send`, values)
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
        async actionOffer(values: actionOfferType) {
            try {
                const data = await fetchWrapper.post(`offers/action-offer`, values)
                    .catch((error: any) => {
                        throw error;
                    }).then((response: any) => {
                        return response
                    })

                return toastWrapper.success(data?.message, data)
            } catch (error: any) {
                return toastWrapper.error(error, error)
            }

        }
    }
});
