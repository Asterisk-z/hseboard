<script setup lang="ts">
import { ref, onMounted, computed, watch } from 'vue';
import { useRoute } from 'vue-router'
import { router } from '@/router';

import BaseBreadcrumb from '@/components/shared/BaseBreadcrumb.vue';
import UiParentCard from '@/components/shared/UiParentCard.vue';
import { useOrganizationStore } from '@/stores/organizationStore';
import { useAuthStore } from '@/stores/auth';
import { useJobHazardStore } from '@/stores/jobHazardStore';
import { useFormatter } from '@/composables/formatter';
import moment from 'moment'
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
        text: 'Review',
        disabled: true,
        href: '#'
    }
]);

const route = useRoute()
const authStore = useAuthStore();
const organizationStore = useOrganizationStore();
const jobHazardStore = useJobHazardStore();
const { formatDate } = useFormatter();


onMounted(() => {
    jobHazardStore.getReviewJobHazardAnalysis(route.params.job_id as string)
});

const computedIndex = (index: any) => ++index;
const getAuthUser: any = computed(() => (authStore.loggedUser));
const getReviewJobHazardAnalysis: any = computed(() => (jobHazardStore.reviewJobHazard));
const getActiveOrg: any = computed(() => (organizationStore.getActiveOrg()));
const isLoggedInUserOwnsActionOrg: any = computed(() => (getAuthUser.value?.id == getActiveOrg.value?.user_id));
const isLoggedInUserIsReviewer: any = computed(() => (getAuthUser.value?.id == getReviewJobHazardAnalysis.value?.reviewed_by));


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

const stepFields = ref({
    reason: "",
    status: "",
    organization_id: getActiveOrg.value?.uuid,

});

const stepFieldRules: any = ref({
    reason: [
        (v: string) => !!v || 'Code  is required',
    ],
})


const stepDialog = ref(false);
const setStepDialog = (value: boolean, status: string = '') => {
    stepDialog.value = value;
    stepFields.value.status = status

}



