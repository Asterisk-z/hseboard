<script setup lang="ts">
import { ref, onMounted, computed, watch } from 'vue';
import { useRoute } from 'vue-router'
import { router } from '@/router';

import BaseBreadcrumb from '@/components/shared/BaseBreadcrumb.vue';
import UiParentCard from '@/components/shared/UiParentCard.vue';
import { useOrganizationStore } from '@/stores/organizationStore';
import { useTeamMemberStore } from '@/stores/teamMemberStore';
import { useInvestigationStore } from '@/stores/investigationStore';
import { useMainAuditStore } from '@/stores/mainAuditStore';
import { useAuthStore } from '@/stores/auth';
import { useOpenLinksStore } from '@/stores/openLinks';
import { useAuditTemplateStore } from '@/stores/auditTemplateStore';
import { useInspectionStore } from '@/stores/inspectionStore';
import moment from 'moment'
import Swal from 'sweetalert2'



const route = useRoute()
const authStore = useAuthStore();
const organizationStore = useOrganizationStore();
const auditTemplateStore = useAuditTemplateStore();
const teamMemberStore = useTeamMemberStore();
const openLinks = useOpenLinksStore();
const investigationStore = useInvestigationStore();
const mainAuditStore = useMainAuditStore();
const inspectionStore = useInspectionStore();


onMounted(() => {
    inspectionStore.getInspectionTypes();
    inspectionStore.getInvestigationTemplateTypes();
    inspectionStore.getInspectionTemplateForTypeId()

    // teamMemberStore.getTeamMembers();
    // openLinks.getPriorities();
    // investigationStore.getInvestigationQuestions(route.params.observation_id as string);
});

const getInspectionTypes: any = computed(() => (inspectionStore.inspectionTypes));
const getInvestigationTemplateTypes: any = computed(() => (inspectionStore.inspectionTemplateTypes));
const getInspectionTemplateForTypeId: any = computed(() => (inspectionStore.inspectionTypeTemplates));

const getActiveOrg: any = computed(() => (organizationStore.getActiveOrg()));
const getAuthUser: any = computed(() => (authStore.loggedUser));
const getInvestigation: any = computed(() => (investigationStore.investigation));
const isLoggedInUserOwnsActionOrg: any = computed(() => (getAuthUser.value?.id == getActiveOrg.value?.user_id));

