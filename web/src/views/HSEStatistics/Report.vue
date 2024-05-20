<script setup lang="ts">
import { ref, onMounted, computed, watch } from 'vue';
import { useRoute } from 'vue-router'
import { router } from '@/router';

import BaseBreadcrumb from '@/components/shared/BaseBreadcrumb.vue';
import UiParentCard from '@/components/shared/UiParentCard.vue';
import { useOrganizationStore } from '@/stores/organizationStore';
import { useInspectionStore } from '@/stores/inspectionStore';
import { useReportStore } from '@/stores/reportStore';
import { useAuthStore } from '@/stores/auth';
import moment from 'moment'
import Swal from 'sweetalert2'
import {
    CheckIcon,
} from 'vue-tabler-icons';


const page = ref({ title: 'HSE Statistics Report' });
const breadcrumbs = ref([
    {
        text: 'Dashboard',
        disabled: false,
        href: '/dashboard'
    },
    {
        text: 'HSE Statistics',
        disabled: false,
        href: '/hse-statistics'
    },
    {
        text: 'Report',
        disabled: true,
        href: '#'
    }
]);

const route = useRoute()
const authStore = useAuthStore();
const organizationStore = useOrganizationStore();
const inspectionStore = useInspectionStore();
const reportStore = useReportStore();


onMounted(() => {
    reportStore.getCompletedReport(route.params.report_id as string)
});


const computedIndex = (index: any) => ++index;

