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

export const useMainAuditStore = defineStore({
    id: 'mainAudit',
    state: () => ({
        // initialize state from local storage to enable user to stay logged in
        // @ts-ignore
        mainAudits: null as null || [],
        completedMainAudit: null as null || [],
        mainAudit: null as null || [],
        auditMembers: null as null || [],
        auditeeMembers: null as null || [],

        questions: null as null || [],
        
    }),
    getters: {
        
    },
    actions: {
        async startMainAudit(values: any) {
            try {
                const data = await fetchWrapper.post(`main-audit/start`, values)
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
        async getOngoingMainAudit(item: string) {
            // this.mainAudit = [];
            const data = await fetchWrapper
                .get(`main-audit/ongoing/${item}`)
                .then((response: any) => {
                    return response.data
                }).catch((error: any) => {
                    console.log(error)
                });
            this.mainAudit = data;
        },
        async getCompletedMainAudit(item: string) {
            // this.mainAudit = [];
            const data = await fetchWrapper
                .get(`main-audit/completed/${item}`)
                .then((response: any) => {
                    return response.data
                }).catch((error: any) => {
                    console.log(error)
                });
            this.completedMainAudit = data;
        },
        async getMainAudits() {

            const organizationStore = useOrganizationStore();
            const data = await fetchWrapper
                .get(`main-audit/all/${organizationStore.active}`)
                .then((response: any) => {
                    return response.data
                }).catch((error: any) => {
                    console.log(error)
                });
            this.mainAudits = data;
        },


        async getAuditMembers(item: string) {
            this.auditMembers = [];
            const data = await fetchWrapper
                .get(`main-audit/auditors/${item}`)
                .then((response: any) => {
                    return response.data
                }).catch((error: any) => {
                    console.log(error)
                });
            this.auditMembers = data;
        },

        async getAuditeeMembers(item: string) {
            this.auditeeMembers = [];
            const data = await fetchWrapper
                .get(`main-audit/auditees/${item}`)
                .then((response: any) => {
                    return response.data
                }).catch((error: any) => {
                    console.log(error)
                });
            this.auditeeMembers = data;
        },
        async actionAudit(values: any) {
            try {
            const organizationStore = useOrganizationStore();
                const data = await fetchWrapper.post(`main-audit/action/${organizationStore.active}`, values)
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
        async setAuditors(values: any) {
            try {
            const organizationStore = useOrganizationStore();
                const data = await fetchWrapper.post(`main-audit/set-auditors/${organizationStore.active}`, values)
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
        async setAuditee(values: any) {
            try {
            const organizationStore = useOrganizationStore();
                const data = await fetchWrapper.post(`main-audit/set-auditees/${organizationStore.active}`, values)
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
                const data = await fetchWrapper.post(`main-audit/remove-member/${organizationStore.active}`, values)
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
        async requestDocument(values: any) {
            try {
                const organizationStore = useOrganizationStore();
                const data = await fetchWrapper.post(`main-audit/request-document/${organizationStore.active}`, values)
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
        async sendDocument(values: any) {
            try {
                
                const user = useAuthStore();
                const data = await axios({
                    method: 'post',
                    url: `${import.meta.env.VITE_API_LINK}main-audit/send-document`,
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
        async actionRemoveDocument(values: any) {
            try {
                const organizationStore = useOrganizationStore();
                const data = await fetchWrapper.post(`main-audit/remove-document/${organizationStore.active}`, values)
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
        async actionRevertDocument(values: any) {
            try {
                const organizationStore = useOrganizationStore();
                const data = await fetchWrapper.post(`main-audit/revert-document/${organizationStore.active}`, values)
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
        async actionAuditDocument(values: any) {
            try {
                const organizationStore = useOrganizationStore();
                const data = await fetchWrapper.post(`main-audit/action-document/${organizationStore.active}`, values)
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
        async actionAuditorTime(values: any) {
            try {
                const organizationStore = useOrganizationStore();
                const data = await fetchWrapper.post(`main-audit/auditor-time/${organizationStore.active}`, values)
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
        async actionAuditeeTime(values: any) {
            try {
                const organizationStore = useOrganizationStore();
                const data = await fetchWrapper.post(`main-audit/auditee-time/${organizationStore.active}`, values)
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
        async actionAcceptedTime(values: any) {
            try {
                const organizationStore = useOrganizationStore();
                const data = await fetchWrapper.post(`main-audit/accept-time/${organizationStore.active}`, values)
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
        async auditSendResponse(values: any) {
            try {
                const organizationStore = useOrganizationStore();
                const data = await fetchWrapper.post(`main-audit/send-response/${organizationStore.active}`, values)
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
        async auditSendComment(values: any) {
            try {
                const organizationStore = useOrganizationStore();
                const data = await fetchWrapper.post(`main-audit/send-comment/${organizationStore.active}`, values)
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
        async sendAuditFindings(values: any) {
            try {
                const organizationStore = useOrganizationStore();
                const data = await fetchWrapper.post(`main-audit/send-finding/${organizationStore.active}`, values)
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
        async sendAuditRecommendations(values: any) {
            try {
                const organizationStore = useOrganizationStore();
                const data = await fetchWrapper.post(`main-audit/send-recommendation/${organizationStore.active}`, values)
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
        async completeAudit(values: any) {
            try {
                const organizationStore = useOrganizationStore();
                const data = await fetchWrapper.post(`main-audit/complete/${organizationStore.active}`, values)
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
