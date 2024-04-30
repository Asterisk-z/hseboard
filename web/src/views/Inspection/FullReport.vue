<script setup lang="ts">
import { ref, onMounted, computed, watch } from 'vue';
import { useRoute } from 'vue-router'
import { router } from '@/router';

import BaseBreadcrumb from '@/components/shared/BaseBreadcrumb.vue';
import UiParentCard from '@/components/shared/UiParentCard.vue';
import { useOrganizationStore } from '@/stores/organizationStore';
import { useInspectionStore } from '@/stores/inspectionStore';
import { useAuthStore } from '@/stores/auth';
import moment from 'moment'
import Swal from 'sweetalert2'
import {
    CheckIcon,
} from 'vue-tabler-icons';


const page = ref({ title: 'Full Inspection Report' });
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
        text: 'Full Report',
        disabled: true,
        href: '#'
    }
]);

const route = useRoute()
const authStore = useAuthStore();
const organizationStore = useOrganizationStore();
const inspectionStore = useInspectionStore();


onMounted(() => {
    inspectionStore.getCompletedInspection(route.params.inspection_id)
});



const getAuthUser = computed(() => (authStore.loggedUser));
const getCompletedInspection = computed(() => (inspectionStore.completedInspection));
const getActiveOrg = computed(() => (organizationStore.getActiveOrg()));
const isLoggedInUserOwnsActionOrg = computed(() => (getAuthUser.value?.id == getActiveOrg.value?.user_id));





const valid = ref(true);;
const formContainer = ref()
const loading = ref(false);
const setLoading = (value: boolean) => {
    loading.value = value;
}







const getSchedule = computed(() => (getCompletedInspection.value?.inspection?.schedule));



const questionTab = ref('question-tab-0');
const questionOldTab = ref('');

function changeQuestionTab(e: string) {
    questionTab.value = e;
}
// console.log(questionTab.value)
const getQuestions = computed(() => (getCompletedInspection.value?.inspection?.questions));

const sendCommentDialog = ref(false);
const setSendCommentDialog = (value: boolean, item_id: string = '', topic_id: string = '', question = {}) => {
    sendCommentDialog.value = value;
    stepFourFields.value.question_id = item_id
    stepFourFields.value.topic_id = topic_id
    stepFourFields.value.comments = question?.comment ? question?.comment  : (question?.response?.comment ? question?.response?.comment : "")
}

const stepFourFields = ref({
    question_id: "",
    topic_id: "",
    comments: "",
});




