<script setup lang="ts">
import { ref, onMounted, computed, watch } from 'vue';
import { useRoute } from 'vue-router'
import { router } from '@/router';

import BaseBreadcrumb from '@/components/shared/BaseBreadcrumb.vue';
import UiParentCard from '@/components/shared/UiParentCard.vue';
import { useOrganizationStore } from '@/stores/organizationStore';
import { useAuthStore } from '@/stores/auth';
import { useTeamMemberStore } from '@/stores/teamMemberStore';
import { useJobHazardStore } from '@/stores/jobHazardStore';
import { useFormatter } from '@/composables/formatter';
import Swal from 'sweetalert2'
import {
    CheckIcon,
} from 'vue-tabler-icons';


const page = ref({ title: 'Job Hazard Analysis' });
const breadcrumbs = ref([
    {
        text: 'Dashboard',
        disabled: false,
        href: '/dashboard'
    },
    {
        text: 'Job Hazard Analysis',
        disabled: false,
        href: '/inspections'
    },
    {
        text: 'Update',
        disabled: true,
        href: '#'
    }
]);

const route = useRoute()
const authStore = useAuthStore();
const organizationStore = useOrganizationStore();
const jobHazardStore = useJobHazardStore();
const teamMemberStore = useTeamMemberStore();
const { formatDate } = useFormatter();



onMounted(() => {
    jobHazardStore.getOngoingJobHazardAnalysis(route.params.job_id as string)
    teamMemberStore.getTeamMembersExcept()
});



const computedIndex = (index: any) => ++index;

const getAuthUser: any = computed(() => (authStore.loggedUser));
const getOngoingJobHazardAnalysis: any = computed(() => (jobHazardStore.ongoingJobHazard));
const getTeamMembersExcept: any = computed(() => (teamMemberStore.membersExceptMe));
const getActiveOrg: any = computed(() => (organizationStore.getActiveOrg()));
const isLoggedInUserOwnsActionOrg: any = computed(() => (getAuthUser.value?.id == getActiveOrg.value?.user_id));

const valid = ref(true);;
const formContainer = ref()
const loading = ref(false);

const setLoading = (value: boolean) => {
    loading.value = value;
}

const dialog = ref(false);
const setDialog = (value: boolean) => {
    dialog.value = value;
}

const completeDialog = ref(false);
const setCompleteDialog = (value: boolean) => {
    completeDialog.value = value;
}

const stepFields = ref({
    code: "",
    description: "",
    name: "",
    designation: "",
    section: "",
    step: "",
    itemId: "",
    reviewerId: "",
    organization_id: getActiveOrg.value?.uuid,

});

const stepFieldRules: any = ref({
    code: [
        (v: string) => !!v || 'Code  is required',
    ],
    description: [
        (v: string) => !!v || 'Description  is required',
    ],
    reviewerId: [
        (v: string) => !!v || 'Reviewer  is required',
    ],
})


const stepTitle = ref(['Code', 'Description']);
const stepDialog = ref(false);
const setStepDialog = (value: boolean, step = '', section: string = '', title: [string, string] = ['Code', 'Description'], item: {
    code?: string,
    description?: string,
    id?: string,
} = {}) => {
    stepDialog.value = value;
    stepFields.value.section = section

    stepFields.value.step = step
    stepTitle.value = title
    if (item && Object.keys(item).length > 0) {
        stepFields.value.code = item?.code || '';
        stepFields.value.description = item?.description || '';
        stepFields.value.itemId = item?.id || '';
    } else {
        stepFields.value.code = title[0]
        stepFields.value.description = ''
        stepFields.value.itemId = ''
    }

}

