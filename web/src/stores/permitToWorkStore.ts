import { defineStore } from 'pinia';
import { router } from '@/router';
import { fetchWrapper } from '@/utils/helpers/fetch-wrapper';
import { toastWrapper } from '@/utils/helpers/toast-wrapper';
import { useOrganizationStore } from './organizationStore';
import { useAuthStore } from '@/stores/auth';
import axios from 'axios';


type startMainAuditType = {
    organization_id: string,
    observation_id: string,
};

type MainAuditType = {
    organization_id: string,
    title: string,
    description: string,
    assignee_id: string,
    start_date: string,
    end_date: string,
    priority_id: string,
};
type editMainAuditType = {
    organization_id: string,
    title: string,
    description: string,
    start_date: string,
    end_date: string,
    priority_id: string,
    MainAudit_id: string,
};

type removeMainAuditType = {
    MainAudit_id: string,
};
type statusMainAuditType = {
    MainAudit_id: string,
    status: string,
    message?: string,
};

export const usePermitToWorkStore = defineStore({
    id: 'permitToWork',
    state: () => ({
        // initialize state from local storage to enable user to stay logged in
        // @ts-ignore
        permitTypes: null as null || [],
        permitRequestTypes: null as null || [],
        holdingMembers: null as null || [],
        userInvolvedPermit: null as null || [],
        activeJHA: null as null || [],




        orgInspections: null as null || [],
        inspectionTypes: null as null || [],
        inspectionTemplateTypes: null as null || [],
        inspectionTypeTemplates: null as null || [],
        acceptedPermit: null as null || [],
        inspectingMembers: null as null || [],
        inspecteeMembers: null as null || [],
        completedInspection: null as null || [],
        
    }),
    getters: {
        
    },
    actions: {
        async getPermitTypes() {
            const data = await fetchWrapper
                .get(`permit-to-work/types`)
                .then((response: any) => {
                    return response.data
                }).catch((error: any) => {
                    console.log(error)
                });
            this.permitTypes = data;
        },
        async getPermitRequestTypes() {
            const data = await fetchWrapper
                .get(`permit-to-work/request-types`)
                .then((response: any) => {
                    return response.data
                }).catch((error: any) => {
                    console.log(error)
                });
            this.permitRequestTypes = data;
        },
        async initiatePermit(values: any) {
            try {
                const data = await fetchWrapper.post(`permit-to-work/create-permit`, values)
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
        async updatePermit(values: any) {
            try {
                const data = await fetchWrapper.post(`permit-to-work/update-permit`, values)
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
        async getAcceptedPermit(item: string) {
            // this.mainAudit = [];
            const organizationStore = useOrganizationStore();
            const data = await fetchWrapper
                .get(`permit-to-work/ongoing/${organizationStore.active}/${item}`)
                .then((response: any) => {
                    return response.data
                }).catch((error: any) => {
                    console.log(error)
                });
            this.acceptedPermit = data;
        },
        async getHoldingMembers(item: string) {
            // this.holdingMembers = [];
            const data = await fetchWrapper
                .get(`permit-to-work/holding-members/${item}`)
                .then((response: any) => {
                    return response.data
                }).catch((error: any) => {
                    console.log(error)
                });
            this.holdingMembers = data;
        },
        async setMembers(values: any) {
            try {
                const organizationStore = useOrganizationStore();
                const data = await fetchWrapper.post(`permit-to-work/set-members/${organizationStore.active}`, values)
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
        async getAllUserInvolvedPermit() {

            const data = await fetchWrapper
                .get(`permit-to-work/all`)
                .then((response: any) => {
                    return response.data
                }).catch((error: any) => {
                    console.log(error)
                });
            this.userInvolvedPermit = data;
        },
        async removeMember(values: any) {
            try {
                const organizationStore = useOrganizationStore();
                const data = await fetchWrapper.post(`permit-to-work/remove-member/${organizationStore.active}`, values)
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
        async getActiveJHA() {
            // this.holdingMembers = [];
            const data = await fetchWrapper
                .get(`permit-to-work/active-jha`)
                .then((response: any) => {
                    return response.data
                }).catch((error: any) => {
                    console.log(error)
                });
            this.activeJHA = data;
        },
        async sendJHA(values: any) {
            try {
                const organizationStore = useOrganizationStore();
                const data = await fetchWrapper.post(`permit-to-work/send-jha/${organizationStore.active}`, values)
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
        async actionJHA(values: any) {
            try {
                const organizationStore = useOrganizationStore();
                const data = await fetchWrapper.post(`permit-to-work/action-jha/${organizationStore.active}`, values)
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
        async actionRequestForm(values: any) {
            try {
                const organizationStore = useOrganizationStore();
                const data = await fetchWrapper.post(`permit-to-work/action-request-form/${organizationStore.active}`, values)
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
        async sendForDeclaration(values: any) {
            try {
                const organizationStore = useOrganizationStore();
                const data = await fetchWrapper.post(`permit-to-work/action-declaration/${organizationStore.active}`, values)
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
        async sendDeclaration(values: any) {
            try {
                const organizationStore = useOrganizationStore();
                const data = await fetchWrapper.post(`permit-to-work/send-declaration/${organizationStore.active}`, values)
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
        async sendIssuePermit(values: any) {
            try {
                const organizationStore = useOrganizationStore();
                const data = await fetchWrapper.post(`permit-to-work/send-issue-permit/${organizationStore.active}`, values)
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
        async suspendPermit(values: any) {
            try {
                const organizationStore = useOrganizationStore();
                const data = await fetchWrapper.post(`permit-to-work/suspend-permit/${organizationStore.active}`, values)
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
        async reactivatePermit(values: any) {
            try {
                const organizationStore = useOrganizationStore();
                const data = await fetchWrapper.post(`permit-to-work/reactivate-permit/${organizationStore.active}`, values)
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
        async sendPermitRequest(values: any) {
            try {
                const organizationStore = useOrganizationStore();
                const data = await fetchWrapper.post(`permit-to-work/send-permit-request/${organizationStore.active}`, values)
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
        async actionPermitRequest(values: any) {
            try {
                const organizationStore = useOrganizationStore();
                const data = await fetchWrapper.post(`permit-to-work/action-permit-request/${organizationStore.active}`, values)
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
