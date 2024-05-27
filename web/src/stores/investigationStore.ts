import { defineStore } from 'pinia';
import { router } from '@/router';
import { fetchWrapper } from '@/utils/helpers/fetch-wrapper';
import { toastWrapper } from '@/utils/helpers/toast-wrapper';
import { useOrganizationStore } from './organizationStore';
import { useAuthStore } from '@/stores/auth';
import axios from 'axios';


type startInvestigationType = {
    organization_id: string,
    observation_id: string,
};

type InvestigationType = {
    organization_id: string,
    title: string,
    description: string,
    assignee_id: string,
    start_date: string,
    end_date: string,
    priority_id: string,
};
type editInvestigationType = {
    organization_id: string,
    title: string,
    description: string,
    start_date: string,
    end_date: string,
    priority_id: string,
    investigation_id: string,
};

type removeInvestigationType = {
    investigation_id: string,
};
type statusInvestigationType = {
    investigation_id: string,
    status: string,
    message?: string,
};

export const useInvestigationStore = defineStore({
    id: 'investigation',
    state: () => ({
        // initialize state from local storage to enable user to stay logged in
        // @ts-ignore
        investigations: null as null || [],
        witness_investigations: null as null || [],
        witness_investigation_questions: null as null || [],
        investigation: null as null || [],
        questions: null as null || [],
        
    }),
    getters: {
        
    },
    actions: {
        async getInvestigations() {

            const data = await fetchWrapper
                .get(`investigations/all`)
                .then((response: any) => {
                    return response.data
                }).catch((error: any) => {
                    console.log(error)
                });
            this.investigations = data;
        },
        async getWitnessInvestigation() {

            const data = await fetchWrapper
                .get(`investigations/witness/all`)
                .then((response: any) => {
                    return response.data
                }).catch((error: any) => {
                    console.log(error)
                });
            this.witness_investigations = data;
        },
        async getWitnessInvestigationQuestions(item: string) {

            const data = await fetchWrapper
                .get(`investigations/witness/all/${item}`)
                .then((response: any) => {
                    return response.data
                }).catch((error: any) => {
                    console.log(error)
                });
            this.witness_investigation_questions = data;
        },
        async sendWitnessResponse(values: any) {
            
            try {
                const data = await fetchWrapper
                    .post(`investigations/witness/respond`, values)
                    .then((response: any) => {
                        return response.data
                    }).catch((error: any) => {
                        console.log(error)
                    });
                return toastWrapper.success(data?.message, data)
            } catch (error: any) {
                return toastWrapper.error(error, error)
            }
        },
        async completeWitnessResponse(values: any) {
            
            try {
                const data = await fetchWrapper
                    .post(`investigations/witness/complete-respond`, values)
                    .then((response: any) => {
                        return response.data
                    }).catch((error: any) => {
                        console.log(error)
                    });
                return toastWrapper.success(data?.message, data)
            } catch (error: any) {
                return toastWrapper.error(error, error)
            }
        },
        async getInvestigation(item: string) {

            const data = await fetchWrapper
                .get(`investigations/show/${item}`)
                .then((response: any) => {
                    return response.data
                }).catch((error: any) => {
                    console.log(error)
                });
            this.investigation = data;
        },
        async getCompletedInvestigation(item: string) {

            const data = await fetchWrapper
                .get(`investigations/completed/${item}`)
                .then((response: any) => {
                    return response.data
                }).catch((error: any) => {
                    console.log(error)
                });
            this.investigation = data;
        },
        async getInvestigationQuestions(values: any) {
            
            const data = await fetchWrapper
                .get(`investigations/questions/${values}`)
                .then((response: any) => {
                    return response.data
                }).catch((error: any) => {
                    console.log(error)
                });
            this.questions = data;
        },
        async setInvestigationMember(values: any) {
            const organizationStore = useOrganizationStore();
            try {
                const data = await fetchWrapper
                    .post(`investigations/member/${organizationStore.active}`, values)
                    .then((response: any) => {
                        return response.data
                    }).catch((error: any) => {
                        console.log(error)
                    });
                return toastWrapper.success(data?.message, data)
            } catch (error: any) {
                return toastWrapper.error(error, error)
            }
        },
        async setExternalInvestigationMember(values: any) {
            try {
                const data = await fetchWrapper
                    .post(`investigations/external-member`, values)
                    .then((response: any) => {
                        return response.data
                    }).catch((error: any) => {
                        console.log(error)
                    });
                return toastWrapper.success(data?.message, data)
            } catch (error: any) {
                return toastWrapper.error(error, error)
            }
        },
        async removeMember(values: any) {
            const organizationStore = useOrganizationStore();
            try {
                const data = await fetchWrapper
                    .post(`investigations/remove-member/${organizationStore.active}`, values)
                    .then((response: any) => {
                        return response.data
                    }).catch((error: any) => {
                        console.log(error)
                    });
                return toastWrapper.success(data?.message, data)
            } catch (error: any) {
                return toastWrapper.error(error, error)
            }
        },
        async addRecommendation(values: any) {
            const organizationStore = useOrganizationStore();
            try {
                const data = await fetchWrapper
                    .post(`investigations/recommendation/${organizationStore.active}`, values)
                    .then((response: any) => {
                        return response.data
                    }).catch((error: any) => {
                        console.log(error)
                    });
                return toastWrapper.success(data?.message, data)
            } catch (error: any) {
                return toastWrapper.error(error, error)
            }
        },
        async addReport(values: any) {
            const organizationStore = useOrganizationStore();
            try {
                const data = await fetchWrapper
                    .post(`investigations/report/${organizationStore.active}`, values)
                    .then((response: any) => {
                        return response.data
                    }).catch((error: any) => {
                        console.log(error)
                    });
                return toastWrapper.success(data?.message, data)
            } catch (error: any) {
                return toastWrapper.error(error, error)
            }
        },
        async sendInvestigationQuestions(values: any) {
            const organizationStore = useOrganizationStore();
            try {
                const data = await fetchWrapper
                    .post(`investigations/send-questions/${organizationStore.active}`, values)
                    .then((response: any) => {
                        return response.data
                    }).catch((error: any) => {
                        console.log(error)
                    });
                return toastWrapper.success(data?.message, data)
            } catch (error: any) {
                return toastWrapper.error(error, error)
            }
        },
        async completeInvestigation(values: any) {
            const organizationStore = useOrganizationStore();
            try {
                const data = await fetchWrapper
                    .post(`investigations/complete/${organizationStore.active}`, values)
                    .then((response: any) => {
                        return response.data
                    }).catch((error: any) => {
                        console.log(error)
                    });
                return toastWrapper.success(data?.message, data)
            } catch (error: any) {
                return toastWrapper.error(error, error)
            }
        },
        async sendInvestigationInvites(values: any) {
            const organizationStore = useOrganizationStore();
            try {
                const data = await fetchWrapper
                    .post(`investigations/send-invites/${organizationStore.active}`, values)
                    .then((response: any) => {
                        return response.data
                    }).catch((error: any) => {
                        console.log(error)
                    });
                return toastWrapper.success(data?.message, data)
            } catch (error: any) {
                return toastWrapper.error(error, error)
            }
        },
        async sendInvestigationFindings(values: any) {
            const organizationStore = useOrganizationStore();
            try {
                // const data = await fetchWrapper
                //     .post(`investigations/send-findings/${organizationStore.active}`, values)
                //     .then((response: any) => {
                //         return response.data
                //     }).catch((error: any) => {
                //         console.log(error)
                //     });

                const user = useAuthStore();
                const data = await axios({
                    method: 'post',
                    url: `${import.meta.env.VITE_API_LINK}investigations/send-findings/${organizationStore.active}`,
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
                return toastWrapper.success(data?.message, data)
            } catch (error: any) {
                return toastWrapper.error(error, error)
            }
        },
        async setInvestigationQuestion(values: any) {
            const organizationStore = useOrganizationStore();
            try {
                const data = await fetchWrapper
                    .post(`investigations/question/${organizationStore.active}`, values)
                    .then((response: any) => {
                        return response.data
                    }).catch((error: any) => {
                        console.log(error)
                    });
                return toastWrapper.success(data?.message, data)
            } catch (error: any) {
                return toastWrapper.error(error, error)
            }
        },
        async startInvestigation(values: startInvestigationType) {
            try {
                const data = await fetchWrapper.post(`investigations/start`, values)
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
        async addInvestigation(values: InvestigationType) {
            try {
                const data = await fetchWrapper.post(`investigations/create`, values)
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
        async editInvestigation(values: editInvestigationType) {
            try {
                const data = await fetchWrapper.post(`investigations/update`, values)
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
        async deleteInvestigation(values: removeInvestigationType) {
            try {
                const data = await fetchWrapper.post(`investigations/delete`, values)
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
        async statusUpdate(values: statusInvestigationType) {
            try {
                const data = await fetchWrapper.post(`investigations/status-update`, values)
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
