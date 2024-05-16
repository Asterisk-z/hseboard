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

export const useInspectionStore = defineStore({
    id: 'inspection',
    state: () => ({
        // initialize state from local storage to enable user to stay logged in
        // @ts-ignore
        orgInspections: null as null || [],
        inspectionTypes: null as null || [],
        inspectionTemplateTypes: null as null || [],
        inspectionTypeTemplates: null as null || [],
        ongoingInspection: null as null || [],
        inspectingMembers: null as null || [],
        inspecteeMembers: null as null || [],
        completedInspection: null as null || [],




        questions: null as null || [],
        
    }),
    getters: {
        
    },
    actions: {
        async getInspectionTypes() {
            const data = await fetchWrapper
                .get(`inspection/types`)
                .then((response: any) => {
                    return response.data
                }).catch((error: any) => {
                    console.log(error)
                });
            this.inspectionTypes = data;
        },
        async getInvestigationTemplateTypes() {
            const data = await fetchWrapper
                .get(`inspection/template-types`)
                .then((response: any) => {
                    return response.data
                }).catch((error: any) => {
                    console.log(error)
                });
            this.inspectionTemplateTypes = data;
        },
        async getInspectionTemplateForTypeId(type_id: string) {
            this.inspectionTypeTemplates = [];
            const organizationStore = useOrganizationStore();
            const data = await fetchWrapper
                .get(`inspection/templates-type/${organizationStore.active}/${type_id}`)
                .then((response: any) => {
                    return response.data
                }).catch((error: any) => {
                    console.log(error)
                });
            this.inspectionTypeTemplates = data;
        },
        async initiateInspection(values: any) {
            try {
                const data = await fetchWrapper.post(`inspection/initiate`, values)
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
        async getAllOrgInspections() {

            const data = await fetchWrapper
                .get(`inspection/all`)
                .then((response: any) => {
                    return response.data
                }).catch((error: any) => {
                    console.log(error)
                });
            this.orgInspections = data;
        },
        async getOngoingInspection(item: string) {
            // this.mainAudit = [];
            const data = await fetchWrapper
                .get(`inspection/ongoing/${item}`)
                .then((response: any) => {
                    return response.data
                }).catch((error: any) => {
                    console.log(error)
                });
            this.ongoingInspection = data;
        },
        async getInspectingMembers(item: string) {

            const data = await fetchWrapper
                .get(`inspection/inspectors/${item}`)
                .then((response: any) => {
                    return response.data
                }).catch((error: any) => {
                    console.log(error)
                });
            this.inspectingMembers = data;
        },

        async getInspecteeMembers(item: string) {

            const data = await fetchWrapper
                .get(`inspection/inspectees/${item}`)
                .then((response: any) => {
                    return response.data
                }).catch((error: any) => {
                    console.log(error)
                });
            this.inspecteeMembers = data;
        },
        async setInspectors(values: any) {
            try {
                const organizationStore = useOrganizationStore();
                const data = await fetchWrapper.post(`inspection/set-inspectors/${organizationStore.active}`, values)
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
        async setRepresentatives(values: any) {
            try {
                const organizationStore = useOrganizationStore();
                const data = await fetchWrapper.post(`inspection/set-representatives/${organizationStore.active}`, values)
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
        async removeMember(values: any) {
            try {
                const organizationStore = useOrganizationStore();
                const data = await fetchWrapper.post(`inspection/remove-member/${organizationStore.active}`, values)
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
        async updateDetailInspection(values: any) {
            try {
                const data = await fetchWrapper.post(`inspection/update-base`, values)
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
        async proceedInspection(values: any) {
            try {
                const organizationStore = useOrganizationStore();
                const data = await fetchWrapper.post(`inspection/proceed/${organizationStore.active}`, values)
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
        async proposeTime(values: any) {
            try {
                const organizationStore = useOrganizationStore();
                const data = await fetchWrapper.post(`inspection/propose-time/${organizationStore.active}`, values)
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
        async actionTime(values: any) {
            try {
                const organizationStore = useOrganizationStore();
                const data = await fetchWrapper.post(`inspection/action-time/${organizationStore.active}`, values)
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
        async sendQuestionResponse(values: any) {
            try {
                const organizationStore = useOrganizationStore();
                const data = await fetchWrapper.post(`inspection/send-response/${organizationStore.active}`, values)
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
        async sendQuestionComment(values: any) {
            try {
                const organizationStore = useOrganizationStore();
                const data = await fetchWrapper.post(`inspection/send-comment/${organizationStore.active}`, values)
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
        async sendFindings(values: any) {
            try {
                const organizationStore = useOrganizationStore();
                const data = await fetchWrapper.post(`inspection/send-finding/${organizationStore.active}`, values)
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
        async completeInspection(values: any) {
            try {
                const organizationStore = useOrganizationStore();
                const data = await fetchWrapper.post(`inspection/complete/${organizationStore.active}`, values)
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

        async actionInspection(values: any) {
            try {
                const organizationStore = useOrganizationStore();
                const data = await fetchWrapper.post(`inspection/action/${organizationStore.active}`, values)
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
        async sendRecommendation(values: any) {
            try {
                const organizationStore = useOrganizationStore();
                const data = await fetchWrapper.post(`inspection/send-recommendation/${organizationStore.active}`, values)
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
        async getCompletedInspection(item: string) {
            // this.mainAudit = [];
            const data = await fetchWrapper
                .get(`inspection/completed/${item}`)
                .then((response: any) => {
                    return response.data
                }).catch((error: any) => {
                    console.log(error)
                });
            this.completedInspection = data;
        },













        async sendDocument(values: any) {
            try {
                
                const user = useAuthStore();
                const data = await axios({
                    method: 'post',
                    url: `${import.meta.env.VITE_API_LINK}inspection/send-document`,
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

    }
});