const completeJob = async (step = null) => {

    try {
        setLoading(true)

        let objectValues = {

            "organization_id": getActiveOrg.value?.uuid,
            "job_hazard_id": getReviewJobHazardAnalysis.value?.uuid,
            "status": stepFields.value.status,
            "reason": stepFields.value.reason
        }

        const resp = await jobHazardStore.actionJobHazardAnalysis(objectValues)
            .catch((error: any) => {
                throw error
            })
            .then((resp: any) => {
                return resp
            });
        //     }
        // });
        if (resp) {
            jobHazardStore.getReviewJobHazardAnalysis(route.params.job_id as string);
            setStepDialog(false)
        }


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


        <v-row v-if="getReviewJobHazardAnalysis">
            <v-col cols="12" md="12">

                <UiParentCard variant="outlined">
                    <v-card-text class="text-center">
                        <!-- <h1 class="text-uppercase mb-3"> {{ getReviewJobHazardAnalysis?.inspection?.facility_name }}</h1> -->
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
                                                <div class="pa-8 d-flex  align-center">
                                                    <v-avatar size="80"
                                                        v-if="getReviewJobHazardAnalysis?.organization?.media">
                                                        <img :src="getReviewJobHazardAnalysis?.organization?.media?.full_url"
                                                            height="80" alt="image" />
                                                    </v-avatar>
                                                    <div>
                                                        <label class="text-subtitle-1">Company</label>
                                                        <p class="text-body-1"> {{
                                                            `${getReviewJobHazardAnalysis?.organization?.name}` }}</p>
                                                    </div>

                                                </div>
                                            </td>
                                            <td colspan="2" class="border-lg">

                                                <div class="pa-8">
                                                    <label class="text-subtitle-1">JHA for</label>
                                                    <p class="text-body-1"> {{ `${getReviewJobHazardAnalysis?.title}` }}
                                                    </p>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="border-lg">
                                                <div class="pa-8">
                                                    <label class="text-subtitle-1">Prepared By</label>
                                                    <p class="text-body-1"> {{
                                                        `${getReviewJobHazardAnalysis?.prepared_user?.full_name}` }}</p>
                                                </div>
                                            </td>
                                            <td class="border-lg">

                                                <div class="pa-8">
                                                    <label class="text-subtitle-1">Date</label>
                                                    <!-- <p class="text-body-1"> {{ `${getReviewJobHazardAnalysis?.title}` }}</p> -->

                                                    <p class="text-body-1"> {{
                                                        `${formatDate(getReviewJobHazardAnalysis?.prepared_date)}` }}
                                                    </p>
                                                </div>
                                            </td>
                                            <td class="border-lg">
                                                <div class="pa-8">
                                                    <label class="text-subtitle-1">Reviewed/Approved By</label>
                                                    <p class="text-body-1"> {{ getReviewJobHazardAnalysis?.reviewer_user
                                                        ? `${getReviewJobHazardAnalysis?.reviewer_user?.full_name}` :
                                                        "-" }}</p>
                                                </div>
                                            </td>
                                            <td class="border-lg">

                                                <div class="pa-8">
                                                    <label class="text-subtitle-1">Date</label>
                                                    <p class="text-body-1"> {{ getReviewJobHazardAnalysis?.reviewed_date
                                                        ? `${formatDate(getReviewJobHazardAnalysis?.reviewed_date)}` :
                                                        '-' }}</p>
                                                </div>
                                            </td>
                                        </tr>

                                    </tbody>
                                </v-table>

                            </VCol>

                            <VCol cols='12' class="text-center">

                                <h2 class="text-uppercase mb-3">STEPS</h2>

                                <v-table class="border-lg ">
                                    <thead>
                                        <tr>
                                            <th class="text-left  border-lg">
                                                S/N
                                            </th>
                                            <th class="text-left  border-lg">
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
                                        </tr>
                                    </thead>
                                    <tbody class="border-lg">
                                        <template v-if="getReviewJobHazardAnalysis.steps?.length > 0">
                                            <tr v-for="(step, index) in getReviewJobHazardAnalysis.steps" :key="step">
                                                <td colspan="1" class="text-left border-lg">
                                                    {{ computedIndex(index) }}
                                                </td>
                                                <td colspan="1" class="text-left border-lg">

                                                    <div class="">
                                                        <p class="text-body-1"> {{ `${step?.title}` }}</p>
                                                    </div>
                                                </td>
                                                <td colspan="1" class="text-left border-lg">


                                                    <div class="pa-4 float-start"
                                                        v-for="(item, index) in step?.top_events" :key="item">
                                                        <label class="text-subtitle-1">{{ `(${computedIndex(index)})
                                                            ${item?.code}` }}</label>
                                                        <p class="text-body-1"> {{ `${item?.description}` }}</p>

                                                    </div>

                                                </td>
                                                <td colspan="1" class="text-left border-lg">


                                                    <div class="pa-4 float-start" v-for="(item, index) in step?.sources"
                                                        :key="item">
                                                        <label class="text-subtitle-1">{{ `(${computedIndex(index)})
                                                            ${item?.code}` }}</label>
                                                        <p class="text-body-1"> {{ `${item?.description}` }}</p>

                                                    </div>

                                                </td>
                                                <td colspan="1" class="text-left border-lg">


                                                    <div class="pa-4 float-start" v-for="(item, index) in step?.targets"
                                                        :key="item">
                                                        <label class="text-subtitle-1">{{ `(${computedIndex(index)})
                                                            ${item?.code}` }}</label>
                                                        <p class="text-body-1"> {{ `${item?.description}` }}</p>

                                                    </div>
                                                </td>
                                                <td colspan="1" class="text-left border-lg">

                                                    <div class="pa-4 float-start"
                                                        v-for="(item, index) in step?.consequences" :key="item">
                                                        <label class="text-subtitle-1">{{ `(${computedIndex(index)})
                                                            ${item?.code}` }}</label>
                                                        <p class="text-body-1"> {{ `${item?.description}` }}</p>
                                                    </div>
                                                </td>
                                                <td colspan="1" class="text-left border-lg">


                                                    <div class="pa-4 float-start"
                                                        v-for="(item, index) in step?.preventives" :key="item">
                                                        <label class="text-subtitle-1">{{ `(${computedIndex(index)})
                                                            ${item?.code}` }}</label>
                                                        <p class="text-body-1"> {{ `${item?.description}` }}</p>

                                                    </div>
                                                </td>
                                                <td colspan="1" class="text-left border-lg">

                                                    <div class="pa-4 float-start" v-for="(item, index) in step?.rcps"
                                                        :key="item">
                                                        <label class="text-subtitle-1">{{ `(${computedIndex(index)})
                                                            ${item?.code}` }}</label>
                                                        <p class="text-body-1"> {{ `${item?.description}` }}</p>

                                                    </div>
                                                </td>
                                                <td colspan="1" class="text-left border-lg">


                                                    <div class="pa-4 float-start"
                                                        v-for="(item, index) in step?.recoveries" :key="item">
                                                        <label class="text-subtitle-1">{{ `(${computedIndex(index)})
                                                            ${item?.code}` }}</label>
                                                        <p class="text-body-1"> {{ `${item?.description}` }}</p>

                                                    </div>
                                                </td>
                                                <td colspan="1" class="text-left border-lg">


                                                    <div class="pa-4 float-start" v-for="(item, index) in step?.parties"
                                                        :key="item">
                                                        <label class="text-subtitle-1">{{ `(${computedIndex(index)})
                                                            ${item?.full_name}` }}</label>
                                                        <p class="text-body-1"> {{ `(${item?.designation})` }}</p>

                                                    </div>
                                                </td>
                                            </tr>
                                        </template>

                                    </tbody>
                                </v-table>

                            </VCol>
                            <VCol cols='12' v-if="getReviewJobHazardAnalysis.is_completed && isLoggedInUserIsReviewer">

                                <v-sheet>
                                    <v-btn color="error" class="mr-2"
                                        @click="setStepDialog(true, 'declined')">Decline</v-btn>
                                    <v-btn color="primary" flat @click="setStepDialog(true, 'approved')">Approve</v-btn>


                                    <v-dialog v-model="stepDialog" max-width="600">
                                        <v-card>
                                            <v-card-text>
                                                <div class="d-flex justify-space-between">
                                                    <h3 class="text-h3"></h3>
                                                    <v-btn icon @click="setStepDialog(false)" size="small" flat>
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
                                                            <v-label
                                                                class="text-subtitle-1 font-weight-medium pb-1">Reason</v-label>
                                                            <VTextarea type="text" v-model="stepFields.reason"
                                                                :rules="stepFieldRules.reason" required
                                                                variant="outlined" label="reason"
                                                                :color="stepFields.reason.length > 2 ? 'success' : 'primary'">
                                                            </VTextarea>
                                                        </VCol>


                                                        <VCol cols="12" lg="12" class="text-right">
                                                            <v-btn color="error" @click="setStepDialog(false)"
                                                                variant="text">Cancel</v-btn>

                                                            <v-btn color="primary" type="submit" :loading="loading"
                                                                :disabled="!valid" @click="completeJob">
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
</style>
