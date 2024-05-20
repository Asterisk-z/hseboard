import { defineStore } from 'pinia';
import { router } from '@/router';
import { fetchWrapper } from '@/utils/helpers/fetch-wrapper';
import { toastWrapper } from '@/utils/helpers/toast-wrapper';
import { useOrganizationStore } from './organizationStore';


type ReportType  = {
    organization_id?: string,
    Report_type: string,
    description: string,
    location_details: string,
    address: string,
    affected_workers?: string,
    date_time: string,
    severity: string,
    images?: any
};
type editReportType = {
    organization_id?: string,
    Report_type: string,
    description: string,
    location_details: string,
    address: string,
    affected_workers?: string,
    date_time: string,
    severity: string,
    Report_id: string,
};

type removeReportType = {
    Report_id: string,
};

export const useReportStore = defineStore({
    id: 'report',
    state: () => ({
        // initialize state from local storage to enable user to stay logged in
        // @ts-ignore
        reports: null as null || [],
        report: null as null || [],
    }),
    getters: {
        
    },
    actions: {
        async getReports() {
            
            const data = await fetchWrapper
                .get(`statistic/all`)
                .then((response: any) => {
                    return response.data
                }).catch((error: any) => {
                    console.log(error)
                });
            this.reports = data;
        },
        async getCompletedReport(value : string) {

            const data = await fetchWrapper
                .get(`statistic/show/${value}`)
                .then((response: any) => {
                    return response.data
                }).catch((error: any) => {
                    console.log(error)
                });
            this.report = data;
        },
        async addReport(values: any) {
            try {
                const data = await fetchWrapper.post(`statistic/create`, values)
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
        async removeReport(values: removeReportType) {
            try {
                const data = await fetchWrapper.post(`statistic/delete`, values)
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
