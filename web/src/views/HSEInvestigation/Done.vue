<script setup lang="ts">
import { ref, onMounted, computed, watch } from 'vue';
import { useRoute } from 'vue-router'
import { router } from '@/router';

import BaseBreadcrumb from '@/components/shared/BaseBreadcrumb.vue';
import UiParentCard from '@/components/shared/UiParentCard.vue';
import { useOrganizationStore } from '@/stores/organizationStore';
import { useTeamMemberStore } from '@/stores/teamMemberStore';
import { useInvestigationStore } from '@/stores/investigationStore';
import { useReportStore } from '@/stores/reportStore';
import { useAuthStore } from '@/stores/auth';
import { useOpenLinksStore } from '@/stores/openLinks';
import moment from 'moment'
import Swal from 'sweetalert2'



const route = useRoute()
const authStore = useAuthStore();
const organizationStore = useOrganizationStore();
const teamMemberStore = useTeamMemberStore();
const openLinks = useOpenLinksStore();
const investigationStore = useInvestigationStore();


onMounted(() => {
    investigationStore.getCompletedInvestigation(route.params.investigation_id);
    // teamMemberStore.getTeamMembers();
});

const getActiveOrg = computed(() => (organizationStore.getActiveOrg()));
const getAuthUser = computed(() => (authStore.loggedUser));
const getMembers = computed(() => (teamMemberStore.members));
const getInvestigationQuestions = computed(() => (investigationStore.questions));
const getInvestigation = computed(() => (investigationStore.investigation));
const isLoggedInUserIsLead= computed(() => (getAuthUser.value?.id == getInvestigation.value?.lead?.member?.id));


const page = ref({ title: 'Investigation' });
const breadcrumbs = ref([
    {
        text: 'Dashboard',
        disabled: false,
        href: 'dashboard'
    },
    {
        text: 'HSE Investigation',
        disabled: false,
        href: '#'
    },
    {
        text: 'Completed',
        disabled: true,
        href: '#'
    }
]);


const tab = ref('tab-1');

function changeTab(e: string) {
    tab.value = e;
}



</script>

