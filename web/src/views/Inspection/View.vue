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


const page = ref({ title: 'View Inspection' });
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
        text: 'View',
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



const getRepresentatives = computed(() => (getCompletedInspection.value?.inspection?.all_representatives));
const getInspectors = computed(() => (getCompletedInspection.value?.inspection?.all_inspectors));




const getDocuments = computed(() => (getCompletedInspection.value?.inspection?.documents));



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
                    <v-card-text>
                        <h1 class="text-uppercase mb-3">Inspection Facility - {{
                            getCompletedInspection?.inspection?.facility_name }}</h1>
                        <h2 class="text-uppercase mb-3">Organization - {{
                            getCompletedInspection?.inspection?.recipient_organization?.name }}</h2>
                    </v-card-text>
                    <v-card-text>
                        <v-tabs v-model="tab" color="primary" class="customTab">
                            <v-tab value="tab-1" rounded="md" class="mb-3 mx-2 text-left" height="70">
                                <UsersIcon stroke-width="1.5" width="20" class="v-icon--start" />
                                <div>
                                    <div>Build Team</div>
                                    <span
                                        class="text-subtitle-2 text-lightText text-medium-emphasis font-weight-medium d-block">Members</span>
                                </div>
                            </v-tab>

                            <v-tab value="tab-2" rounded="md" class="mb-3 mx-2 text-left" height="70">
                                <UsersIcon stroke-width="1.5" width="20" class="v-icon--start" />
                                <div>
                                    <div>Basic Inspection</div>
                                    <span
                                        class="text-subtitle-2 text-lightText text-medium-emphasis font-weight-medium d-block">Details</span>
                                </div>
                            </v-tab>


                            <v-tab value="tab-3" rounded="md" class="mb-3 mx-2 text-left" height="70">
                                <CreditCardIcon stroke-width="1.5" width="20" class="v-icon--start" />
                                <div>
                                    <div>Send Schedule</div>
                                    <span
                                        class="text-subtitle-2 text-lightText text-medium-emphasis font-weight-medium d-block">
                                        To Organization
                                    </span>
                                </div>
                            </v-tab>
                            <v-tab value="tab-4" rounded="md" class="mb-3 mx-2 text-left" height="70">
                                <CreditCardIcon stroke-width="1.5" width="20" class="v-icon--start" />
                                <div>
                                    <div>Onsite Inspection</div>
                                    <span
                                        class="text-subtitle-2 text-lightText text-medium-emphasis font-weight-medium d-block">
                                        Start
                                    </span>
                                </div>
                            </v-tab>
                            <v-tab value="tab-5" rounded="md" class="mb-3 mx-2 text-left" height="70">
                                <CreditCardIcon stroke-width="1.5" width="20" class="v-icon--start" />
                                <div>
                                    <div>Inspection Findings </div>
                                    <span
                                        class="text-subtitle-2 text-lightText text-medium-emphasis font-weight-medium d-block">
                                        And Action
                                    </span>
                                </div>
                            </v-tab>
                        </v-tabs>
                        <v-window v-model="tab" class="customTab">
                            <v-window-item value="tab-1" class="pa-1">

                                <div>
                                    <v-row class="mt-3 px-4">

                                        <v-col cols="12">
                                            <v-table>
                                                <thead>
                                                    <tr>
                                                        <th class="text-left">
                                                            #
                                                        </th>
                                                        <th class="text-left">
                                                            Full Name
                                                        </th>
                                                        <th class="text-left">
                                                            Email
                                                        </th>
                                                        <th class="text-left">
                                                            Position
                                                        </th>
                                                    </tr>
                                                </thead>
                                                <tbody>

                                                    <tr v-for="(member, index) in getInspectors" :key="member">
                                                        <td>{{ ++index }}</td>
                                                        <td>{{ `${member?.member?.lastName}
                                                            ${member?.member?.firstName}` }}</td>
                                                        <td>{{ `${member?.member?.email}` }}</td>
                                                        <td>{{ `${member?.position.split('_').join(' ')}` }}</td>

                                                    </tr>
                                                    <tr v-for="(member, index) in getRepresentatives" :key="member">
                                                        <td>{{ ++index }}</td>
                                                        <td>{{ `${member?.member?.lastName}
                                                            ${member?.member?.firstName}` }}</td>
                                                        <td>{{ `${member?.member?.email}` }}</td>
                                                        <td>{{ `${member?.position.split('_').join(' ')}` }}</td>

                                                    </tr>
                                                </tbody>
                                            </v-table>
                                        </v-col>
                                    </v-row>

                                </div>

                                <v-row class="mt-3">
                                    <v-col cols="12" sm="6">

                                    </v-col>
                                    <v-col cols="12" sm="6" class="text-sm-right">
                                        <v-btn color="primary" @click="changeTab('tab-2')">Next Step</v-btn>
                                    </v-col>
                                </v-row>
                            </v-window-item>
                            <v-window-item value="tab-2" class="pa-1">

                                <div>
                                    <v-row class="mt-3 px-4">


                                        <v-col cols="12">
                                            <v-table>
                                                <thead>
                                                    <tr>
                                                        <th class="text-left">
                                                            #
                                                        </th>
                                                        <th class="text-left">
                                                            Facility Name
                                                        </th>
                                                        <th class="text-left">
                                                            Location
                                                        </th>
                                                        <th class="text-left">
                                                            Description
                                                        </th>
                                                        <th class="text-left">
                                                            Objective
                                                        </th>
                                                        <th class="text-left">
                                                            PPE
                                                        </th>
                                                    </tr>
                                                </thead>
                                                <tbody>

                                                        <tr>
                                                            <td></td>

                                                            <td>{{ `${getCompletedInspection?.inspection?.facility_name}` }}</td>
                                                            <td>{{ `${getCompletedInspection?.inspection?.location}` }}</td>
                                                            <td>{{ `${getCompletedInspection?.inspection?.description}` }}</td>
                                                            <td>{{ `${getCompletedInspection?.inspection?.objective}` }}</td>
                                                            <td>{{ `${getCompletedInspection?.inspection?.ppe}` }}</td>
                                                        </tr>

                                                </tbody>
                                            </v-table>
                                        </v-col>
                                    </v-row>

                                </div>
                                <v-row class="mt-3">
                                    <v-col cols="12" sm="6">

                                    </v-col>
                                    <v-col cols="12" sm="6" class="text-sm-right">
                                        <v-btn color="primary" @click="changeTab('tab-2')">Next
                                            Step</v-btn>
                                    </v-col>
                                </v-row>
                            </v-window-item>

                            <v-window-item value="tab-3" class="pa-1">

                                <div>
                                    <v-row class="mt-3 px-4">


                                        <v-col cols="12">
                                            <v-table>
                                                <thead>
                                                    <tr>

                                                        <th class="text-left">
                                                            #
                                                        </th>
                                                        <th class="text-left">
                                                            Inspector Time
                                                        </th>
                                                        <th class="text-left">
                                                            Representative Time
                                                        </th>
                                                        <th class="text-left">
                                                            Time Agreed
                                                        </th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td></td>
                                                        <td>
                                                            {{ `${getSchedule?.propose_time ? getSchedule?.propose_time
                                                            : ""}` }}
                                                        </td>
                                                        <td>
                                                            {{ `${getSchedule?.suggested_time ?
                                                            getSchedule?.suggested_time : ""}` }}
                                                        </td>
                                                        <td>
                                                            {{ `${getSchedule?.accepted_time ?
                                                            getSchedule?.accepted_time : ""}` }}
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </v-table>
                                        </v-col>
                                    </v-row>

                                </div>
                                <v-row class="mt-3">
                                    <v-col cols="6">
                                        <v-btn color="primary" variant="tonal" @click="changeTab('tab-2')">Back</v-btn>
                                    </v-col>
                                    <v-col cols="6" class="text-right">
                                        <v-btn color="primary" @click="changeTab('tab-4')">Next Step</v-btn>
                                    </v-col>
                                </v-row>
                            </v-window-item>
                            <v-window-item value="tab-4" class="pa-1">

                                <div>
                                    <v-row class="mt-3 px-4">


                                        <v-col cols="12">

                                            <v-window v-model="questionTab" class="customTab">
                                                <template v-if="getQuestions.length > 0">
                                                    <v-window-item v-for="(title_question, index) in getQuestions"
                                                        :key="title_question" :value="`questionTab-${index}`"
                                                        class="pa-1">

                                                        <div>
                                                            <v-row class="mt-3 px-4">

                                                                <v-col cols="12">


                                                                    <v-sheet>
                                                                        <v-dialog v-model="sendCommentDialog"
                                                                            max-width="800">
                                                                            <v-card>



                                                                                <v-card-text>

                                                                                    <VForm v-model="valid"
                                                                                        ref="formContainer" fast-fail
                                                                                        lazy-validation class="py-1">
                                                                                        <VRow class="mt-5 mb-3">

                                                                                            <VCol cols="12" md="12">
                                                                                                <v-label
                                                                                                    class="text-subtitle-1 font-weight-medium pb-1">Comments</v-label>
                                                                                                <VTextarea
                                                                                                    variant="outlined"
                                                                                                    outlined
                                                                                                    name="Comment"
                                                                                                    :readonly="true"
                                                                                                    label="Comment"
                                                                                                    v-model="stepFourFields.comments"
                                                                                                    required
                                                                                                    :color="stepFourFields.comments.length > 10 ? 'success' : 'primary'">
                                                                                                </VTextarea>
                                                                                            </VCol>



                                                                                            <VCol cols="12" lg="12"
                                                                                                class="text-right">
                                                                                                <v-btn color="error"
                                                                                                    @click="setSendCommentDialog(false)"
                                                                                                    variant="text">Close</v-btn>


                                                                                            </VCol>
                                                                                        </VRow>
                                                                                    </VForm>
                                                                                </v-card-text>
                                                                            </v-card>
                                                                        </v-dialog>
                                                                    </v-sheet>
                                                                </v-col>

                                                                <v-col cols="12">
                                                                    <h2>{{title_question.title}}</h2>
                                                                    <v-table>
                                                                        <thead>
                                                                            <tr>
                                                                                <th class="text-left">
                                                                                    Questions
                                                                                </th>
                                                                                <th class="text-left" width="10%">
                                                                                    Yes
                                                                                </th>
                                                                                <th class="text-left" width="10%">
                                                                                    NC Minor
                                                                                </th>
                                                                                <th class="text-left" width="10%">
                                                                                    NC Major
                                                                                </th>
                                                                                <th class="text-left" width="10%">
                                                                                    NA
                                                                                </th>
                                                                                <th class="text-left">
                                                                                    Comments / Evidence
                                                                                </th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                            <!-- {{ title_question.questions }} -->
                                                                            <!-- <template> -->
                                                                            <tr v-for="(question) in title_question.questions"
                                                                                :key="question">
                                                                                <!-- <td>{{ ++index }}</td> -->
                                                                                <td>{{ `${question?.question}` }}</td>
                                                                                <td>
                                                                                    <template
                                                                                        v-if="question?.answer ? question?.answer == 'yes' : question?.response?.answer == 'yes'">
                                                                                        <v-btn color='success'
                                                                                            size='small' flat>
                                                                                            selected
                                                                                        </v-btn>
                                                                                    </template>
                                                                                </td>
                                                                                <td>
                                                                                    <template
                                                                                        v-if="question?.answer ? question?.answer == 'nc_minor' : question?.response?.answer == 'nc_minor'">
                                                                                        <v-btn color='success'
                                                                                            size='small' flat>
                                                                                            selected
                                                                                        </v-btn>
                                                                                    </template>

                                                                                </td>
                                                                                <td>
                                                                                    <template
                                                                                        v-if="question?.answer ? question?.answer == 'nc_major' : question?.response?.answer == 'nc_major'">
                                                                                        <v-btn color='success'
                                                                                            size='small' flat>
                                                                                            selected
                                                                                        </v-btn>
                                                                                    </template>

                                                                                </td>
                                                                                <td>
                                                                                    <template
                                                                                        v-if="question?.answer ? question?.answer == 'na' : question?.response?.answer == 'na'">
                                                                                        <v-btn color='success'
                                                                                            size='small' flat>
                                                                                            selected
                                                                                        </v-btn>
                                                                                    </template>
                                                                                </td>
                                                                                <td>
                                                                                    <v-btn color='primary' size='small'
                                                                                        @click="setSendCommentDialog(true, question?.id, title_question.id, question)"
                                                                                        flat
                                                                                        v-if="question?.response?.comment">
                                                                                        {{ "View Comment" }}
                                                                                    </v-btn>

                                                                                </td>
                                                                            </tr>
                                                                            <!-- </template> -->


                                                                        </tbody>
                                                                    </v-table>
                                                                </v-col>

                                                            </v-row>

                                                        </div>

                                                        <v-row class="mt-3">
                                                            <v-col cols="12" sm="6">
                                                                <v-btn color="primary" variant="tonal"
                                                                    @click="changeQuestionTab(`questionTab-${index-1}`)"
                                                                    v-if="index > 0">Previous Questions</v-btn>
                                                            </v-col>
                                                            <v-col cols="12" sm="6" class="text-sm-right">
                                                                <v-btn color="primary"
                                                                    @click="changeQuestionTab(`questionTab-${index+1}`)"
                                                                    v-if="index < (getQuestions.length - 1)">Next
                                                                    Questions</v-btn>
                                                            </v-col>
                                                        </v-row>
                                                    </v-window-item>
                                                </template>

                                            </v-window>

                                        </v-col>
                                    </v-row>

                                </div>
                                <v-row class="mt-3">
                                    <v-col cols="6">
                                        <v-btn color="primary" variant="tonal" @click="changeTab('tab-3')">Back</v-btn>
                                    </v-col>
                                    <v-col cols="6" class="text-right">
                                        <v-btn color="primary" @click="changeTab('tab-5')">Next Step</v-btn>
                                    </v-col>
                                </v-row>
                            </v-window-item>
                            <v-window-item value="tab-5" class="pa-1">
                                <div>
                                    <v-row class="mt-3 px-4">


                                        <v-col cols="12">
                                            <h3>Findings</h3>
                                            <v-table>
                                                <thead>
                                                    <tr>

                                                        <th class="text-left">
                                                            Inspector
                                                        </th>
                                                        <th class="text-left">
                                                            Description
                                                        </th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td>{{ getFindings ? `${getFindings?.user?.lastName}
                                                            ${getFindings?.user?.firstName}` : '' }}</td>
                                                        <td>{{ getFindings ? `${getFindings?.description}` : '' }}</td>
                                                    </tr>
                                                </tbody>
                                            </v-table>
                                        </v-col>
                                    </v-row>

                                    <v-row class="mt-3 px-4">

                                        <v-col cols="12">
                                            <h3>Corrective Action</h3>
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
                                                            Description
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
                                        </v-col>
                                    </v-row>

                                </div>
                                <v-row class="mt-3">
                                    <v-col cols="6">
                                        <v-btn color="primary" variant="tonal" @click="changeTab('tab-4')">Back</v-btn>
                                    </v-col>

                                </v-row>

                            </v-window-item>
                        </v-window>
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

