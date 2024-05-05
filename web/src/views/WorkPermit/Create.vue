<script setup lang="ts">
import { ref, onMounted, computed, watch } from 'vue';
import { useRoute } from 'vue-router'
import { router } from '@/router';

import BaseBreadcrumb from '@/components/shared/BaseBreadcrumb.vue';
import UiParentCard from '@/components/shared/UiParentCard.vue';
import { useOrganizationStore } from '@/stores/organizationStore';
import { useAuthStore } from '@/stores/auth';
import { usePermitToWorkStore } from '@/stores/permitToWorkStore';
import moment from 'moment'
import Swal from 'sweetalert2'



const route = useRoute()
const authStore = useAuthStore();
const organizationStore = useOrganizationStore();
const permitToWorkStore = usePermitToWorkStore();


onMounted(() => {
    permitToWorkStore.getPermitTypes();
    permitToWorkStore.getPermitRequestTypes();
    
    
});


const getPermitTypes : any  = computed(() => (permitToWorkStore.permitTypes));
const getPermitRequestTypes : any  = computed(() => (permitToWorkStore.permitRequestTypes));



const getActiveOrg : any  = computed(() => (organizationStore.getActiveOrg()));
const getAuthUser : any  = computed(() => (authStore.loggedUser));
const isLoggedInUserOwnsActionOrg : any  = computed(() => (getAuthUser.value?.id == getActiveOrg.value?.user_id));

const page = ref({ title: 'Generate Permit Intent' });
const breadcrumbs = ref([
    {
        text: 'Dashboard',
        disabled: false,
        href: 'dashboard'
    },
    {
        text: 'Permit To Work',
        disabled: false,
        href: '/work-permit'
    },
    {
        text: 'Create',
        disabled: true,
        href: '#'
    }
]);


const valid = ref(true);;
const formContainer = ref("")
const loading = ref(false);
const setLoading = (value: boolean) => {
    loading.value = value;
}

type OrganizationType = {
    uuid: string
    name: string,
    token: string,
};
const OrganizationInit = ref({
    uuid: '',
    name: '',
    token: '',
});
const stepOneFields = ref({
    orgToken: '',
    organization: null as null || {} as OrganizationType,
    selectedType: '',
    selectedRequestType: '',
    jobCode: '',
    jobName: '',
    locationNumber: '',
    description: '',
    locationName: '',
    contractorName: '',
    requestDate : '',
    start_date : '',
    end_date : '',
    organization_id: getActiveOrg.value?.uuid,
});





const stepOneFieldRules: any = ref({
    selectedRequestType: [
        (v: number) => !!v || 'Field is Required',
    ],
    orgToken: [
        (v: number) => !!v || 'Field is Required',
    ],
    organization: [
        (v: string) => !!v || 'Field is Required',
    ],
    jobCode: [
        (v: string) => !!v || 'Field is Required',
    ],
    jobName: [
        (v: string) => !!v || 'Field is Required',
    ],
    locationNumber: [
        (v: string) => !!v || 'Field is Required',
    ],
    description: [
        (v: string) => !!v || 'Field is Required',
    ],
    locationName: [
        (v: string) => !!v || 'Field is Required',
    ],
    contractorName: [
        (v: string) => !!v || 'Field is Required',
    ],
    requestDate: [
        (v: string) => !!v || 'Field is Required',
    ],
    start_date: [
        (v: string) => !!v || 'Field is Required',
    ],
    end_date: [
        (v: string) => !!v || 'Field is Required',
    ],
})






const fetchOrganization = async (token: string) => {

    try {


        const resp = await organizationStore.getTokenOrganizations(stepOneFields.value.orgToken)
            .catch((error: any) => {
                console.log(error)
                throw error
            })
            .then((resp: any) => {
                return resp
            });

        if (resp) {
            stepOneFields.value.organization = resp
        } else {

            stepOneFields.value.organization = {
                        uuid: '',
                        name: '',
                        token: '',
                    }
        }


    } catch (error) {

    }
}






