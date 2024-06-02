<script setup lang="ts">
import { ref, onMounted, computed, watch } from 'vue';
import { useRoute } from 'vue-router'
import { router } from '@/router';

import BaseBreadcrumb from '@/components/shared/BaseBreadcrumb.vue';
import UiParentCard from '@/components/shared/UiParentCard.vue';
import { useOrganizationStore } from '@/stores/organizationStore';
import { useMainAuditStore } from '@/stores/mainAuditStore';
import { useFormatter } from '@/composables/formatter';
import { useAuthStore } from '@/stores/auth';
import moment from 'moment'
import Swal from 'sweetalert2'
import {
    CheckIcon,
} from 'vue-tabler-icons';


const page = ref({ title: 'Audit Summary' });
const breadcrumbs = ref([
    {
        text: 'Dashboard',
        disabled: false,
        href: '/dashboard'
    },
    {
        text: 'Audit Management',
        disabled: false,
        href: '/hse-audit'
    },
    {
        text: 'Report Summary',
        disabled: true,
        href: '#'
    }
]);

const route = useRoute()
const authStore = useAuthStore();
const organizationStore = useOrganizationStore();
const mainAuditStore = useMainAuditStore();
const { formatDate } = useFormatter();


onMounted(() => {
    mainAuditStore.getCompletedMainAudit(route.params.main_audit_id as string)
});

const computedIndex = (index: any) => ++index;


const getAuthUser: any = computed(() => (authStore.loggedUser));
const getCompletedMainAudit: any = computed(() => (mainAuditStore.completedMainAudit));
const getActiveOrg: any = computed(() => (organizationStore.getActiveOrg()));
const isAuditingOrg: any = computed(() => (getCompletedMainAudit.value?.main_audit?.organization_id == getActiveOrg.value?.id));
const isLoggedInUserOwnsActionOrg: any = computed(() => (getAuthUser.value?.id == getActiveOrg.value?.user_id));





const valid = ref(true);;
const formContainer = ref()
const loading = ref(false);
const setLoading = (value: boolean) => {
    loading.value = value;
}



const getRepresentatives: any = computed(() => (getCompletedMainAudit.value?.main_audit?.all_representatives));
const getAuditors: any = computed(() => (getCompletedMainAudit.value?.main_audit?.all_auditors));




const getDocuments: any = computed(() => (getCompletedMainAudit.value?.main_audit?.documents));



const getSchedule: any = computed(() => (getCompletedMainAudit.value?.main_audit?.schedule));



const questionTab = ref('question-tab-0');
const questionOldTab = ref('');

function changeQuestionTab(e: string) {
    questionTab.value = e;
}
// console.log(questionTab.value)
const getQuestions: any = computed(() => (getCompletedMainAudit.value?.main_audit?.questions));

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