const getAuthUser: any = computed(() => (authStore.loggedUser));
const getCompletedReport: any = computed(() => (reportStore.report));
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
                        <h2 class="text-uppercase mb-3">HSE Statistics </h2>
                    </v-card-text>
                    <v-card-text class="">
                        <VRow>

                            <VCol cols='12' class="text-left">
                                <!-- <h2 class="text-uppercase mb-3">Inspection SUMMARY</h2> -->

                                <v-table>
                                    <thead>
                                        <tr>
                                            <th class="text-left" colspan="4">
                                                HSE Statistics
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        <tr>
                                            <td colspan="2" rowspan="3">{{
            getCompletedReport?.main_report?.organization?.name }}
                                            </td>
                                            <td>Industry</td>
                                            <td>{{ `${getCompletedReport?.main_report?.organization?.industry?.name}`
                                                }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>HSE Contact</td>
                                            <td>{{ ` ${getCompletedReport?.main_report?.user?.full_name}`
                                                }}</td>
                                        </tr>
                                        <tr>
                                            <td>{{ `Year - ${getCompletedReport?.stats?.year}` }}</td>
                                            <td>{{ `Month - ${getCompletedReport?.stats?.month}` }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="text-left">
                                                S/N
                                            </th>
                                            <th class="text-left">
                                                Parameter (KPIs)
                                            </th>
                                            <th class="text-left">
                                                Target
                                            </th>
                                            <th class="text-left">
                                                Actual
                                            </th>
                                        </tr>

                                        <tr>
                                            <td>1</td>
                                            <td>No. of workers</td>
                                            <td>{{ getCompletedReport?.main_report?.number_of_workers }}</td>
                                            <td>{{ getCompletedReport?.main_report?.number_of_workers }}</td>
                                        </tr>
                                        <tr>
                                            <td>2</td>
                                            <td>Man-hours</td>
                                            <td>{{ getCompletedReport?.stats?.target_man_hours }}</td>
                                            <td>{{ getCompletedReport?.stats?.actual_man_hours }}</td>
                                        </tr>
                                        <tr>
                                            <td>3</td>
                                            <td>% of hours on overtime</td>
                                            <td>{{ '< 3 %' }}</td>
                                            <td>{{ getCompletedReport?.stats?.percentage_of_hours_on_overtime }}</td>
                                        </tr>
                                        <tr>
                                            <td>4</td>
                                            <td>HSE Meetings</td>
                                            <td>{{ getCompletedReport?.main_report?.hse_meetings_target }}</td>
                                            <td>{{ getCompletedReport?.main_report?.hse_meetings_actual }}</td>
                                        </tr>
                                        <tr>
                                            <td>5</td>
                                            <td>Toolbox Talks</td>
                                            <td>{{ getCompletedReport?.main_report?.toolbox_talks_target }}</td>
                                            <td>{{ getCompletedReport?.main_report?.toolbox_talks_actual }}</td>
                                        </tr>
                                        <tr>
                                            <td>6</td>
                                            <td>HSE Audit/Inspection</td>
                                            <td>{{ getCompletedReport?.main_report?.hse_inspection_target }}</td>
                                            <td>{{ getCompletedReport?.main_report?.hse_inspection_actual }}</td>
                                        </tr>
                                        <tr>
                                            <td>7</td>
                                            <td>Drills</td>
                                            <td>{{ getCompletedReport?.main_report?.drills_target }}</td>
                                            <td>{{ getCompletedReport?.main_report?.drills_actual }}</td>
                                        </tr>
                                        <tr>
                                            <td>8</td>
                                            <td>Unsafe Acts (UA)</td>
                                            <td>{{ getCompletedReport?.main_report?.unsafe_acts_target }}</td>
                                            <td>{{ getCompletedReport?.stats?.unsafe_acts_observations }}</td>
                                        </tr>
                                        <tr>
                                            <td>9</td>
                                            <td>Unsafe Conditions (UC)</td>
                                            <td>{{ getCompletedReport?.main_report?.unsafe_conditions_target }}</td>
                                            <td>{{ getCompletedReport?.stats?.unsafe_conditions_observations }}</td>
                                        </tr>
                                        <tr>
                                            <td>10</td>
                                            <td>First Aid Cases (FAC)</td>
                                            <td>{{ '0' }}</td>
                                            <td>{{ getCompletedReport?.stats?.unsafe_conditions_observations }}</td>
                                        </tr>
                                        <tr>
                                            <td>11</td>
                                            <td>Near Misses (NM) with IPM &gt; C3</td>
                                            <td>{{ '0' }}</td>
                                            <td>{{ getCompletedReport?.stats?.near_miss_observations }}</td>
                                        </tr>
                                        <tr>
                                            <td>12</td>
                                            <td>Restricted Work Cases (RWC)</td>
                                            <td>{{ '0' }}</td>
                                            <td>{{ getCompletedReport?.stats?.first_aid_case_observations }}</td>
                                        </tr>
                                        <tr>
                                            <td>13</td>
                                            <td>Road Traffic Accidents (RTA)</td>
                                            <td>{{ '0' }}</td>
                                            <td>{{ getCompletedReport?.stats?.restricted_work_case_observations }}</td>
                                        </tr>
                                        <tr>
                                            <td>14</td>
                                            <td>Medical Treatment Cases (MTC)</td>
                                            <td>{{ '0' }}</td>
                                            <td>{{ getCompletedReport?.stats?.road_traffic_accident_observations }}</td>
                                        </tr>
                                        <tr>
                                            <td>15</td>
                                            <td>Lost Workday Cases (LWC)</td>
                                            <td>{{ '0' }}</td>
                                            <td>{{ getCompletedReport?.stats?.medical_case_treatment_observations }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>16</td>
                                            <td>Number of Days Away From Work </td>
                                            <td>{{ '0' }}</td>
                                            <td>{{ getCompletedReport?.stats?.lost_workday_case_observations }}</td>
                                        </tr>
                                        <tr>
                                            <td>17</td>
                                            <td>Dangerous Occurrences </td>
                                            <td>{{ '0' }}</td>
                                            <td>{{ getCompletedReport?.stats?.dangerous_occurance_observations }}</td>
                                        </tr>
                                        <tr>
                                            <td>18</td>
                                            <td>Permanent Partial Disability (PPD)</td>
                                            <td>{{ '0' }}</td>
                                            <td>{{ getCompletedReport?.stats?.permanent_partial_disability_observations
                                                }}</td>
                                        </tr>
                                        <tr>
                                            <td>19</td>
                                            <td>Permanent Total Disability (PTD)</td>
                                            <td>{{ '0' }}</td>
                                            <td>{{ getCompletedReport?.stats?.permamnent_total_disabilities_observations
                                                }}</td>
                                        </tr>
                                        <tr>
                                            <td>20</td>
                                            <td>Fatality (FAT)</td>
                                            <td>{{ '0' }}</td>
                                            <td>{{ getCompletedReport?.stats?.fatality_observations }}</td>
                                        </tr>
                                        <tr>
                                            <td>21</td>
                                            <td>Lost Time Injury (LTI)</td>
                                            <td>{{ '0' }}</td>
                                            <td>{{ getCompletedReport?.stats?.lost_time_injury }}</td>
                                        </tr>
                                        <tr>
                                            <td>22</td>
                                            <td>Lost Time Injury Frequency (LTIF)</td>
                                            <td>{{ '0' }}</td>
                                            <td>{{ getCompletedReport?.stats?.lost_time_injury_frequency }}</td>
                                        </tr>
                                        <tr>
                                            <td>23</td>
                                            <td>Total Recordable Cases (TRC)</td>
                                            <td>{{ '0' }}</td>
                                            <td>{{ getCompletedReport?.stats?.total_recorded_cases }}</td>
                                        </tr>
                                        <tr>
                                            <td>24</td>
                                            <td>Total Recordable Cases Frequency (TRCF)</td>
                                            <td>{{ '0' }}</td>
                                            <td>{{ getCompletedReport?.stats?.total_recorded_cases_frequency }}
                                            </td>
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