<template>
    <div>
        <BaseBreadcrumb :title="page.title" :breadcrumbs="breadcrumbs"></BaseBreadcrumb>

        <v-row v-if="getInvestigation">
            <v-col cols="12" md="9">

                <UiParentCard variant="outlined">
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
                                <FileDescriptionIcon stroke-width="1.5" width="20" class="v-icon--start" />
                                <div>
                                    <div>Questionaire</div>
                                    <span
                                        class="text-subtitle-2 text-lightText text-medium-emphasis font-weight-medium d-block">
                                        Ask Members
                                    </span>
                                </div>
                            </v-tab>

                            <v-tab value="tab-3" rounded="md" class="mb-3 mx-2 text-left" height="70">
                                <CreditCardIcon stroke-width="1.5" width="20" class="v-icon--start" />
                                <div>
                                    <div>Send Invitations</div>
                                    <span
                                        class="text-subtitle-2 text-lightText text-medium-emphasis font-weight-medium d-block">
                                        For Interview
                                    </span>
                                </div>
                            </v-tab>
                            <v-tab value="tab-4" rounded="md" class="mb-3 mx-2 text-left" height="70">
                                <CreditCardIcon stroke-width="1.5" width="20" class="v-icon--start" />
                                <div>
                                    <div>Finding</div>
                                    <span
                                        class="text-subtitle-2 text-lightText text-medium-emphasis font-weight-medium d-block">
                                        With Conclusion
                                    </span>
                                </div>
                            </v-tab>
                            <v-tab value="tab-5" rounded="md" class="mb-3 mx-2 text-left" height="70">
                                <CreditCardIcon stroke-width="1.5" width="20" class="v-icon--start" />
                                <div>
                                    <div>Recommendation </div>
                                    <span
                                        class="text-subtitle-2 text-lightText text-medium-emphasis font-weight-medium d-block">
                                        Members Action
                                    </span>
                                </div>
                            </v-tab>
                            <v-tab value="tab-6" rounded="md" class="mb-3 mx-2 text-left" height="70">
                                <CreditCardIcon stroke-width="1.5" width="20" class="v-icon--start" />
                                <div>
                                    <div>Incident Report</div>
                                    <span
                                        class="text-subtitle-2 text-lightText text-medium-emphasis font-weight-medium d-block">
                                        Final Report
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
                                                    <tr v-for="(member, index) in getInvestigation?.all_members"
                                                        :key="member">
                                                        <td>{{ ++index }}</td>
                                                        <td>{{ `${member?.member?.lastName}
                                                            ${member?.member?.firstName}` }}</td>
                                                        <td>{{ `${member?.member?.email}` }}</td>
                                                        <td>{{ `${member?.position}` }}</td>
                                                    
                                                    </tr>
                                                </tbody>
                                            </v-table>
                                        </v-col>
                                    </v-row>

                                </div>

                                <v-row class="mt-3">
                                    <v-col cols="12" sm="6">
                                        <!-- <v-btn color="primary" variant="tonal">Continue</v-btn> -->
                                    </v-col>
                                    <v-col cols="12" sm="6" class="text-sm-right">
                                        <v-btn color="primary" @click="changeTab('tab-2')"
                                            v-if="isLoggedInUserIsLead">Next Step</v-btn>
                                    </v-col>
                                </v-row>
                            </v-window-item>
                            <v-window-item value="tab-2" class="pa-1">
                                <div>
                                    <v-row>
                                        <v-col>

                                            <v-table>
                                                <thead>
                                                    <tr>
                                                        <th class="text-left">
                                                            #
                                                        </th>
                                                        <th class="text-left">
                                                            To
                                                        </th>
                                                        <th class="text-left">
                                                            From
                                                        </th>
                                                        <th class="text-left">
                                                            Question
                                                        </th>
                                                        <th class="text-left">
                                                            Response
                                                        </th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr v-for="(question, index) in getInvestigation?.questions"
                                                        :key="question">
                                                        <td>{{ ++index }}</td>
                                                        <td>{{ `${question?.responder?.lastName}
                                                            ${question?.responder?.firstName}` }}</td>
                                                        <td>{{ `${question?.user?.lastName}
                                                            ${question?.user?.firstName}` }}</td>
                                                        <td>{{ question?.question?.question }}</td>
                                                        <td class=''>{{ question?.response }}</td>
                                                    </tr>

                                                </tbody>
                                            </v-table>
                                        </v-col>


                                    </v-row>
                                </div>
                                <v-row class="mt-3">
                                    <v-col cols="6">
                                        <v-btn color="primary" variant="tonal" @click="changeTab('tab-1')">Back</v-btn>
                                    </v-col>
                                    <v-col cols="6" class="text-right">
                                        <v-btn color="primary" @click="changeTab('tab-3')"
                                            v-if="isLoggedInUserIsLead">Next Step</v-btn>
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
                                                            Interviewer
                                                        </th>
                                                        <th class="text-left">
                                                            Interviewee
                                                        </th>
                                                        <th class="text-left">
                                                            Type
                                                        </th>
                                                        <th class="text-left">
                                                            Selected Date
                                                        </th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr v-for="(interview, index) in getInvestigation?.interviews"
                                                        :key="interview">
                                                        <td>{{ ++index }}</td>
                                                        <td>{{ `${interview?.user?.lastName}
                                                            ${interview?.user?.firstName}` }}</td>
                                                        <td>{{ `${interview?.invitee?.lastName}
                                                            ${interview?.invitee?.firstName}` }}</td>
                                                        <td>{{ `${interview?.type}` }}</td>
                                                        <td>{{ `${interview?.selected_date ? interview?.selected_date :
                                                            ''}` }}</td>
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

                                        <v-col cols="12" v-if="getInvestigation?.findings">
                                            <v-table>
                                                <thead>
                                                    <tr>

                                                        <th class="text-left">
                                                            Investigator
                                                        </th>
                                                        <th class="text-left">
                                                            Description
                                                        </th>
                                                        <th class="text-left">
                                                            Type
                                                        </th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr v-for="(interview) in getInvestigation?.findings"
                                                        :key="interview">
                                                        <td>{{ `${interview?.user?.lastName}
                                                            ${interview?.user?.firstName}` }}</td>
                                                        <td>{{ `${interview?.description}` }}</td>
                                                        <td>{{ `${interview?.type} ${interview?.type == 'conclusion' ?
                                                            '' : 'Cause'}` }}</td>
                                                    </tr>
                                                </tbody>
                                            </v-table>
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
                                                    <tr v-for="(actions, index) in getInvestigation?.actions"
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
                                        </v-col>
                                    </v-row>

                                </div>
                                <v-row class="mt-3">
                                    <v-col cols="6">
                                        <v-btn color="primary" variant="tonal" @click="changeTab('tab-4')">Back</v-btn>
                                    </v-col>
                                    <v-col cols="6" class="text-right">
                                        <v-btn color="primary" @click="changeTab('tab-6')">Next Step</v-btn>
                                    </v-col>
                                </v-row>
                            </v-window-item>
                            <v-window-item value="tab-6" class="pa-1">


                                <v-col cols="12">
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
                                                    Location
                                                </th>
                                                <th class="text-left">
                                                    Damages
                                                </th>
                                                <th class="text-left">
                                                    Incident Date
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr :key="getInvestigation?.report" v-if="getInvestigation?.report">
                                                <td>{{ '1' }}</td>
                                                <td>{{ `${getInvestigation?.report?.title}` }}</td>
                                                <td>{{ `${getInvestigation?.report?.description}` }}</td>
                                                <td>{{ `${getInvestigation?.report?.location}` }}</td>
                                                <td>{{ `${getInvestigation?.report?.damages}` }}</td>
                                                <td>{{ `${getInvestigation?.report?.incident_date_time}` }}</td>
                                            </tr>
                                        </tbody>
                                    </v-table>
                                </v-col>
                                <v-row class="mt-3">
                                    <v-col cols="12" sm="6">
                                        <v-btn color="primary" variant="tonal" @click="changeTab('tab-5')">Back</v-btn>
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