const getFindings: any = computed(() => (getCompletedMainAudit.value?.main_audit?.findings));
const getActions: any = computed(() => (getCompletedMainAudit.value?.main_audit?.actions));








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


        <v-row v-if="getCompletedMainAudit">
            <v-col cols="12" md="11">

                <UiParentCard variant="outlined">
                    <v-card-text class="text-center">
                        <h1 class="text-uppercase mb-3"> {{ getCompletedMainAudit?.main_audit?.audit_title }}</h1>
                        <h1 class="text-uppercase mb-3"> {{
            getCompletedMainAudit?.main_audit?.audit_template?.description }}</h1>
                        <h2 class="text-uppercase mb-3">Organization : {{
            getCompletedMainAudit?.main_audit?.recipient_organization?.name }}</h2>
                    </v-card-text>
                    <v-card-text class="">
                        <VRow>
                            <VCol cols='12' md="4">
                                <label class="text-subtitle-1">Start Time</label>
                                <p class="text-body-1"> {{
            `${formatDate(getCompletedMainAudit?.main_audit?.start_date)}` }}</p>
                            </VCol>
                            <VCol cols='12' md="4">
                                <label class="text-subtitle-1">End Time</label>
                                <p class="text-body-1"> {{ `${formatDate(getCompletedMainAudit?.main_audit?.end_date)}`
                                    }}</p>
                            </VCol>
                            <VCol cols='12' md="4">
                                <label class="text-subtitle-1">ISPON Number</label>
                                <p class="text-body-1">{{
            getCompletedMainAudit?.main_audit?.recipient_organization?.ispon ?
                getCompletedMainAudit?.main_audit?.recipient_organization?.ispon : 'NA' }}</p>
                            </VCol>
                            <VCol cols='12' md="4">
                                <label class="text-subtitle-1">Organization</label>
                                <p class="text-body-1">{{
            getCompletedMainAudit?.main_audit?.recipient_organization?.name }}</p>
                            </VCol>
                            <VCol cols='12' md="4">
                                <label class="text-subtitle-1">Address</label>
                                <p class="text-body-1">{{
            getCompletedMainAudit?.main_audit?.recipient_organization?.address }}</p>
                            </VCol>
                            <!-- <VCol cols='12' md="4">
                                <label class="text-subtitle-1">Organization Reps.</label>
                                <p class="text-body-1">Lead Representative : {{ `${getCompletedMainAudit?.main_audit?.lead_representative?.member?.full_name}` }}</p>
                                <p class="text-body-1">Other Representative : {{ `${getCompletedMainAudit?.main_audit?.representatives?.length} Other Representatives` }}</p>
                            </VCol> -->
                            <VCol cols='12' md="12">
                                <h2 class="text-uppercase mb-3">Organization Reps.</h2>
                                <v-table>
                                    <thead>
                                        <tr>
                                            <th class="text-left">
                                                Auditors
                                            </th>
                                            <th class="text-left">
                                                Representatives
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>

                                            <td>
                                                <div v-for="(auditor) in getCompletedMainAudit?.main_audit?.all_auditors"
                                                    :key="auditor" class="text-uppercase mb-3">
                                                    {{ auditor?.member?.full_name }}
                                                    {{ ` - ${auditor?.position.split('_').join(' ')}` }}
                                                </div>
                                            </td>

                                            <td>
                                                <div v-for="(representative) in getCompletedMainAudit?.main_audit?.all_representatives"
                                                    :key="representative" class="text-uppercase mb-3">
                                                    {{ representative?.member?.full_name }}
                                                    {{ ` - ${representative?.position.split('_').join(' ')}` }}
                                                </div>
                                            </td>

                                        </tr>

                                    </tbody>
                                </v-table>
                            </VCol>
                            <VCol cols='12'>
                                <p>
                                    This general audit checklist may be used for all ISPON audit exercises to assess
                                    performance of Health, Safety and Environmental Management System (HSE-MS) and
                                    establish compliance with minimum safety requirements in an organization. The
                                    exercise shall cover all areas of the facility. Issues shall be summarized on the
                                    last page. Recommendations will be made and documented on the summary page. All
                                    recommendations are expected to be completed within the time advised by the Auditor.
                                </p>
                            </VCol>


                            <VCol cols='12' class="text-center">
                                <h2 class="text-uppercase mb-3">AUDIT SUMMARY</h2>
                                <p>Audit Score (%): (Total Score -Total Non Conformance / Maximum Score ) * 100</p>
                                <p>Audit Rating: 80-100% is Excellent, 70-79% is Good, 60-69% is Poor, 0-59% is Fail</p>
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
                                            <td>{{ getCompletedMainAudit?.stats?.total_questions }}</td>

                                        </tr>
                                        <tr>
                                            <td>Total Yes</td>
                                            <td>{{ getCompletedMainAudit?.stats?.number_of_yeses }}</td>

                                        </tr>
                                        <tr>
                                            <td>Total Minor (NC)</td>
                                            <td>{{ getCompletedMainAudit?.stats?.number_of_nc_minor }}</td>

                                        </tr>
                                        <tr>
                                            <td>Total Major (NC)</td>
                                            <td>{{ getCompletedMainAudit?.stats?.number_of_nc_major }}</td>

                                        </tr>
                                        <tr>
                                            <td>Total Non Conformance</td>
                                            <td>{{ getCompletedMainAudit?.stats?.number_of_na }}</td>

                                        </tr>
                                        <tr>
                                            <td>Maximum Score</td>
                                            <td>{{ getCompletedMainAudit?.stats?.maximum_score }}</td>

                                        </tr>
                                        <tr>
                                            <td>Audit score</td>
                                            <td>{{ getCompletedMainAudit?.stats?.audit_score }}%</td>

                                        </tr>
                                        <tr>
                                            <td>Audit rating</td>
                                            <td> <span>{{ getCompletedMainAudit?.stats?.audit_rate }}</span> ( {{
            getCompletedMainAudit?.stats?.audit_score }} %)</td>

                                        </tr>

                                    </tbody>
                                </v-table>

                            </VCol>
                            <VCol cols='12' class="text-center">
                                <h2 class="text-uppercase mb-3">AUDIT FINDING</h2>
                                <p>{{ `${getFindings?.description}` }}</p>
                            </VCol>
                            <VCol cols='12' class="text-center">
                                <h2 class="text-uppercase mb-3">AUDIT RECOMMENDATION</h2>

                                <v-table>
                                    <thead>
                                        <tr>

                                            <th class="text-left">
                                                #
                                            </th>
                                            <th class="text-left">
                                                Title
                                            </th>
                                            <th class="text-left">
                                                Assignor
                                            </th>
                                            <th class="text-left">
                                                Assignee
                                            </th>
                                            <th class="text-left">
                                                Status
                                            </th>
                                            <th class="text-left">
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