const safeData = async (e: any) => {
    e.preventDefault();
    try {
        setLoading(true)

        const values = { ...stepFields.value }

        // console.log(stepFields.value)

        let objectValues = {
            "organization_id": values?.organization_id,
            "job_hazard_id": getOngoingJobHazardAnalysis.value?.uuid,
            "code": values?.code,
            "designation": values?.designation,
            "name": values?.name,
            "description": values?.description,
            "job_hazard_step_id": values?.step,
            "job_hazard_item_id": values?.itemId,
        }

        let section = values?.section

        switch (section) {
            case 'top_events':

                let resp1 = await jobHazardStore.addEventJobHazardAnalysis(objectValues)
                    .catch((error: any) => {
                        throw error
                    })
                    .then((resp: any) => {
                        return resp
                    });
                break;
            case 'sources':

                let resp2 = await jobHazardStore.addSourceJobHazardAnalysis(objectValues)
                    .catch((error: any) => {
                        throw error
                    })
                    .then((resp: any) => {
                        return resp
                    });
                break;
            case 'targets':

                let resp3 = await jobHazardStore.addTargetJobHazardAnalysis(objectValues)
                    .catch((error: any) => {
                        throw error
                    })
                    .then((resp: any) => {
                        return resp
                    });
                break;
            case 'consequences':

                let resp4 = await jobHazardStore.addConsequenceJobHazardAnalysis(objectValues)
                    .catch((error: any) => {
                        throw error
                    })
                    .then((resp: any) => {
                        return resp
                    });
                break;
            case 'preventives':

                let resp5 = await jobHazardStore.addActionJobHazardAnalysis(objectValues)
                    .catch((error: any) => {
                        throw error
                    })
                    .then((resp: any) => {
                        return resp
                    });
                break;
            case 'rcps':

                let resp6 = await jobHazardStore.addRCPJobHazardAnalysis(objectValues)
                    .catch((error: any) => {
                        throw error
                    })
                    .then((resp: any) => {
                        return resp
                    });
                break;
            case 'recoveries':

                let resp7 = await jobHazardStore.addRecoveryJobHazardAnalysis(objectValues)
                    .catch((error: any) => {
                        throw error
                    })
                    .then((resp: any) => {
                        return resp
                    });
                break;
            case 'parties':

                let resp8 = await jobHazardStore.addPartyJobHazardAnalysis(objectValues)
                    .catch((error: any) => {
                        throw error
                    })
                    .then((resp: any) => {
                        return resp
                    });
                break;
            default:
                break;
        }


        jobHazardStore.getOngoingJobHazardAnalysis(route.params.job_id as string)

        setLoading(false)
        setStepDialog(false)

        stepFields.value.code = "";
        stepFields.value.section = "";
        stepFields.value.name = "";
        stepFields.value.designation = "";
        stepFields.value.description = "";
        stepFields.value.step = "";
        stepFields.value.itemId = "";


    } catch (error) {
        console.log(error)
        setLoading(false)
        setStepDialog(false)
    }

}

const deleteItem = async (step = null, section: string = '', item = null) => {

    try {
        setLoading(true)

        let objectValues = {

            "organization_id": getActiveOrg.value?.uuid,
            "job_hazard_id": getOngoingJobHazardAnalysis.value?.uuid,
            "job_hazard_step_id": step,
            "job_hazard_item_id": item,
            "item": section,
        }





        Swal.fire({
            title: 'Info!',
            text: 'Do you sure?',
            icon: 'info',
            confirmButtonText: 'Yes',
            cancelButtonText: 'No',
            showCancelButton: true,
            allowOutsideClick: false,
        }).then((result) => {
            if (result.isConfirmed) {


                jobHazardStore.deleteJobHazardAnalysisItem(objectValues)
                    .catch((error: any) => {
                        throw error
                    })
                    .then((resp: any) => {
                        jobHazardStore.getOngoingJobHazardAnalysis(route.params.job_id as string);
                        return resp
                    });


            }
        });


        setLoading(false)

    } catch (error) {
        console.log(error)
        setLoading(false)
    }

}
const removeStepItem = async (step: number) => {

    try {
        setLoading(true)

        let objectValues = {

            "organization_id": getActiveOrg.value?.uuid,
            "job_hazard_id": getOngoingJobHazardAnalysis.value?.uuid,
            "job_hazard_step_id": step
        }



        Swal.fire({
            title: 'Info!',
            text: 'Do you sure?',
            icon: 'info',
            confirmButtonText: 'Yes',
            cancelButtonText: 'No',
            showCancelButton: true,
            allowOutsideClick: false,
        }).then((result) => {
            if (result.isConfirmed) {


                jobHazardStore.removeJobHazardAnalysisStep(objectValues)
                    .catch((error: any) => {
                        throw error
                    })
                    .then((resp: any) => {
                        jobHazardStore.getOngoingJobHazardAnalysis(route.params.job_id as string);
                        return resp
                    });
            }
        });


        setLoading(false)

    } catch (error) {
        console.log(error)
        setLoading(false)
    }

}
const completeJob = async () => {

    try {
        setLoading(true)

        let objectValues = {
            "organization_id": getActiveOrg.value?.uuid,
            "reviewer_id": stepFields.value?.reviewerId,
            "job_hazard_id": getOngoingJobHazardAnalysis.value?.uuid,
        }
        setCompleteDialog(false)
        Swal.fire({
            title: 'Info!',
            text: 'Are you sure?',
            icon: 'info',
            confirmButtonText: 'Yes',
            cancelButtonText: 'No',
            showCancelButton: true,
            allowOutsideClick: false,
        }).then((result) => {
            if (result.isConfirmed) {


                jobHazardStore.completeJobHazardAnalysis(objectValues)
                    .catch((error: any) => {
                        throw error
                    })
                    .then((resp: any) => {
                        jobHazardStore.getOngoingJobHazardAnalysis(route.params.job_id as string);
                        return resp
                    });
                router.push(`/hazard-analysis`);
            }
        });


        setLoading(false)

    } catch (error) {
        console.log(error)
        setLoading(false)
    }

}
const fields = ref({
    title: "",
    organization_id: getActiveOrg.value?.uuid,

});

