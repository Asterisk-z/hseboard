<script setup lang="ts">
import { ref, onMounted, computed, watch } from 'vue';
import { useRoute } from 'vue-router'
import { router } from '@/router';

import BaseBreadcrumb from '@/components/shared/BaseBreadcrumb.vue';
import UiParentCard from '@/components/shared/UiParentCard.vue';
import { useOrganizationStore } from '@/stores/organizationStore';
import { useInspectionStore } from '@/stores/inspectionStore';
import { useAuthStore } from '@/stores/auth';
import { useFormatter } from '@/composables/formatter';
import moment from 'moment'
import Swal from 'sweetalert2'
import {
    CheckIcon,
} from 'vue-tabler-icons';


const page = ref({ title: 'Inspection Summery' });
const breadcrumbs = ref([
    {
        text: 'Dashboard',
        disabled: false,
        href: '/dashboard'
    },
    {
        text: 'Inspection',
        disabled: false,
        href: '/inspections'
    },
    {
        text: 'Summary',
        disabled: true,
        href: '#'
    }
]);

const route = useRoute()
const authStore = useAuthStore();
const organizationStore = useOrganizationStore();
const inspectionStore = useInspectionStore();

const { formatDate } = useFormatter();

onMounted(() => {
    inspectionStore.getCompletedInspection(route.params.inspection_id as string)
});


const computedIndex = (index: any) => ++index;

const getAuthUser: any = computed(() => (authStore.loggedUser));
const getCompletedInspection: any = computed(() => (inspectionStore.completedInspection));
const getActiveOrg: any = computed(() => (organizationStore.getActiveOrg()));
const isLoggedInUserOwnsActionOrg: any = computed(() => (getAuthUser.value?.id == getActiveOrg.value?.user_id));





const valid = ref(true);;
const formContainer = ref()
const loading = ref(false);
const setLoading = (value: boolean) => {
    loading.value = value;
}







const getSchedule: any = computed(() => (getCompletedInspection.value?.inspection?.schedule));



const questionTab = ref('question-tab-0');
const questionOldTab = ref('');

function changeQuestionTab(e: string) {
    questionTab.value = e;
}
// console.log(questionTab.value)
const getQuestions: any = computed(() => (getCompletedInspection.value?.inspection?.questions));

const sendCommentDialog = ref(false);
const setSendCommentDialog = (value: boolean, item_id: string = '', topic_id: string = '', question: {
    comment: string,
    response: {
        comment: string
    }
} = {
        comment: '',
        response: {
            comment: ''
        }
    }) => {
    sendCommentDialog.value = value;
    stepFourFields.value.question_id = item_id
    stepFourFields.value.topic_id = topic_id
    stepFourFields.value.comments = question?.comment ? question?.comment : (question?.response?.comment ? question?.response?.comment : "")
}

const stepFourFields = ref({
    question_id: "",
    topic_id: "",
    comments: "",
});