const sendInitiatePermit = async (e: any) => {
    e.preventDefault();

    try {
        setLoading(true)

        const values = { ...stepOneFields.value }
        
        let objectValues = {
            "organization_id": getActiveOrg.value?.uuid,
            "recipient_organization_id": values?.organization ? values?.organization?.uuid : getActiveOrg.value?.uuid,
            "permit_request_id" : values?.selectedRequestType,
            "job_code": values?.jobCode,
            "job_title": values?.jobName,
            "location": values?.locationName,
            "location_id": values?.locationNumber,
            "contractor_name": values?.contractorName,
            "description": values?.description,
            "request_date": moment(values?.requestDate).format('YYYY-MM-DD HH:mm:ss'),
            "start_date":  moment(values?.start_date).format('YYYY-MM-DD HH:mm:ss'),
            "end_date": moment(values?.end_date).format('YYYY-MM-DD HH:mm:ss'),
        }

        const resp = await permitToWorkStore.initiatePermit(objectValues)
            .catch((error: any) => {
                console.log(error)
                throw error
            })
            .then((resp: any) => {
                return resp
            });

        if (resp?.message == 'success') {
            setLoading(false)

            router.push(`/ongoing-work-permit/${resp.data?.data?.uuid}`)

            stepOneFields.value.selectedType = '';
            stepOneFields.value.selectedRequestType = '';
            stepOneFields.value.orgToken = '';
            stepOneFields.value.organization = {
                        uuid: '',
                        name: '',
                        token: '',
                    };
            stepOneFields.value.jobCode = '';
            stepOneFields.value.jobName = '';
            stepOneFields.value.locationNumber = '';
            stepOneFields.value.locationName = '';
            stepOneFields.value.contractorName = '';
            stepOneFields.value.description = '';


        }

        setLoading(false)



    } catch (error) {
        console.log(error)
        setLoading(false)
    }

}









</script>