const page = ref({ title: 'Create Inspection' });
const breadcrumbs = ref([
    {
        text: 'Dashboard',
        disabled: false,
        href: 'dashboard'
    },
    {
        text: 'Facility Inspection',
        disabled: false,
        href: '/inspections'
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

const stepOneFields = ref({
    orgToken: '',
    organization: null as null || {} as OrganizationType,
    selectedType: '',
    templateId: '',
    name: '',
    inspectionTemplateTypeId: '',
    location: '',
    description: '',
    objective: '',
    ppe: '',
    organization_id: getActiveOrg.value?.uuid,
});

const stepOneFieldRules: any = ref({
    inspectionTemplateTypeId: [
        (v: number) => !!v || 'Field is Required',
    ],
    orgToken: [
        (v: number) => !!v || 'Field is Required',
    ],
    organization: [
        (v: string) => !!v || 'Field is Required',
    ],
    templateId: [
        (v: string) => !!v || 'Field is Required',
    ],
    name: [
        (v: string) => !!v || 'Field is Required',
    ],
    location: [
        (v: string) => !!v || 'Field is Required',
    ],
    description: [
        (v: string) => !!v || 'Field is Required',
    ],
    objective: [
        (v: string) => !!v || 'Field is Required',
    ],
    ppe: [
        (v: string) => !!v || 'Field is Required',
    ],
})





// const fetchInspectionTemplate = async () => {

//     try {


//         const resp = await inspectionStore.getInspectionTemplateForTypeId(stepOneFields.value.inspectionTemplateTypeId as string)
//             .catch((error: any) => {
//                 console.log(error)
//                 throw error
//             })
//             .then((resp: any) => {
//                 return resp
//             });



//     } catch (error) {

//     }
// }

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






const sendInitiateInspection = async (e: any) => {
    e.preventDefault();

    try {
        setLoading(true)

        const values = { ...stepOneFields.value }

        console.log(getActiveOrg.value?.uuid);
        console.log(values?.organization);
        let objectValues = {
            "organization_id": getActiveOrg.value?.uuid,
            "recipient_organization_id": Object.keys(values?.organization).length === 0 ? getActiveOrg.value?.uuid : values.organization?.uuid,
            "selectedType": values?.selectedType,
            "templateId": values?.templateId,
            "name": values?.name,
            // "inspectionTemplateTypeId": values?.inspectionTemplateTypeId,
            "location": values?.location,
            "description": values?.description,
            "objective": values?.objective,
            "ppe": values?.ppe,
        }
        console.log(objectValues);
        const resp = await inspectionStore.initiateInspection(objectValues)
            .catch((error: any) => {
                console.log(error)
                throw error
            })
            .then((resp: any) => {
                return resp
            });

        if (resp?.message == 'success') {
            setLoading(false)

            stepOneFields.value.selectedType = '';
            // stepOneFields.value.inspectionTemplateTypeId = '';
            stepOneFields.value.orgToken = '';
            stepOneFields.value.organization = {
                uuid: '',
                name: '',
                token: '',
            };
            stepOneFields.value.templateId = '';
            stepOneFields.value.name = '';
            stepOneFields.value.location = '';
            stepOneFields.value.objective = '';
            stepOneFields.value.ppe = '';
            stepOneFields.value.description = '';

            router.push(`/ongoing-inspection-report/${resp.data?.data?.uuid}`)

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
                            @submit.prevent="sendInitiateInspection" class="py-1">
                            <VRow class="mt-5 mb-3">

                                <VCol cols="12" md="12">
                                    <v-label class="font-weight-medium pb-1">Type Of Inspection</v-label>
                                    <VSelect v-model="stepOneFields.selectedType" :items="getInspectionTypes"
                                        label="Select" single-line variant="outlined" class="text-capitalize"
                                        :rules="[(v: any) => !!v || 'You must select to continue!']"
                                        item-title='description' item-value="name" required>
                                    </VSelect>
                                </VCol>

                                <VCol cols="12" md="12" v-if="stepOneFields.selectedType == 'third-party'">
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
                                    v-if="stepOneFields.selectedType == 'third-party' && stepOneFields.orgToken?.length > 12">

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

                                    <!-- <VCol cols="12" md="12">
                                        <v-label class="font-weight-medium pb-1">Inspection Template Type</v-label>
                                        <VSelect v-model="stepOneFields.inspectionTemplateTypeId"
                                            :items="getInvestigationTemplateTypes" label="Select" single-line
                                            variant="outlined" @update:modelValue="fetchInspectionTemplate"
                                            class="text-capitalize"
                                            :rules="[(v: any) => !!v || 'You must select to continue!']"
                                            item-title='description' item-value="id" required>
                                        </VSelect>
                                    </VCol> -->

                                    <template v-if="getInspectionTemplateForTypeId.length > 0">

                                        <VCol cols="12" md="12">
                                            <v-label class="font-weight-medium pb-1">Select Checklist Template</v-label>
                                            <VSelect v-model="stepOneFields.templateId"
                                                :items="getInspectionTemplateForTypeId" label="Select" single-line
                                                variant="outlined" class="text-capitalize"
                                                :rules="[(v: any) => !!v || 'You must select to continue!']"
                                                item-title='description' item-value="id" required>
                                            </VSelect>
                                        </VCol>

                                        <VCol cols="12" md="12">
                                            <v-label class="text-subtitle-1 font-weight-medium pb-1">Name Of
                                                Facility</v-label>
                                            <VTextField type="text" v-model="stepOneFields.name"
                                                :rules="stepOneFieldRules.name" required variant="outlined" label=""
                                                :color="stepOneFields.name.length > 2 ? 'success' : 'primary'">
                                            </VTextField>
                                        </VCol>
                                        <VCol cols="12" md="12">
                                            <v-label class="text-subtitle-1 font-weight-medium pb-1">Location Of
                                                Facility</v-label>
                                            <VTextField type="text" v-model="stepOneFields.location"
                                                :rules="stepOneFieldRules.location" required variant="outlined" label=""
                                                :color="stepOneFields.location.length > 2 ? 'success' : 'primary'">
                                            </VTextField>
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


                                        <VCol cols="12" md="12">
                                            <v-label class="text-subtitle-1 font-weight-medium pb-1">Objective Of
                                                Inspection</v-label>
                                            <VTextarea type="text" v-model="stepOneFields.objective"
                                                :rules="stepOneFieldRules.objective" required variant="outlined"
                                                label=""
                                                :color="stepOneFields.objective.length > 2 ? 'success' : 'primary'">
                                            </VTextarea>
                                        </VCol>

                                        <VCol cols="12" md="12">
                                            <v-label class="text-subtitle-1 font-weight-medium pb-1">Personal Protective
                                                Equipment (PPE) </v-label>
                                            <VTextarea type="text" v-model="stepOneFields.ppe"
                                                :rules="stepOneFieldRules.ppe" required variant="outlined" label=""
                                                :color="stepOneFields.ppe.length > 2 ? 'success' : 'primary'">
                                            </VTextarea>
                                        </VCol>

                                    </template>



                                </template>



                                <VCol cols="12" lg="12" class="text-right">

                                    <v-btn color="primary" type="submit" :loading="loading" :disabled="!valid"
                                        @click="sendInitiateInspection">
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
