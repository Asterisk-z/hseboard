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

export const useJobHazardStore = defineStore({
    id: 'jobHazard',
    state: () => ({
        // initialize state from local storage to enable user to stay logged in
        // @ts-ignore
        jobHazards: null as null || [],
        reviewJobHazard: null as null || [],
        ongoingJobHazard: null as null || [],

        questions: null as null || [],
        
    }),
    getters: {
        
    },
    actions: {
        async createJobHazardAnalysis(values: any) {
            try {
                const data = await fetchWrapper.post(`jha/createJob`, values)
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
        async getAllJobHazardAnalysis() {

            const organizationStore = useOrganizationStore();
            const data = await fetchWrapper
                .get(`jha/all/${organizationStore.active}`)
                .then((response: any) => {
                    return response.data
                }).catch((error: any) => {
                    console.log(error)
                });
            this.jobHazards = data;
        },
        async getOngoingJobHazardAnalysis(job_id: number) {
            
            const organizationStore = useOrganizationStore();
            const data = await fetchWrapper
                .get(`jha/ongoing/${organizationStore.active}/${job_id}`)
                .then((response: any) => {
                    return response.data
                }).catch((error: any) => {
                    console.log(error)
                });
            this.ongoingJobHazard = data;
        },
        async getReviewJobHazardAnalysis(job_id: number) {

            const organizationStore = useOrganizationStore();
            const data = await fetchWrapper
                .get(`jha/review/${organizationStore.active}/${job_id}`)
                .then((response: any) => {
                    return response.data
                }).catch((error: any) => {
                    console.log(error)
                });
            this.reviewJobHazard = data;
        },
        async addJobHazardAnalysis(values: any) {
            try {
                const data = await fetchWrapper.post(`jha/add-step`, values)
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
        async addEventJobHazardAnalysis(values: any) {
            try {
                const data = await fetchWrapper.post(`jha/add-event`, values)
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
        async addSourceJobHazardAnalysis(values: any) {
            try {
                const data = await fetchWrapper.post(`jha/add-source`, values)
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
        async addTargetJobHazardAnalysis(values: any) {
            try {
                const data = await fetchWrapper.post(`jha/add-target`, values)
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
        async addConsequenceJobHazardAnalysis(values: any) {
            try {
                const data = await fetchWrapper.post(`jha/add-consequence`, values)
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
        async addActionJobHazardAnalysis(values: any) {
            try {
                const data = await fetchWrapper.post(`jha/add-action`, values)
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
        async addRCPJobHazardAnalysis(values: any) {
            try {
                const data = await fetchWrapper.post(`jha/add-rcp`, values)
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
        async addRecoveryJobHazardAnalysis(values: any) {
            try {
                const data = await fetchWrapper.post(`jha/add-recovery`, values)
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
        async addPartyJobHazardAnalysis(values: any) {
            try {
                const data = await fetchWrapper.post(`jha/add-party`, values)
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
        async deleteJobHazardAnalysisItem(values: any) {
            try {
                const data = await fetchWrapper.post(`jha/delete-item`, values)
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
        async removeJobHazardAnalysisStep(values: any) {
            try {
                const data = await fetchWrapper.post(`jha/remove-step`, values)
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
        async completeJobHazardAnalysis(values: any) {
            try {
                const data = await fetchWrapper.post(`jha/complete`, values)
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
        async actionJobHazardAnalysis(values: any) {
            try {
                const data = await fetchWrapper.post(`jha/action`, values)
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
