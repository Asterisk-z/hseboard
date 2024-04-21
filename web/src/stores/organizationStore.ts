import { defineStore } from 'pinia';
import { router } from '@/router';
import { fetchWrapper } from '@/utils/helpers/fetch-wrapper';
import { toastWrapper } from '@/utils/helpers/toast-wrapper';

export const useOrganizationStore = defineStore({
    id: 'organization',
    state: () => ({
        // initialize state from local storage to enable user to stay logged in
        // @ts-ignore
        list: localStorage.getItem('loggerOrg'),
        active: localStorage.getItem('loggerActiveOrg'),
        organizations: null as null || [],
        organization: null as null || [],
    }),
    getters: {
        loggedUserOrgs: (state) => {
            return JSON.parse(atob(state.list))
        },
    },
    actions: {
        async getAllOrganizations() {

            const data = await fetchWrapper
                .get(`organizations`)
                .then((response: any) => {
                    return response.data
                }).catch((error: any) => {
                    console.log(error)
                });
            this.organizations = data;
        },
        async getTokenOrganizations(org_token: string) {

            const data = await fetchWrapper
                .get(`organizations/org_token/${org_token}/${this.active}`)
                .then((response: any) => {
                    return response.data
                }).catch((error: any) => {
                    console.log(error)
                });
           return data;
        },
        getActiveOrg() {
            try {
                let activeOrg;
                if (this.loggedUserOrgs.length < 1) return;
                if (this.active) {
                    activeOrg = this.loggedUserOrgs?.find((items: any) => items.uuid == this.active)

                } else {
                    activeOrg = this.loggedUserOrgs[0]
                }
                if(activeOrg) this.setActiveOrg(activeOrg?.uuid)
                return activeOrg;
            } catch (error: any) {
                console.log(error)
                toastWrapper.error(error, error)
                return false;
            } 
        },
        changeActiveOrg(uuid: string) {
            try {
                this.setActiveOrg(uuid)
                this.getActiveOrg();
                window.location.reload()
            } catch (error: any) {
                toastWrapper.error(error, error)
                return false;
            }
        },
        setActiveOrg(uuid: string) {
                localStorage.setItem('loggerActiveOrg', uuid);
                this.active = localStorage.getItem('loggerActiveOrg');
        }
    }
});
 