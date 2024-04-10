import { defineStore } from 'pinia';
import { router } from '@/router';
import { fetchWrapper } from '@/utils/helpers/fetch-wrapper';
import { toastWrapper } from '@/utils/helpers/toast-wrapper';
import { useOrganizationStore } from './organizationStore';


type memberType = {
    firstName: string,
    lastName: string,
    phoneNumber: string,
    emailAddress: string,
    accountRole: string,
    password: string,
    password_confirmation: string,
    organization_id: string,
};
type editMemberType = {
    firstName: string,
    lastName: string,
    phoneNumber: string,
    emailAddress: string,
    accountRole: string,
    user_id: string,
    organization_id: string,
};

type removeMemberType = {
    user_id: string,
    organization_id: string,
};

export const useTeamMemberStore = defineStore({
    id: 'teamMember',
    state: () => ({
        // initialize state from local storage to enable user to stay logged in
        // @ts-ignore
        members: null as null || [],
        membersExceptMe: null as null || [],
    }),
    getters: {
        
    },
    actions: {
        async getTeamMembers() {

            const organizationStore = useOrganizationStore();
            const data = await fetchWrapper
                .get(`users/all/${organizationStore.active}`)
                .then((response: any) => {
                    return response.data
                }).catch((error: any) => {
                    console.log(error)
                });
            this.members = data;
        },
        async getTeamMembersExcept() {

            const organizationStore = useOrganizationStore();
            const data = await fetchWrapper
                .get(`users/except/${organizationStore.active}`)
                .then((response: any) => {
                    return response.data
                }).catch((error: any) => {
                    console.log(error)
                });
            this.membersExceptMe = data;
        },
        async addMember(values: memberType) {
            try {
                const data = await fetchWrapper.post(`users/create`, values)
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
        async editMember(values: editMemberType) {
            try {
                const data = await fetchWrapper.post(`users/update`, values)
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
        async removeMember(values: removeMemberType) {
            try {
                const data = await fetchWrapper.post(`organizations/remove-user`, values)
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
