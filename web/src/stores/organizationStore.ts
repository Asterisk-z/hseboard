import { defineStore } from 'pinia';
import { router } from '@/router';
import { fetchWrapper } from '@/utils/helpers/fetch-wrapper';
import { toastWrapper } from '@/utils/helpers/toast-wrapper';
import { useAuthStore } from '@/stores/auth';
import axios from 'axios';

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
            return JSON.parse(atob(state.list as any))
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
        async getOrganizations() {

            const data = await fetchWrapper
                .get(`organizations/${this.active}`)
                .then((response: any) => {
                    return response.data
                }).catch((error: any) => {
                    console.log(error)
                });
            this.organization = data;
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
        async getOrganizationUsers(org_id: string) {

            const data = await fetchWrapper
                .get(`organizations/users/${org_id}`)
                .then((response: any) => {
                    return response.data
                }).catch((error: any) => {
                    console.log(error)
                });
            return data;
        },

        async updateDetails(value: any) {
            try {

                const authStore = useAuthStore();
                const data = await fetchWrapper.post(`organizations/update-details`, value)
                    .catch((error: any) => {
                        throw error;
                    }).then((response: any) => {
                        authStore.refresh()
                        return response
                    })

                return toastWrapper.success(data?.message, data)


            } catch (error: any) {
                return toastWrapper.error(error, error)
            }

        },
        async getActiveOrganization(value: any) {
            try {

                if (this.active == value?.organization_id) return;

                const authStore = useAuthStore();
                const data = await fetchWrapper.post(`profile/set-organization`, value)
                    .catch((error: any) => {
                        throw error;
                    }).then((response: any) => {
                        authStore.refresh()
                        return response
                    })
                
                // toastWrapper.success(data?.message, data)
                // this.setActiveOrg(value?.organization_id)
                // window.location.href = `${import.meta.env.VITE_API_WEB}dashboard`

                // return toastWrapper.success(data?.message, data)
                
            } catch (error: any) {
                return toastWrapper.error(error, error)
            }

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
        },
        async uploadLogo(values: any) {
            try {

                const user = useAuthStore();
                const data = await axios({
                    method: 'post',
                    url: `${import.meta.env.VITE_API_LINK}organizations/upload-logo`,
                    data: values,
                    headers: {
                        'Content-Type': "multipart/form-data; charset=utf-8; boundary=" + Math.random().toString().substr(2),
                        "Authorization": `Bearer ${user.accessToken}`,
                        "Accept": "Application/json",
                    },
                }).catch((error: any) => {
                    throw error;
                }).then((response: any) => {

                    user.refresh('business-setting');
                    return response
                })

                return toastWrapper.success(data?.message, data)
            } catch (error: any) {
                return toastWrapper.error(error, error)
            }

        },
    }
});
 