<template>
    <div>
        <BaseBreadcrumb :title="page.title" :breadcrumbs="breadcrumbs"></BaseBreadcrumb>
        <!-- {{ getInvestigation }} -->
        <v-row v-if="isLoggedInUserOwnsActionOrg">
            <!-- <v-row v-if="getInvestigation?.length > 0"> -->
            <v-col cols="12" md="7">

                <UiParentCard variant="outlined">
                    <v-card-text>

                        <VForm v-model="valid" ref="formContainer" fast-fail lazy-validation
                            @submit.prevent="sendInitiatePermit" class="py-1">
                            <VRow class="mt-5 mb-3">

                                <VCol cols="12" md="12">
                                    <v-label class="font-weight-medium pb-1">Type Of Permit Intent</v-label>
                                    <VSelect v-model="stepOneFields.selectedType" :items="getPermitTypes" label="Select"
                                        single-line variant="outlined" class="text-capitalize"
                                        :rules="[(v: any) => !!v || 'You must select to continue!']"
                                        item-title='description' item-value="name" required>
                                    </VSelect>
                                </VCol>


                                <VCol cols="12" md="12" v-if="stepOneFields.selectedType == 'Contractor'">
                                    <v-label class="text-subtitle-1 font-weight-medium pb-1">Organization
                                        Invite Token</v-label>
                                    <VTextField type="text" v-model="stepOneFields.orgToken"
                                        :rules="stepOneFieldRules.orgToken" required variant="outlined" label=""
                                        @change="fetchOrganization"
                                        :color="stepOneFields.orgToken.length > 2 ? 'success' : 'primary'">
                                        <template v-slot:append>
                                            <v-btn color="primary" @click="fetchOrganization"
                                                :disabled="stepOneFields.orgToken?.length < 12">Search</v-btn>
                                        </template>
                                    </VTextField>
                                </VCol>
                                <VCol cols="12" md="12"
                                    v-if="stepOneFields.selectedType == 'Contractor' && stepOneFields.orgToken?.length > 12">

                                    <div v-if="stepOneFields.organization">
                                        <v-card elevation="10" rounded="md">
                                            <v-card-item>
                                                <div class="d-flex align-center">
                                                    <div class="pl-4 mt-n1">
                                                        <h5 class="text-h6">{{
                                                            stepOneFields.organization?.name
                                                            }}</h5>
                                                        <h5 class="text-h6">{{
                                                            stepOneFields.organization?.token
                                                            }}</h5>
                                                    </div>
                                                </div>
                                            </v-card-item>
                                        </v-card>
                                    </div>
                                    <div v-else>
                                        <v-card elevation="10" rounded="md">
                                            <v-card-item>
                                                <div class="d-flex align-center">
                                                    <div class="pl-4 mt-n1">
                                                        <h5 class="text-h6">
                                                            Organization Not Found
                                                        </h5>
                                                    </div>
                                                </div>
                                            </v-card-item>
                                        </v-card>
                                    </div>
                                </VCol>



                                <template v-if="stepOneFields.selectedType">

                                    <VCol cols="12" md="12">
                                        <v-label class="font-weight-medium pb-1">Permit Request Type</v-label>
                                        <VSelect v-model="stepOneFields.selectedRequestType"
                                            :items="getPermitRequestTypes" label="Select" single-line variant="outlined"
                                            class="text-capitalize"
                                            :rules="[(v: any) => !!v || 'You must select to continue!']"
                                            item-title='description' item-value="id" required>
                                        </VSelect>
                                    </VCol>

                                    <template v-if="stepOneFields.selectedRequestType">

                                        <VCol cols="12" md="12">
                                            <v-label class="text-subtitle-1 font-weight-medium pb-1">Name Of
                                                Job</v-label>
                                            <VTextField type="text" v-model="stepOneFields.jobName"
                                                :rules="stepOneFieldRules.jobName" required variant="outlined" label=""
                                                :color="stepOneFields.jobName.length > 2 ? 'success' : 'primary'">
                                            </VTextField>
                                        </VCol>
                                        <VCol cols="12" md="12">
                                            <v-label class="text-subtitle-1 font-weight-medium pb-1">Job Code/Job Order
                                                No</v-label>
                                            <VTextField type="text" v-model="stepOneFields.jobCode"
                                                :rules="stepOneFieldRules.jobCode" required variant="outlined" label=""
                                                :color="stepOneFields.jobCode.length > 2 ? 'success' : 'primary'">
                                            </VTextField>
                                        </VCol>
                                        <VCol cols="12" md="12">
                                            <v-label class="text-subtitle-1 font-weight-medium pb-1">Job
                                                Location Name</v-label>
                                            <VTextField type="text" v-model="stepOneFields.locationName"
                                                :rules="stepOneFieldRules.locationName" required variant="outlined"
                                                label=""
                                                :color="stepOneFields.locationName.length > 2 ? 'success' : 'primary'">
                                            </VTextField>
                                        </VCol>
                                        <VCol cols="12" md="12">
                                            <v-label class="text-subtitle-1 font-weight-medium pb-1">Job
                                                Location Number</v-label>
                                            <VTextField type="text" v-model="stepOneFields.locationNumber"
                                                :rules="stepOneFieldRules.locationNumber" required variant="outlined"
                                                label=""
                                                :color="stepOneFields.locationNumber.length > 2 ? 'success' : 'primary'">
                                            </VTextField>
                                        </VCol>
                                        <VCol cols="12" md="12">
                                            <v-label class="text-subtitle-1 font-weight-medium pb-1">Name Of Contractor/
                                                Project Team</v-label>
                                            <VTextField type="text" v-model="stepOneFields.contractorName"
                                                :rules="stepOneFieldRules.contractorName" required variant="outlined"
                                                label=""
                                                :color="stepOneFields.contractorName.length > 2 ? 'success' : 'primary'">
                                            </VTextField>
                                        </VCol>
                                        <VCol cols="12" md="12">
                                            <v-label class="text-subtitle-1 font-weight-medium pb-1">Request
                                                Date</v-label>
                                            <VTextField type="hidden" v-model="stepOneFields.requestDate"
                                                :rules="stepOneFieldRules.requestDate" required variant="outlined"
                                                label=""
                                                :color="stepOneFields.requestDate.length > 2 ? 'success' : 'primary'">
                                            </VTextField>
                                            <VueDatePicker input-class-name="dp-custom-input"
                                                v-model="stepOneFields.requestDate" :min-date="new Date()" required>
                                            </VueDatePicker>
                                        </VCol>


                                        <VCol cols="12" md="12">
                                            <v-label class="text-subtitle-1 font-weight-medium pb-1">Description Of
                                                Facility</v-label>
                                            <VTextarea type="text" v-model="stepOneFields.description"
                                                :rules="stepOneFieldRules.description" required variant="outlined"
                                                label=""
                                                :color="stepOneFields.description.length > 2 ? 'success' : 'primary'">
                                            </VTextarea>
                                        </VCol>

                                        <VCol cols="12" md="6">
                                            <v-label class="text-subtitle-1 font-weight-medium pb-1">Work Start
                                                Date</v-label>
                                            <VueDatePicker input-class-name="dp-custom-input"
                                                v-model="stepOneFields.start_date" :min-date="new Date()" required>
                                            </VueDatePicker>
                                        </VCol>
                                        <VCol cols="12" md="6">
                                            <v-label class="text-subtitle-1 font-weight-medium pb-1">Work End
                                                Date</v-label>
                                            <VueDatePicker input-class-name="dp-custom-input"
                                                :disabled='!stepOneFields.start_date' v-model="stepOneFields.end_date"
                                                :min-date="stepOneFields.start_date ? new Date(stepOneFields.start_date) : new Date()"
                                                required></VueDatePicker>
                                        </VCol>

                                    </template>



                                </template>



                                <VCol cols="12" lg="12" class="text-right">

                                    <v-btn color="primary" type="submit" :loading="loading" :disabled="!valid"
                                        @click="sendInitiatePermit">
                                        <span v-if="!loading">
                                            Save
                                        </span>
                                        <clip-loader v-else :loading="loading" color="white"
                                            :size="'25px'"></clip-loader>
                                    </v-btn>

                                </VCol>
                            </VRow>
                        </VForm>
                    </v-card-text>
                </UiParentCard>

            </v-col>

        </v-row>
    </div>
</template>
<style lang="scss">
.customTab {
    min-height: 68px;
}
</style>