const getFindings: any = computed(() => (getCompletedInspection.value?.inspection?.findings));
const getActions: any = computed(() => (getCompletedInspection.value?.inspection?.actions));








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


        <v-row v-if="getCompletedInspection">
            <v-col cols="12" md="11">

                <UiParentCard variant="outlined">
                    <v-card-text class="text-center">
                        <h1 class="text-uppercase mb-3"> {{ getCompletedInspection?.inspection?.facility_name }}</h1>
                        <!-- <h1 class="text-uppercase mb-3"> {{ getCompletedInspection?.inspection?.inspection_template?.description }}</h1> -->
                        <h2 class="text-uppercase mb-3">Organization : {{
            getCompletedInspection?.inspection?.recipient_organization?.name }}</h2>
                    </v-card-text>
                    <v-card-text class="">
                        <VRow>
                            <VCol cols='12' md="4">
                                <label class="text-subtitle-1">Start Time</label>
                                <p class="text-body-1"> {{
            `${formatDate(getCompletedInspection?.inspection?.start_date)}` }}</p>
                            </VCol>
                            <VCol cols='12' md="4">
                                <label class="text-subtitle-1">End Time</label>
                                <p class="text-body-1"> {{ `${formatDate(getCompletedInspection?.inspection?.end_date)}`
                                    }}</p>
                            </VCol>
                            <VCol cols='12' md="4">
                                <label class="text-subtitle-1">ISPON Number</label>
                                <p class="text-body-1">{{
            getCompletedInspection?.inspection?.recipient_organization?.ispon ?
                getCompletedInspection?.inspection?.recipient_organization?.ispon : 'NA' }}</p>
                            </VCol>
                            <VCol cols='12' md="4">
                                <label class="text-subtitle-1">Organization</label>
                                <p class="text-body-1">{{
            getCompletedInspection?.inspection?.recipient_organization?.name }}</p>
                            </VCol>
                            <VCol cols='12' md="4">
                                <label class="text-subtitle-1">Address</label>
                                <p class="text-body-1">{{
            getCompletedInspection?.inspection?.recipient_organization?.address }}</p>
                            </VCol>
                            <!-- <VCol cols='12' md="4">
                                <label class="text-subtitle-1">Organization Reps.</label>
                                <p class="text-body-1">Lead Representative : {{ `${getCompletedInspection?.inspection?.lead_representative?.member?.full_name}` }}</p>
                                <p class="text-body-1">Other Representative : {{ `${getCompletedInspection?.inspection?.representatives?.length} Other Representatives` }}</p>
                            </VCol> -->
                            <VCol cols='12' md="12">
                                <h2 class="text-uppercase mb-3">Organization Reps.</h2>
                                <v-table>
                                    <thead>
                                        <tr>
                                            <th class="text-left">
                                                Inspectors
                                            </th>
                                            <th class="text-left">
                                                Representatives
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>

                                            <td>
                                                <div v-for="(inspector) in getCompletedInspection?.inspection?.all_inspectors"
                                                    :key="inspector" class="text-uppercase mb-3">
                                                    {{ inspector?.member?.full_name }}
                                                    {{ ` - ${inspector?.position.split('_').join(' ')}` }}
                                                </div>
                                            </td>

                                            <td>
                                                <div v-for="(representative) in getCompletedInspection?.inspection?.all_representatives"
                                                    :key="representative" class="text-uppercase mb-3">
                                                    {{ representative?.member?.full_name }}
                                                    {{ ` - ${representative?.position.split('_').join(' ')}` }}
                                                </div>
                                            </td>

                                        </tr>

                                    </tbody>
                                </v-table>
                            </VCol>

                            <VCol cols='12' class="text-center">
                                <h2 class="text-uppercase mb-3">Inspection SUMMARY</h2>
                                <p>Inspection Score (%): (Total Score -Total Non Conformance / Maximum Score ) * 100</p>
                                <p>Inspection Rating: 80-100% is Excellent, 70-79% is Good, 60-69% is Poor, 0-59% is
                                    Fail</p>
                                <p>Total score = number of YES</p>
                                <p>Total Non Conformance (NC) = Minor NC + Major NC</p>
                                <p>Maximum Score = Total Score + Minor NC + Major NC</p>

                                <v-table>
                                    <thead>
                                        <tr>
                                            <th class="text-left">

                                            </th>
                                            <th class="text-left">

                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>Total Questions</td>
                                            <td>{{ getCompletedInspection?.stats?.total_questions }}</td>

                                        </tr>
                                        <tr>
                                            <td>Total Yes</td>
                                            <td>{{ getCompletedInspection?.stats?.number_of_yeses }}</td>

                                        </tr>
                                        <tr>
                                            <td>Total Minor (NC)</td>
                                            <td>{{ getCompletedInspection?.stats?.number_of_nc_minor }}</td>

                                        </tr>
                                        <tr>
                                            <td>Total Major (NC)</td>
                                            <td>{{ getCompletedInspection?.stats?.number_of_nc_major }}</td>

                                        </tr>
                                        <tr>
                                            <td>Total Non Conformance</td>
                                            <td>{{ getCompletedInspection?.stats?.number_of_na }}</td>

                                        </tr>
                                        <tr>
                                            <td>Maximum Score</td>
                                            <td>{{ getCompletedInspection?.stats?.maximum_score }}</td>

                                        </tr>
                                        <tr>
                                            <td>Inspection score</td>
                                            <td>{{ getCompletedInspection?.stats?.inspection_score }}%</td>

                                        </tr>
                                        <tr>
                                            <td>Inspection rating</td>
                                            <td> <span>{{ getCompletedInspection?.stats?.inspection_rate }}</span> ( {{
            getCompletedInspection?.stats?.inspection_score }} %)</td>

                                        </tr>

                                    </tbody>
                                </v-table>

                            </VCol>
                            <VCol cols='12' class="text-center">
                                <h2 class="text-uppercase mb-3">INSPECTION FINDING</h2>
                                <p>{{ `${getFindings?.description}` }}</p>
                            </VCol>
                            <VCol cols='12' class="text-center">
                                <h2 class="text-uppercase mb-3">INSPECTION RECOMMENDATION</h2>

                                <v-table>
                                    <thead>
                                        <tr>

                                            <th class="">
                                                #
                                            </th>
                                            <th class="">
                                                Title
                                            </th>
                                            <th class="">
                                                Assignor
                                            </th>
                                            <th class="">
                                                Assignee
                                            </th>
                                            <th class="">
                                                Status
                                            </th>
                                            <th class="">
                                                Priority
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(actions, index) in getActions" :key="actions">
                                            <td>{{ computedIndex(index) }}</td>
                                            <td>{{ `${actions?.title}` }}</td>
                                            <td>{{ `${actions?.description}` }}</td>
                                            <td>{{ `${actions?.assignee?.lastName}
                                                ${actions?.assignee?.firstName}` }}</td>
                                            <td>{{ `${actions?.status}` }}</td>
                                            <td>{{ `${actions?.priority?.description}` }}</td>
                                        </tr>
                                    </tbody>
                                </v-table>
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