const getFindings = computed(() => (getCompletedInspection.value?.inspection?.findings));
const getActions = computed(() => (getCompletedInspection.value?.inspection?.actions));








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
                        <h2 class="text-uppercase mb-3">Organization : {{ getCompletedInspection?.inspection?.recipient_organization?.name }}</h2>
                    </v-card-text>
                    <v-card-text class="">
                        <VRow>
                            <VCol cols='12' md="4">
                                <label class="text-subtitle-1">Start Time</label>
                                <p class="text-body-1"> {{ `${moment(getCompletedInspection?.inspection?.start_date).format('MMMM Do YYYY, h:mm a')}` }}</p>
                            </VCol>
                            <VCol cols='12' md="4">
                                <label class="text-subtitle-1">End Time</label>
                                <p class="text-body-1"> {{ `${moment(getCompletedInspection?.inspection?.end_date).format('MMMM Do YYYY, h:mm a')}` }}</p>
                            </VCol>
                            <VCol cols='12' md="4">
                                <label class="text-subtitle-1">ISPON</label>
                                <p class="text-body-1">NA</p>
                            </VCol>
                            <VCol cols='12' md="4">
                                <label class="text-subtitle-1">Organization</label>
                                <p class="text-body-1">{{ getCompletedInspection?.inspection?.recipient_organization?.name }}</p>
                            </VCol>
                            <VCol cols='12' md="4">
                                <label class="text-subtitle-1">Address</label>
                                <p class="text-body-1">{{ getCompletedInspection?.inspection?.recipient_organization?.address }}</p>
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
                                                    <div v-for="(inspector) in getCompletedInspection?.inspection?.all_inspectors" :key="inspector" class="text-uppercase mb-3">
                                                        {{ inspector?.member?.full_name }}
                                                        {{ ` - ${inspector?.position.split('_').join(' ')}`  }}
                                                    </div>
                                                </td>
                                            
                                                <td>
                                                    <div v-for="(representative) in getCompletedInspection?.inspection?.all_representatives" :key="representative" class="text-uppercase mb-3">
                                                        {{ representative?.member?.full_name }}
                                                        {{ ` - ${representative?.position.split('_').join(' ')}`  }}
                                                    </div>
                                                </td>

                                            </tr>
                                        
                                    </tbody>
                                </v-table>
                            </VCol>
                            <VCol cols='12'>
                                <p>
                                    This general audit checklist may be used for all ISPON audit exercises to assess performance of Health, Safety and Environmental Management System (HSE-MS) and establish compliance with minimum safety requirements in an organization. The exercise shall cover all areas of the facility. Issues shall be summarized on the last page. Recommendations will be made and documented on the summary page. All recommendations are expected to be completed within the time advised by the Auditor.
                                </p>
                            </VCol>
                            
                            <VCol cols="12">
                                <h2 class="text-uppercase mb-3">Questions.</h2>
                                <v-table>
                                    <thead>
                                        <tr>
                                            <th class="text-left">
                                                Questions
                                            </th>
                                            <th class="text-left"  width="5%">
                                                Yes
                                            </th>
                                            <th class="text-left"  width="5%">
                                                NC Minor
                                            </th>
                                            <th class="text-left"  width="5%">
                                                NC Major
                                            </th>
                                            <th class="text-left"  width="5%">
                                                NA
                                            </th>
                                            <th class="text-left">
                                                Comments / Evidence
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <template  v-for="(title_question) in getQuestions" >
                                            <tr v-for="(question) in title_question.questions" :key="question">
                                            
                                                <td>{{ `${question?.question}` }}</td>
                                                <td>
                                                    <template  v-if="question?.answer ? question?.answer == 'yes' : question?.response?.answer == 'yes'">
                                                        <CheckIcon stroke-width="1.5" width="20"  />
                                                    </template>
                                                </td>
                                                <td>
                                                    <template  v-if="question?.answer ? question?.answer == 'nc_minor' : question?.response?.answer == 'nc_minor'">
                                                        <CheckIcon stroke-width="1.5" width="20"  />
                                                    </template>
                                                                        
                                                </td>
                                                <td>
                                                    <template  v-if="question?.answer ? question?.answer == 'nc_major' : question?.response?.answer == 'nc_major'">
                                                        <CheckIcon stroke-width="1.5" width="20"  />
                                                    </template>
                                                    
                                                </td>
                                                <td>
                                                    <template  v-if="question?.answer ? question?.answer == 'na' : question?.response?.answer == 'na'">
                                                        <CheckIcon stroke-width="1.5" width="20"  />
                                                    </template>
                                                </td>
                                                <td>
                                                    {{ `${question?.response?.comment ? question?.response?.comment : ""}` }}
                                                </td>
                                            </tr>                                  
                                        </template>
                                        
                                    </tbody>
                                </v-table>
                            </VCol>
                            
                            <VCol cols='12' class="text-center">
                                <h2 class="text-uppercase mb-3">Inspection SUMMARY</h2>
                                <p>Inspection Score (%): (Total Score -Total Non Conformance / Maximum Score ) * 100</p>
                                <p>Inspection Rating: 80-100% is Excellent, 70-79% is Good, 60-69% is Poor, 0-59% is Fail</p>
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
                                                <td> <span>{{ getCompletedInspection?.stats?.inspection_rate }}</span> ( {{ getCompletedInspection?.stats?.inspection_score }} %)</td>

                                            </tr>
                                        
                                    </tbody>
                                </v-table>

                            </VCol>
                            <VCol cols='12' class="text-center">
                                <h2 class="text-uppercase mb-3">INSPECTION FINDING</h2>
                                <p>{{ `${getFindings?.description}`  }}</p>
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
                                                    <tr v-for="(actions, index) in getActions"
                                                        :key="actions">
                                                        <td>{{ ++index }}</td>
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