const fieldRules: any = ref({
    title: [
        (v: string) => !!v || 'Title  is required',
    ],
})

const save = async (e: any) => {
    e.preventDefault();

    try {
        setLoading(true)

        const values = { ...fields.value }

        let objectValues = {
            "organization_id": values?.organization_id,
            "job_hazard_id": getOngoingJobHazardAnalysis.value?.uuid,
            "title": values?.title,
        }

        const resp = await jobHazardStore.addJobHazardAnalysis(objectValues)
            .catch((error: any) => {
                console.log(error)
                throw error
            })
            .then((resp: any) => {
                return resp
            });

        if (resp?.message == 'success') {
            setLoading(false)
            setDialog(false)

            fields.value.title = "";

            jobHazardStore.getOngoingJobHazardAnalysis(route.params.job_id as string)


        }

        setLoading(false)
        setDialog(false)



    } catch (error) {
        console.log(error)
        setLoading(false)
        setDialog(false)
    }

}

const thankyou = ref(false);

const tab = ref('tab-1');
const checkbox = ref([]);

function changeTab(e: string) {
    tab.value = e;
}
const blankFn = () => {
    console.log('black')
}



</script>

<template>
    <div>
        <BaseBreadcrumb :title="page.title" :breadcrumbs="breadcrumbs"></BaseBreadcrumb>


        <v-row v-if="getOngoingJobHazardAnalysis">
            <v-col cols="12" md="12">

                <UiParentCard variant="outlined">
                    <v-card-text class="text-center">
                        <!-- <h1 class="text-uppercase mb-3"> {{ getOngoingJobHazardAnalysis?.inspection?.facility_name }}</h1> -->
                    </v-card-text>
                    <v-card-text class="">
                        <VRow>
                            <VCol cols='12' class="text-left">
                                <v-table class="border-lg">
                                    <thead>
                                        <!-- <tr>
                                            <th class="text-left">
                                                
                                            </th>
                                            <th class="text-left">
                                                
                                            </th>
                                        </tr> -->
                                    </thead>
                                    <tbody class="border-lg">
                                        <tr>
                                            <td colspan="2" class="border-lg">
                                                <div class="pa-8">
                                                    <label class="text-subtitle-1">Company</label>
                                                    <p class="text-body-1"> {{
                                                        `${getOngoingJobHazardAnalysis?.organization?.name}` }}</p>
                                                </div>
                                            </td>
                                            <td colspan="2" class="border-lg">

                                                <div class="pa-8">
                                                    <label class="text-subtitle-1">JHA for</label>
                                                    <p class="text-body-1"> {{ `${getOngoingJobHazardAnalysis?.title}`
                                                        }}</p>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="border-lg">
                                                <div class="pa-8">
                                                    <label class="text-subtitle-1">Prepared By</label>
                                                    <p class="text-body-1"> {{
                                                        `${getOngoingJobHazardAnalysis?.prepared_user?.full_name}` }}
                                                    </p>
                                                </div>
                                            </td>
                                            <td class="border-lg">

                                                <div class="pa-8">
                                                    <label class="text-subtitle-1">Date</label>
                                                    <!-- <p class="text-body-1"> {{ `${getOngoingJobHazardAnalysis?.title}` }}</p> -->

                                                    <p class="text-body-1"> {{
                                                        `${formatDate(getOngoingJobHazardAnalysis?.prepared_date)}` }}
                                                    </p>
                                                </div>
                                            </td>
                                            <td class="border-lg">
                                                <div class="pa-8">
                                                    <label class="text-subtitle-1">Reviewed/Approved By</label>
                                                    <p class="text-body-1"> {{
                                                        getOngoingJobHazardAnalysis?.reviewer_user ?
                                                        `${getOngoingJobHazardAnalysis?.reviewer_user?.full_name}` : "-" }}
                                                    </p>
                                                </div>
                                            </td>
                                            <td class="border-lg">

                                                <div class="pa-8">
                                                    <label class="text-subtitle-1">Date</label>
                                                    <p class="text-body-1"> {{
                                                        getOngoingJobHazardAnalysis?.reviewed_date ?
                                                        `${formatDate(getOngoingJobHazardAnalysis?.reviewed_date)}` : `
                                                        - ` }}</p>
                                                </div>
                                            </td>
                                        </tr>

                                    </tbody>
                                </v-table>

                            </VCol>
                            <VCol cols="12">

                                <v-sheet>
                                    <v-btn color="primary" class="mr-2" @click="setDialog(true)">Add Job Step</v-btn>

                                    <v-dialog v-model="dialog" max-width="600">
                                        <v-card>
                                            <v-card-text>
                                                <div class="d-flex justify-space-between">
                                                    <h3 class="text-h3">Add Job Hazard Analysis Step</h3>
                                                    <v-btn icon @click="setDialog(false)" size="small" flat>
                                                        <XIcon size="16" />
                                                    </v-btn>
                                                </div>
                                            </v-card-text>
                                            <v-divider></v-divider>

                                            <v-card-text>

                                                <VForm v-model="valid" ref="formContainer" fast-fail lazy-validation
                                                    @submit.prevent="save" class="py-1">
                                                    <VRow class="mt-5 mb-3">

                                                        <VCol cols="12" md="12">
                                                            <v-label
                                                                class="text-subtitle-1 font-weight-medium pb-1">Title</v-label>
                                                            <VTextField type="text" v-model="fields.title"
                                                                :rules="fieldRules.title" required variant="outlined"
                                                                label="Title"
                                                                :color="fields.title.length > 2 ? 'success' : 'primary'">
                                                            </VTextField>
                                                        </VCol>

                                                        <VCol cols="12" lg="12" class="text-right">
                                                            <v-btn color="error" @click="setDialog(false)"
                                                                variant="text">Cancel</v-btn>

                                                            <v-btn color="primary" type="submit" :loading="loading"
                                                                :disabled="!valid" @click="save">
                                                                <span v-if="!loading">
                                                                    Submit
                                                                </span>
                                                                <clip-loader v-else :loading="loading" color="white"
                                                                    :size="'25px'"></clip-loader>
                                                            </v-btn>

                                                        </VCol>
                                                    </VRow>
                                                </VForm>
                                            </v-card-text>
                                        </v-card>
                                    </v-dialog>


                                    <v-dialog v-model="stepDialog" max-width="600">
                                        <v-card>
                                            <v-card-text>
                                                <div class="d-flex justify-space-between">
                                                    <h3 class="text-h3">Job Hazard Analysis Step</h3>
                                                    <v-btn icon @click="setStepDialog(false)" size="small" flat>
                                                        <XIcon size="16" />
                                                    </v-btn>
                                                </div>
                                            </v-card-text>
                                            <v-divider></v-divider>

                                            <v-card-text>

                                                <VForm v-model="valid" ref="formContainer" fast-fail lazy-validation
                                                    @submit.prevent="safeData" class="py-1">
                                                    <VRow class="mt-5 mb-3">
                                                        <template v-if="stepFields?.section == 'parties'">

                                                            <VCol cols="12" md="12">
                                                                <v-label
                                                                    class="text-subtitle-1 font-weight-medium pb-1">Full
                                                                    name</v-label>
                                                                <VTextField type="text" v-model="stepFields.name"
                                                                    :rules="stepFieldRules.code" required
                                                                    variant="outlined" label="name"
                                                                    :color="stepFields.name.length > 2 ? 'success' : 'primary'">
                                                                </VTextField>
                                                            </VCol>
                                                            <VCol cols="12" md="12">
                                                                <v-label
                                                                    class="text-subtitle-1 font-weight-medium pb-1">Designation</v-label>
                                                                <VTextField type="text" v-model="stepFields.designation"
                                                                    :rules="stepFieldRules.code" required
                                                                    variant="outlined" label="designation"
                                                                    :color="stepFields.designation.length > 2 ? 'success' : 'primary'">
                                                                </VTextField>
                                                            </VCol>
                                                        </template>
                                                        <template v-else>
                                                            <VCol cols="12" md="12">
                                                                <v-label
                                                                    class="text-subtitle-1 font-weight-medium pb-1">{{
                                                                    stepTitle[0] }}</v-label>
                                                                <VTextField type="text" v-model="stepFields.code"
                                                                    :rules="stepFieldRules.code" required
                                                                    variant="outlined" label="code" readonly
                                                                    :color="stepFields.code.length > 2 ? 'success' : 'primary'">
                                                                </VTextField>
                                                            </VCol>

                                                            <VCol cols="12" md="12">
                                                                <v-label
                                                                    class="text-subtitle-1 font-weight-medium pb-1">{{
                                                                    stepTitle[1] }}</v-label>
                                                                <VTextarea type="text" v-model="stepFields.description"
                                                                    :rules="stepFieldRules.description" required
                                                                    variant="outlined" label="Description"
                                                                    :color="stepFields.description.length > 2 ? 'success' : 'primary'">
                                                                </VTextarea>
                                                            </VCol>
                                                        </template>

                                                        <VCol cols="12" lg="12" class="text-right">
                                                            <v-btn color="error" @click="setStepDialog(false)"
                                                                variant="text">Cancel</v-btn>

                                                            <v-btn color="primary" type="submit" :loading="loading"
                                                                :disabled="!valid" @click="safeData">
                                                                <span v-if="!loading">
                                                                    Submit
                                                                </span>
                                                                <clip-loader v-else :loading="loading" color="white"
                                                                    :size="'25px'"></clip-loader>
                                                            </v-btn>

                                                        </VCol>
                                                    </VRow>
                                                </VForm>
                                            </v-card-text>
                                        </v-card>
                                    </v-dialog>

                                </v-sheet>
                            </VCol>
                            <VCol cols='12' class="text-center">

                                <h2 class="text-uppercase mb-3">STEPS</h2>

                                <v-table class="border-lg ">
                                    <thead>
                                        <tr>
                                            <th class="text-left  border-lg">
                                                S/N
                                            </th>
                                            <th class="text-left  border-lg min-w-20">
                                                Step Title
                                            </th>
                                            <th class="text-left  border-lg">
                                                Top Event (TE)
                                            </th>
                                            <th class="text-left  border-lg">
                                                Hazard/ Hazard Sources (Haz)
                                            </th>
                                            <th class="text-left  border-lg">
                                                Target (Tgt)
                                            </th>
                                            <th class="text-left  border-lg">
                                                Consequence (C)
                                            </th>
                                            <th class="text-left  border-lg">
                                                Preventive Actions
                                            </th>
                                            <th class="text-left  border-lg">
                                                RCP
                                            </th>
                                            <th class="text-left  border-lg">
                                                Recovery Measures
                                            </th>
                                            <th class="text-left  border-lg">
                                                Action Party
                                            </th>
                                            <th>

                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="border-lg">
                                        <template v-if="getOngoingJobHazardAnalysis.steps?.length > 0">
                                            <tr v-for="(step, index) in getOngoingJobHazardAnalysis.steps" :key="step">
                                                <td colspan="1" class="text-left border-lg">
                                                    {{ computedIndex(index) }}
                                                </td>
                                                <td colspan="1" class="text-left border-lg">

                                                    <div class="">
                                                        <p class="text-body-1"> {{ `${step?.title}` }}</p>
                                                    </div>
                                                </td>
                                                <td colspan="1" class="text-left border-lg">

                                                    <v-btn color="primary" size='small' flat
                                                        @click="setStepDialog(true, step?.id, 'top_events', [`TE${step?.top_events?.length + 1}`, 'Top Event'])">Add
                                                        top
                                                        event</v-btn>

                                                    <div class="pa-4 float-start"
                                                        v-for="(item, index) in step?.top_events" :key="item">
                                                        <label class="text-subtitle-1">{{ `(${computedIndex(index)})
                                                            ${item?.code}` }}</label>
                                                        <p class="text-body-1"> {{ `${item?.description}` }}</p>
                                                        <div class="d-flex flex-row gap-2">
                                                            <v-btn color="info" size='small' flat
                                                                @click="setStepDialog(true, step?.id, 'top_events', [`TE${step?.top_events?.length + 1}`, 'Top Event'], item?.id)">Edit</v-btn>
                                                            <v-btn color="error" size='small' flat
                                                                @click="deleteItem(step?.id, 'top_events', item?.id)">Delete</v-btn>
                                                        </div>

                                                    </div>

                                                </td>
                                                <td colspan="1" class="text-left border-lg">

                                                    <v-btn color="primary" size='small' flat
                                                        @click="setStepDialog(true, step?.id, 'sources', [`Haz${step?.sources?.length + 1}`, 'Hazard/Hazard Source'])">Add
                                                        hazard
                                                        source</v-btn>

                                                    <div class="pa-4 float-start" v-for="(item, index) in step?.sources"
                                                        :key="item">
                                                        <label class="text-subtitle-1">{{ `(${computedIndex(index)})
                                                            ${item?.code}` }}</label>
                                                        <p class="text-body-1"> {{ `${item?.description}` }}</p>
                                                        <div class="d-flex flex-row gap-2">
                                                            <v-btn color="info" size='small' flat
                                                                @click="setStepDialog(true, step?.id, 'sources', [`Haz${step?.sources?.length + 1}`, 'Hazard/Hazard Source'], item?.id)">Edit</v-btn>
                                                            <v-btn color="error" size='small' flat
                                                                @click="deleteItem(step?.id, 'sources', item?.id)">Delete</v-btn>
                                                        </div>

                                                    </div>

                                                </td>
                                                <td colspan="1" class="text-left border-lg">

                                                    <v-btn color="primary" size='small' flat
                                                        @click="setStepDialog(true, step?.id, 'targets', [`Tgt${step?.targets?.length + 1}`, 'Target'])">Add
                                                        target</v-btn>

                                                    <div class="pa-4 float-start" v-for="(item, index) in step?.targets"
                                                        :key="item">
                                                        <label class="text-subtitle-1">{{ `(${computedIndex(index)})
                                                            ${item?.code}` }}</label>
                                                        <p class="text-body-1"> {{ `${item?.description}` }}</p>
                                                        <div class="d-flex flex-row gap-2">
                                                            <v-btn color="info" size='small' flat
                                                                @click="setStepDialog(true, step?.id, 'targets', [`Tgt${step?.targets?.length + 1}`, 'Target'], item?.id)">Edit</v-btn>
                                                            <v-btn color="error" size='small' flat
                                                                @click="deleteItem(step?.id, 'targets', item?.id)">Delete</v-btn>
                                                        </div>

                                                    </div>
                                                </td>
                                                <td colspan="1" class="text-left border-lg">

                                                    <v-btn color="primary" size='small' flat
                                                        @click="setStepDialog(true, step?.id, 'consequences', [`TE${step?.consequences?.length + 1}`, 'Consequence'])">Add
                                                        consequence</v-btn>

                                                    <div class="pa-4 float-start"
                                                        v-for="(item, index) in step?.consequences" :key="item">
                                                        <label class="text-subtitle-1">{{ `(${computedIndex(index)})
                                                            ${item?.code}` }}</label>
                                                        <p class="text-body-1"> {{ `${item?.description}` }}</p>
                                                        <div class="d-flex flex-row gap-2">
                                                            <v-btn color="info" size='small' flat
                                                                @click="setStepDialog(true, step?.id, 'consequences', [`TE${step?.consequences?.length + 1}`, 'Consequence'], item?.id)">Edit</v-btn>
                                                            <v-btn color="error" size='small' flat
                                                                @click="deleteItem(step?.id, 'consequences', item?.id)">Delete</v-btn>
                                                        </div>

                                                    </div>
                                                </td>
                                                <td colspan="1" class="text-left border-lg">

                                                    <v-btn color="primary" size='small' flat
                                                        @click="setStepDialog(true, step?.id, 'preventives', [`TE${step?.preventives?.length + 1}`, 'Preventive Action'])">Add
                                                        preventive action</v-btn>

                                                    <div class="pa-4 float-start"
                                                        v-for="(item, index) in step?.preventives" :key="item">
                                                        <label class="text-subtitle-1">{{ `(${computedIndex(index)})
                                                            ${item?.code}` }}</label>
                                                        <p class="text-body-1"> {{ `${item?.description}` }}</p>
                                                        <div class="d-flex flex-row gap-2">
                                                            <v-btn color="info" size='small' flat
                                                                @click="setStepDialog(true, step?.id, 'preventives', [`TE${step?.preventives?.length + 1}`, 'Preventive Action'], item?.id)">Edit</v-btn>
                                                            <v-btn color="error" size='small' flat
                                                                @click="deleteItem(step?.id, 'preventives', item?.id)">Delete</v-btn>
                                                        </div>

                                                    </div>
                                                </td>
                                                <td colspan="1" class="text-left border-lg">

                                                    <v-btn color="primary" size='small' flat
                                                        @click="setStepDialog(true, step?.id, 'rcps', [`TE${step?.rcps?.length + 1}`, 'Risk Control Priority'])">Add
                                                        risk control
                                                        priority</v-btn>

                                                    <div class="pa-4 float-start" v-for="(item, index) in step?.rcps"
                                                        :key="item">
                                                        <label class="text-subtitle-1">{{ `(${computedIndex(index)})
                                                            ${item?.code}` }}</label>
                                                        <p class="text-body-1"> {{ `${item?.description}` }}</p>
                                                        <div class="d-flex flex-row gap-2">
                                                            <v-btn color="info" size='small' flat
                                                                @click="setStepDialog(true, step?.id, 'rcps', [`TE${step?.rcps?.length + 1}`, 'Risk Control Priority'], item?.id)">Edit</v-btn>
                                                            <v-btn color="error" size='small' flat
                                                                @click="deleteItem(step?.id, 'rcps', item?.id)">Delete</v-btn>
                                                        </div>

                                                    </div>
                                                </td>
                                                <td colspan="1" class="text-left border-lg">

                                                    <v-btn color="primary" size='small' flat
                                                        @click="setStepDialog(true, step?.id, 'recoveries', [`C${step?.recoveries?.length + 1}`, 'Recovery Measures'])">Add
                                                        recovery measure</v-btn>

                                                    <div class="pa-4 float-start"
                                                        v-for="(item, index) in step?.recoveries" :key="item">
                                                        <label class="text-subtitle-1">{{ `(${computedIndex(index)})
                                                            ${item?.code}` }}</label>
                                                        <p class="text-body-1"> {{ `${item?.description}` }}</p>
                                                        <div class="d-flex flex-row gap-2">
                                                            <v-btn color="info" size='small' flat
                                                                @click="setStepDialog(true, step?.id, 'recoveries', [`C${step?.recoveries?.length + 1}`, 'Recovery Measures'], item?.id)">Edit</v-btn>
                                                            <v-btn color="error" size='small' flat
                                                                @click="deleteItem(step?.id, 'recoveries', item?.id)">Delete</v-btn>
                                                        </div>

                                                    </div>
                                                </td>
                                                <td colspan="1" class="text-left border-lg">

                                                    <v-btn color="primary" size='small' flat
                                                        @click="setStepDialog(true, step?.id, 'parties', [`TE${step?.parties?.length + 1}`, 'Designation'])">Add</v-btn>

                                                    <div class="pa-4 float-start" v-for="(item, index) in step?.parties"
                                                        :key="item">
                                                        <label class="text-subtitle-1">{{ `(${computedIndex(index)})
                                                            ${item?.full_name}` }}</label>
                                                        <p class="text-body-1"> {{ `(${item?.designation})` }}</p>
                                                        <div class="d-flex flex-row gap-2">
                                                            <v-btn color="info" size='small' flat
                                                                @click="setStepDialog(true, step?.id, 'parties', [`TE${step?.parties?.length + 1}`, 'Designation'], item?.id)">Edit</v-btn>
                                                            <v-btn color="error" size='small' flat
                                                                @click="deleteItem(step?.id, 'parties', item?.id)">Delete</v-btn>
                                                        </div>

                                                    </div>
                                                </td>
                                                <td colspan="1" class="text-left border-lg">

                                                    <v-btn color="error" size='small' flat
                                                        @click="removeStepItem(step?.id)">Remove Step</v-btn>

                                                </td>
                                            </tr>
                                        </template>

                                    </tbody>
                                </v-table>

                            </VCol>
                            <VCol cols='12'>
                                <v-btn color="success" v-if="getOngoingJobHazardAnalysis.steps?.length > 0" flat
                                    @click="setCompleteDialog(true)">Send For
                                    Review</v-btn>
                                <!-- <v-btn color="success" v-if="getOngoingJobHazardAnalysis.steps?.length > 0" flat
                                    @click="completeJob">Send For
                                    Review</v-btn> -->
                                <!--  -->

                                <v-sheet>

                                    <v-dialog v-model="completeDialog" max-width="600">
                                        <v-card>
                                            <v-card-text>
                                                <div class="d-flex justify-space-between">
                                                    <h3 class="text-h3">Select Reviewer</h3>
                                                    <v-btn icon @click="setCompleteDialog(false)" size="small" flat>
                                                        <XIcon size="16" />
                                                    </v-btn>
                                                </div>
                                            </v-card-text>
                                            <v-divider></v-divider>

                                            <v-card-text>

                                                <VForm v-model="valid" ref="formContainer" fast-fail lazy-validation
                                                    @submit.prevent="completeJob" class="py-1">
                                                    <VRow class="mt-5 mb-3">


                                                        <VCol cols="12" md="12">
                                                            <v-label class="font-weight-medium pb-1">Select
                                                                Reviewer</v-label>
                                                            <VSelect v-model="stepFields.reviewerId"
                                                                :items="getTeamMembersExcept"
                                                                :rules="stepFieldRules.reviewerId" label="Select"
                                                                :selected="''" item-title='full_name' item-value="uuid"
                                                                single-line variant="outlined" class="text-capitalize">
                                                            </VSelect>
                                                        </VCol>

                                                        <VCol cols="12" lg="12" class="text-right">
                                                            <v-btn color="error" @click="setCompleteDialog(false)"
                                                                variant="text">Cancel</v-btn>

                                                            <v-btn color="primary" type="submit" :loading="loading"
                                                                :disabled="!valid" @click="completeJob">
                                                                <span v-if="!loading">
                                                                    Complete
                                                                </span>
                                                                <clip-loader v-else :loading="loading" color="white"
                                                                    :size="'25px'"></clip-loader>
                                                            </v-btn>

                                                        </VCol>
                                                    </VRow>
                                                </VForm>
                                            </v-card-text>
                                        </v-card>
                                    </v-dialog>
                                </v-sheet>
                            </VCol>
                        </VRow>
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

.min-w-20 {
    min-width: 20px;
}
</style>
