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
import BuildTeam from '@/views/HSEInvestigation/components/ongoingInvestigation/BuildTeam.vue'
import moment from 'moment'
import Swal from 'sweetalert2'



const route = useRoute()
const authStore = useAuthStore();
const organizationStore = useOrganizationStore();
const reportStore = useReportStore();
const teamMemberStore = useTeamMemberStore();
const openLinks = useOpenLinksStore();
const investigationStore = useInvestigationStore();


onMounted(() => {
    investigationStore.getInvestigation(route.params.observation_id as string);
    teamMemberStore.getTeamMembers();
    openLinks.getPriorities();
    investigationStore.getInvestigationQuestions(route.params.observation_id as string);
});

const computedIndex = (index: any) => ++index;

const getActiveOrg: any = computed(() => (organizationStore.getActiveOrg()));
const getAuthUser: any = computed(() => (authStore.loggedUser));
const getMembers: any = computed(() => (teamMemberStore.members));
const getInvestigationQuestions: any = computed(() => (investigationStore.questions));
const getInvestigation: any = computed(() => (investigationStore.investigation));
const getPriorities: any = computed(() => (openLinks.priorities));
const isLoggedInUserOwnsActionOrg: any = computed(() => (getAuthUser.value?.id == getActiveOrg.value?.user_id));
const isLoggedInUserOwnInvestigationOrganization: any = computed(() => (getAuthUser.value?.id == getActiveOrg.value?.user_id && getAuthUser.value?.id == getInvestigation.value.organization?.user_id));
const isLoggedInUserIsLead: any = computed(() => (getAuthUser.value?.id == getInvestigation.value?.lead?.member?.id));
const isLoggedInUserIsMember: any = computed(() => (getInvestigation.value?.all_members?.map((item: any) => item?.member_id).includes(getAuthUser.value?.id)));
// 
// console.log(isLoggedInUserIsMember.value)

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
        text: 'Ongoing',
        disabled: true,
        href: '#'
    }
]);






const valid = ref(true);;
const formContainer = ref('')
const loading = ref(false);
const setLoading = (value: boolean) => {
    loading.value = value;
}



const viewReportDialog = ref(false);
const setViewReportDialog = (value: boolean) => {
    viewReportDialog.value = value;
}
const addInviteDialog = ref(false);
const setAddInviteDialog = (value: boolean) => {
    addInviteDialog.value = value;
}
const addMemberDialog = ref(false);
const setAddMemberDialog = (value: boolean) => {
    addMemberDialog.value = value;
}
const addExternalMemberDialog = ref(false);
const setAddExternalMemberDialog = (value: boolean) => {
    addExternalMemberDialog.value = value;
}
const viewQuestionTeamDialog = ref(false);
const setSelectQuestionaireTeam = (value: boolean) => {
    viewQuestionTeamDialog.value = value;
}

const viewExternalWitnessDialog = ref(false);
const setExternalWitnessDialog = (value: boolean) => {
    viewExternalWitnessDialog.value = value;
}

const viewQuestionDialog = ref(false);
const setViewQuestionDialog = (value: boolean) => {
    viewQuestionDialog.value = value;
    if (value == false) selectItem({})
}
const viewFindingDialog = ref(false);
const setViewFindingDialog = (value: boolean, type: string = '') => {
    viewFindingDialog.value = value;
    stepFourFields.value.type = type;
    if (value == false) selectItem({})
}
const viewRecommendationDialog = ref(false);
const setViewRecommendationDialog = (value: boolean, type: string = '') => {
    viewRecommendationDialog.value = value;
    if (value == false) selectItem({})
}
const selectedItem = ref({} as any);
const selectItem = (item: any, action: string = '', extra: any = '') => {
    selectedItem.value = Object.assign({}, item.raw);

    switch (action) {
        case 'viewQuestion':
            setViewQuestionDialog(true)
            break;
        case 'viewFinding':
            setViewFindingDialog(true, extra)
            break;
        case 'viewRecommendation':
            setViewFindingDialog(true)
            break;
        // case 'edit':

        //     setEditDialog(true)
        //     break;
        // case 'delete':
        //     setDeleteDialog(true)
        //     break;
        default:
            break;
    }

}
type OrganizationType = {
    uuid: string
    name: string,
    token: string,
};

type UserType = {
    id: string
    uuid: string
    full_name: string,
};
const fields = ref({
    leadInvestigator: getInvestigation?.lead ?? '',
    teamMember: [],
    externalTeamMembers: [],
    groupChatName: '',
    orgToken: '',
    organization: null as null || {} as OrganizationType,
    questions: [],
    externalOrgMembers: null as null || {} as UserType[],
    organization_id: getActiveOrg.value?.uuid,
});

const members_ids: any = computed(() => (getInvestigation.value?.all_members.map((member: any) => (member.member_id))));
const filteredNonMember: any = computed(() => (getMembers.value?.filter((member: any) => (!members_ids.value?.includes(member.id)))));

const stepTwoFields = ref({
    question: "",
    questions: [],
    externalTeamMembers: [],
    members: [],
    orgToken: '',
    organization: null as null || {} as OrganizationType,
    externalOrgMembers: null as null || {} as UserType[],
});

const stepTwoFieldRules: any = ref({
    question: [
        (v: string) => !!v || 'Field is Required',
    ],
    questions: [
        (v: string) => !!v || 'Field is Required',
    ],
    members: [
        (v: string) => !!v || 'Field is Required',
    ],
})
const sendQuestion = async (e: any) => {
    e.preventDefault();

    try {
        setLoading(true)

        const values = { ...stepTwoFields.value }

        let teams: any[] = values?.members;

        if (viewExternalWitnessDialog.value) {

            teams = [...teams, ...values?.externalTeamMembers]

        }

        let objectValues = {
            "organization_id": getActiveOrg.value?.uuid,
            "investigation_id": getInvestigation.value?.uuid,
            "questions": values?.questions,
            "members": teams,
        }

        const resp = await investigationStore.sendInvestigationQuestions(objectValues)
            .catch((error: any) => {
                console.log(error)
                throw error
            })
            .then((resp: any) => {
                return resp
            });

        if (resp?.message == 'success') {
            setLoading(false)

            investigationStore.getInvestigation(route.params.observation_id as string);
        }

        setLoading(false)

        stepTwoFields.value.questions = [];
        stepTwoFields.value.members = [];
        stepTwoFields.value.externalTeamMembers = [];
        stepTwoFields.value.orgToken = ''

    } catch (error) {
        console.log(error)
        setLoading(false)
    }

}

const fetchWitnessOrganization = async (token: string) => {
    try {

        const resp = await organizationStore.getTokenOrganizations(stepTwoFields.value.orgToken)
            .catch((error: any) => {
                console.log(error)
                throw error
            })
            .then((resp: any) => {
                return resp
            });

        if (resp) {

            stepTwoFields.value.organization = resp
            fetchWitnessOrgUser(resp.uuid)
        } else {

            stepTwoFields.value.organization = {
                uuid: '',
                name: '',
                token: '',
            }
        }


    } catch (error) {
        console.log(error)
    }
}


const fetchWitnessOrgUser = async (token: string) => {

    try {

        const resp = await organizationStore.getOrganizationUsers(token)
            .catch((error: any) => {
                console.log(error)
                throw error
            })
            .then((resp: any) => {
                return resp
            });

        if (resp) {

            stepTwoFields.value.externalOrgMembers = resp

        } else {

            stepTwoFields.value.externalOrgMembers = []
        }


    } catch (error) {

    }
}
const stepThreeFields = ref({
    interview_type: "online",
    interview_link: "",
    interview_location: "",
    date_times: [],
    members: [],
});

const stepThreeFieldRules: any = ref({
    interview_type: [
        (v: string) => !!v || 'Field is Required',
    ],
    interview_link: [
        (v: string) => !!v || 'Field is Required',
    ],
    interview_location: [
        (v: string) => !!v || 'Field is Required',
    ],
    date_times: [
        (v: string) => !!v || 'Field is Required',
    ],
    members: [
        (v: string) => !!v || 'Field is Required',
    ],
})
const sendInvites = async (e: any) => {
    e.preventDefault();

    try {
        setLoading(true)

        const values = { ...stepThreeFields.value }

        const date_times = values?.date_times.map((item: any) => moment(item).format('YYYY-MM-DD hh:mm:ss'))

        let objectValues = {
            "organization_id": getActiveOrg.value?.uuid,
            "investigation_id": getInvestigation.value?.uuid,
            "date_times": date_times,
            "members": values?.members,
            "interview_type": values?.interview_type,
            "interview_link": values?.interview_link,
            "interview_location": values?.interview_location,
        }

        const resp = await investigationStore.sendInvestigationInvites(objectValues)
            .catch((error: any) => {
                console.log(error)
                throw error
            })
            .then((resp: any) => {
                return resp
            });

        if (resp?.message == 'success') {
            setLoading(false)
            setAddInviteDialog(false)
            investigationStore.getInvestigation(route.params.observation_id as string);
        }

        setLoading(false)
        setAddInviteDialog(false)

        stepThreeFields.value.members = [];
        stepThreeFields.value.date_times = [];
        stepThreeFields.value.interview_type = "";
        stepThreeFields.value.interview_link = "";
        stepThreeFields.value.interview_location = "";


    } catch (error) {
        console.log(error)
        setLoading(false)
        setAddInviteDialog(false)
    }

}

const stepFourFields = ref({
    type: "root",
    detail: "",
});

const stepFourFieldRules: any = ref({
    type: [
        (v: string) => !!v || 'Field is Required',
    ],
    detail: [
        (v: string) => !!v || 'Field is Required',
    ],
})
const sendFindings = async (e: any) => {
    e.preventDefault();

    try {
        setLoading(true)

        const values = { ...stepFourFields.value }
        const formData = new FormData();

        formData.append('organization_id', getActiveOrg.value?.uuid);
        formData.append('investigation_id', getInvestigation.value?.uuid);
        formData.append('type', values?.type);
        formData.append('detail', values?.detail);

        files.value.forEach(file => {
            formData.append('files[]', file);
        });

        const resp = await investigationStore.sendInvestigationFindings(formData)
            .catch((error: any) => {
                console.log(error)
                throw error
            })
            .then((resp: any) => {
                return resp
            });

        if (resp?.message == 'success') {
            setLoading(false)
            setViewFindingDialog(false)

            investigationStore.getInvestigation(route.params.observation_id as string);
        }

        setLoading(false)
        setViewFindingDialog(false)
        stepFourFields.value.detail = ''
        files.value = []


    } catch (error) {
        console.log(error)
        setLoading(false)
        setViewFindingDialog(false)
    }

}
const stepFiveFields = ref({
    title: "",
    description: "",
    assigneeId: "",
    priorityId: "",
    end_date: "",
    start_date: "",
    organization_id: getActiveOrg.value?.uuid,

});

const stepFiveFieldRules: any = ref({
    priorityId: [
        (v: string) => !!v || 'Priority is required',
    ],
    assigneeId: [
        (v: string) => !!v || 'User is required',
    ],
    title: [
        (v: string) => !!v || 'Title  is required',
    ],
    description: [
        (v: string) => !!v || 'Description is required',
        (v: string) => v.length > 10 || 'More than ten letters required'
    ],
    end_date: [
        (v: string) => !!v || 'End Date is required',
        // (v: string) => v.length > 10 || 'More than ten letters required'
    ],
    start_date: [
        (v: string) => !!v || 'Start Date is required',
        // (v: string) => v.length > 10 || 'More than ten letters required'
    ],
})

const sendRecommendation = async (e: any) => {
    e.preventDefault();

    try {
        setLoading(true)

        const values = { ...stepFiveFields.value }

        let objectValues = {
            "organization_id": getActiveOrg.value?.uuid,
            "investigation_id": getInvestigation.value?.uuid,
            "title": values?.title,
            "description": values?.description,
            "assignee_id": values?.assigneeId,
            "start_date": moment(values?.start_date).format('YYYY-MM-DD HH:mm:ss'),
            "end_date": moment(values?.end_date).format('YYYY-MM-DD HH:mm:ss'),
            "priority_id": values?.priorityId
        }


        const resp = await investigationStore.addRecommendation(objectValues)
            .catch((error: any) => {
                console.log(error)
                throw error
            })
            .then((resp: any) => {
                return resp
            });


        if (resp?.message == 'success') {
            setLoading(false)
            setViewRecommendationDialog(false)

            stepFiveFields.value.title = "";
            stepFiveFields.value.description = "";
            stepFiveFields.value.assigneeId = "";
            stepFiveFields.value.start_date = "";
            stepFiveFields.value.end_date = "";
            stepFiveFields.value.priorityId = "";

            investigationStore.getInvestigation(route.params.observation_id as string);
        }

        setLoading(false)
        setViewRecommendationDialog(false)



    } catch (error) {
        console.log(error)
        setLoading(false)
        setViewRecommendationDialog(false)
    }

}

const stepSixFields = ref({
    title: "",
    method: "",
    description: "",
    damages: "",
    location: "",
    incident_date_time: "",
    organization_id: getActiveOrg.value?.uuid,

});

const stepSixFieldRules: any = ref({
    location: [
        (v: string) => !!v || 'Priority is required',
    ],
    damages: [
        (v: string) => !!v || 'Damages is required',
    ],
    method: [
        (v: string) => !!v || 'Method  is required',
    ],
    title: [
        (v: string) => !!v || 'Title  is required',
    ],
    description: [
        (v: string) => !!v || 'Description is required',
        (v: string) => v.length > 10 || 'More than ten letters required'
    ],
    incident_date_time: [
        (v: string) => !!v || 'End Date is required',
        // (v: string) => v.length > 10 || 'More than ten letters required'
    ],
})

const sendReport = async (e: any) => {
    e.preventDefault();

    try {
        setLoading(true)

        const values = { ...stepSixFields.value }

        let objectValues = {
            "organization_id": getActiveOrg.value?.uuid,
            "investigation_id": getInvestigation.value?.uuid,
            "title": values?.title,
            "description": values?.description,
            "method": values?.method,
            "incident_date_time": moment(values?.incident_date_time).format('YYYY-MM-DD HH:mm:ss'),
            "location": values?.location,
            "damages": values?.damages
        }

        const resp = await investigationStore.addReport(objectValues)
            .catch((error: any) => {
                console.log(error)
                throw error
            })
            .then((resp: any) => {
                return resp
            });


        if (resp?.message == 'success') {
            setLoading(false)
            setViewReportDialog(false)

            stepSixFields.value.title = "";
            stepSixFields.value.method = "";
            stepSixFields.value.description = "";
            stepSixFields.value.damages = "";
            stepSixFields.value.location = "";
            stepSixFields.value.incident_date_time = "";
            investigationStore.getInvestigation(route.params.observation_id as string);
        }

        setLoading(false)
        setViewReportDialog(false)



    } catch (error) {
        console.log(error)
        setLoading(false)
        setViewReportDialog(false)
    }

}

const addQuestion = async (e: any) => {
    e.preventDefault();

    try {
        setLoading(true)

        const values = { ...stepTwoFields.value }

        let objectValues = {
            "investigation_id": getInvestigation.value?.uuid,
            "question": values?.question
        }

        const resp = await investigationStore.setInvestigationQuestion(objectValues)
            .catch((error: any) => {
                console.log(error)
                throw error
            })
            .then((resp: any) => {
                return resp
            });

        if (resp?.message == 'success') {
            setLoading(false)
            stepTwoFields.value.question = "";
            investigationStore.getInvestigationQuestions(route.params.observation_id as string);
        }

        setLoading(false)
        setViewQuestionDialog(false)


    } catch (error) {
        console.log(error)
        setLoading(false)
        setViewQuestionDialog(false)
    }

}

const completeInvestigation = async (member: any) => {

    try {
        setLoading(true)

        let objectValues = {
            "organization_id": getActiveOrg.value?.uuid,
            "investigation_id": getInvestigation.value?.uuid,
        }

        Swal.fire({
            title: 'Info!',
            text: 'Do you want to complete investigation?',
            icon: 'info',
            confirmButtonText: 'Yes',
            cancelButtonText: 'No',
            showCancelButton: true,
            allowOutsideClick: false,
        }).then((result) => {
            if (result.isConfirmed) {


                investigationStore.completeInvestigation(objectValues)
                    .catch((error: any) => {
                        throw error
                    })
                    .then((resp: any) => {
                        //  investigationStore.getInvestigation(route.params.observation_id as string);
                        console.log(resp)
                        router.push(`/hse-investigation`)
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

// ---------------------------------------------------

const thankyou = ref(false);

const tab = ref('tab-1');
const checkbox = ref([]);

function changeTab(e: string) {
    tab.value = e;
}
const blankFn = () => {
    console.log('black')
}

const files = ref([])
const images = ref([])
const previewImage = ref([] as any)

const selectImage = (image: any) => {

    images.value = image.target.files;
    previewImage.value = [];

    for (let index = 0; index < images.value.length; index++) {
        const element = images.value[index];
        previewImage.value.push(URL.createObjectURL(element) as string)
    }

}
const date = ref(null);

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

                            <v-tab value="tab-2" rounded="md" class="mb-3 mx-2 text-left" height="70"
                                :disabled="!isLoggedInUserIsLead">
                                <FileDescriptionIcon stroke-width="1.5" width="20" class="v-icon--start" />
                                <div>
                                    <div>Questionaire</div>
                                    <span
                                        class="text-subtitle-2 text-lightText text-medium-emphasis font-weight-medium d-block">
                                        Ask Members
                                    </span>
                                </div>
                            </v-tab>

                            <v-tab value="tab-3" rounded="md" class="mb-3 mx-2 text-left" height="70"
                                :disabled="!isLoggedInUserIsLead">
                                <CreditCardIcon stroke-width="1.5" width="20" class="v-icon--start" />
                                <div>
                                    <div>Send Invitations</div>
                                    <span
                                        class="text-subtitle-2 text-lightText text-medium-emphasis font-weight-medium d-block">
                                        For Interview
                                    </span>
                                </div>
                            </v-tab>
                            <v-tab value="tab-4" rounded="md" class="mb-3 mx-2 text-left" height="70"
                                :disabled="!isLoggedInUserIsLead">
                                <CreditCardIcon stroke-width="1.5" width="20" class="v-icon--start" />
                                <div>
                                    <div>Finding</div>
                                    <span
                                        class="text-subtitle-2 text-lightText text-medium-emphasis font-weight-medium d-block">
                                        With Conclusion
                                    </span>
                                </div>
                            </v-tab>
                            <v-tab value="tab-5" rounded="md" class="mb-3 mx-2 text-left" height="70"
                                :disabled="!isLoggedInUserIsLead">
                                <CreditCardIcon stroke-width="1.5" width="20" class="v-icon--start" />
                                <div>
                                    <div>Recommendation </div>
                                    <span
                                        class="text-subtitle-2 text-lightText text-medium-emphasis font-weight-medium d-block">
                                        Members Action
                                    </span>
                                </div>
                            </v-tab>
                            <v-tab value="tab-6" rounded="md" class="mb-3 mx-2 text-left" height="70"
                                :disabled="!isLoggedInUserIsLead">
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
                                    <BuildTeam />
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
                                    <v-row v-if="getInvestigation?.questions?.length === 0">
                                        <v-col v-if="!viewQuestionTeamDialog">

                                            <v-table>
                                                <thead>
                                                    <tr>
                                                        <th class="text-left">
                                                            #
                                                        </th>
                                                        <th class="text-left">
                                                            Questions
                                                        </th>
                                                        <th class="text-left">
                                                            Type
                                                        </th>
                                                        <th class="text-left">
                                                            Check to Send
                                                        </th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr v-for="(question, index) in getInvestigationQuestions"
                                                        :key="question">
                                                        <td>{{ computedIndex(index) }}</td>
                                                        <td>{{ question?.question }}</td>
                                                        <td>{{ question?.type }}</td>
                                                        <td class=''>
                                                            <v-checkbox v-model="stepTwoFields.questions"
                                                                :value="question?.id" class="m-0"></v-checkbox>
                                                        </td>
                                                    </tr>

                                                </tbody>
                                            </v-table>
                                        </v-col>


                                        <v-col cols="12" v-else>
                                            <v-btn color="primary" @click="setExternalWitnessDialog(true)"
                                                class="m-2">Add
                                                External Witness</v-btn>
                                            <VForm v-model="valid" ref="formContainer" fast-fail lazy-validation
                                                @submit.prevent="sendQuestion" class="py-1">
                                                <VRow class="mt-5 mb-3">

                                                    <VCol cols="12" md="12">
                                                        <v-label class="font-weight-medium pb-1">Select
                                                            Witness</v-label>
                                                        <VSelect v-model="stepTwoFields.members"
                                                            :items="filteredNonMember"
                                                            :rules="stepTwoFieldRules.members" label="Select"
                                                            item-title="lastName" item-value="id" single-line
                                                            variant="outlined" class="text-capitalize" chips
                                                            :item-props="(item: any) => ({ title: `${item?.lastName} ${item?.firstName}`, subtitle: `${item?.email}` })"
                                                            multiple>
                                                        </VSelect>
                                                    </VCol>
                                                    <template v-if="viewExternalWitnessDialog">

                                                        <VCol cols="12" md="12">
                                                            <v-label
                                                                class="text-subtitle-1 font-weight-medium pb-1">Organization
                                                                Token</v-label>
                                                            <VTextField type="text" v-model="stepTwoFields.orgToken"
                                                                :rules="stepTwoFieldRules.orgToken" required
                                                                variant="outlined" label=""
                                                                @change="fetchWitnessOrganization"
                                                                :color="stepTwoFields.orgToken.length > 2 ? 'success' : 'primary'">
                                                                <template v-slot:append>
                                                                    <v-btn color="primary"
                                                                        @click="fetchWitnessOrganization"
                                                                        :disabled="stepTwoFields.orgToken?.length < 12">Search</v-btn>
                                                                </template>
                                                            </VTextField>
                                                        </VCol>

                                                        <VCol cols="12" md="12"
                                                            v-if="stepTwoFields.orgToken?.length > 12">

                                                            <div v-if="stepTwoFields.organization">
                                                                <v-card elevation="10" rounded="md">
                                                                    <v-card-item>
                                                                        <div class="d-flex align-center">
                                                                            <div class="pl-4 mt-n1">
                                                                                <h5 class="text-h6">{{
            stepTwoFields.organization?.name
        }}</h5>
                                                                                <h5 class="text-h6">{{
                stepTwoFields.organization?.token
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


                                                        <VCol cols="12" md="12"
                                                            v-if="stepTwoFields.externalOrgMembers?.length > 0">
                                                            <v-label class="font-weight-medium pb-1">Select
                                                                Team Member</v-label>
                                                            <VSelect v-model="stepTwoFields.externalTeamMembers"
                                                                :items="stepTwoFields.externalOrgMembers"
                                                                :rules="stepTwoFieldRules.externalTeamMember"
                                                                label="Select" item-title="full_name" item-value="id"
                                                                single-line variant="outlined" class="text-capitalize"
                                                                chips multiple>

                                                            </VSelect>
                                                        </VCol>
                                                    </template>


                                                    <VCol cols="12" lg="12" class="text-right">

                                                        <v-btn color="primary" type="submit" :loading="loading"
                                                            :disabled="!valid" @click="sendQuestion">
                                                            <span v-if="!loading">
                                                                Save
                                                            </span>
                                                            <clip-loader v-else :loading="loading" color="white"
                                                                :size="'25px'"></clip-loader>
                                                        </v-btn>

                                                    </VCol>
                                                </VRow>
                                            </VForm>
                                        </v-col>

                                        <v-col cols="12">
                                            <v-btn color="primary" @click="selectItem({}, 'viewQuestion')"
                                                class="mr-2">Add Question</v-btn>
                                            <v-btn color="primary" @click="setSelectQuestionaireTeam(true)" class="mr-2"
                                                v-if="stepTwoFields.questions.length > 0 && !viewQuestionTeamDialog">Select
                                                Team Members</v-btn>
                                            <v-btn color="primary" @click="setSelectQuestionaireTeam(false)"
                                                class="mr-2"
                                                v-if="stepTwoFields.questions.length > 0 && viewQuestionTeamDialog">Back
                                                to
                                                Questions</v-btn>
                                            <v-sheet>
                                                <v-dialog v-model="viewQuestionDialog" max-width="500">
                                                    <v-card>

                                                        <v-card-text>
                                                            <div class="d-flex justify-space-between">
                                                                <h3 class="text-h3">Add </h3>
                                                                <v-btn icon @click="setViewQuestionDialog(false)"
                                                                    size="small" flat>
                                                                    <XIcon size="16" />
                                                                </v-btn>
                                                            </div>
                                                        </v-card-text>
                                                        <v-divider></v-divider>

                                                        <v-card-text>

                                                            <VForm v-model="valid" ref="formContainer" fast-fail
                                                                lazy-validation @submit.prevent="addQuestion"
                                                                class="py-1">
                                                                <VRow class="mt-5 mb-3">

                                                                    <VCol cols="12" md="12">
                                                                        <v-label
                                                                            class="text-subtitle-1 font-weight-medium pb-1">Question</v-label>
                                                                        <VTextarea variant="outlined" outlined
                                                                            name="Description" label="Question"
                                                                            v-model="stepTwoFields.question"
                                                                            :rules="stepTwoFieldRules.question" required
                                                                            :color="stepTwoFields.question.length > 10 ? 'success' : 'primary'">
                                                                        </VTextarea>
                                                                    </VCol>
                                                                    <VCol cols="12" lg="12" class="text-right">
                                                                        <v-btn color="error"
                                                                            @click="setViewQuestionDialog(false)"
                                                                            variant="text">Cancel</v-btn>

                                                                        <v-btn color="primary" type="submit"
                                                                            :loading="loading" :disabled="!valid"
                                                                            @click="addQuestion">
                                                                            <span v-if="!loading">
                                                                                Submit
                                                                            </span>
                                                                            <clip-loader v-else :loading="loading"
                                                                                color="white"
                                                                                :size="'25px'"></clip-loader>
                                                                        </v-btn>

                                                                    </VCol>
                                                                </VRow>
                                                            </VForm>
                                                        </v-card-text>
                                                    </v-card>
                                                </v-dialog>

                                            </v-sheet>
                                        </v-col>
                                    </v-row>
                                    <v-row v-else>
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
                                                        <td>{{ computedIndex(index) }}</td>
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

                                            <v-btn color="primary" @click="setAddInviteDialog(true)" class="mr-2">Create
                                                Interview</v-btn>
                                            <v-sheet>
                                                <v-dialog v-model="addInviteDialog" max-width="800">
                                                    <v-card>

                                                        <v-card-text>
                                                            <div class="d-flex justify-space-between">
                                                                <h3 class="text-h3">Add Invite </h3>
                                                                <v-btn icon @click="setAddInviteDialog(false)"
                                                                    size="small" flat>
                                                                    <XIcon size="16" />
                                                                </v-btn>
                                                            </div>
                                                        </v-card-text>
                                                        <v-divider></v-divider>

                                                        <v-card-text>
                                                            <VForm v-model="valid" ref="formContainer" fast-fail
                                                                lazy-validation @submit.prevent="sendInvites"
                                                                class="py-1">
                                                                <VRow class="mt-5 mb-3">

                                                                    <VCol cols="12" md="12">
                                                                        <v-label class="font-weight-medium pb-1">Select
                                                                            Members</v-label>
                                                                        <VSelect v-model="stepThreeFields.members"
                                                                            :items="filteredNonMember"
                                                                            :rules="stepThreeFieldRules.members"
                                                                            label="Select" item-title="lastName"
                                                                            item-value="id" single-line
                                                                            variant="outlined" class="text-capitalize"
                                                                            chips
                                                                            :item-props="(item: any) => ({ title: `${item?.lastName} ${item?.firstName}`, subtitle: `${item?.email}` })"
                                                                            multiple>

                                                                        </VSelect>
                                                                    </VCol>
                                                                    <VCol cols="12" md="12">
                                                                        <v-label class="font-weight-medium pb-1">Select
                                                                            Interview Type</v-label>
                                                                        <VSelect
                                                                            v-model="stepThreeFields.interview_type"
                                                                            :items="['online', 'physical']"
                                                                            :rules="stepThreeFieldRules.interview_type"
                                                                            label="Select" single-line
                                                                            variant="outlined" class="text-capitalize">

                                                                        </VSelect>
                                                                    </VCol>
                                                                    <VCol cols="12" md="12">
                                                                        <v-label class="font-weight-medium pb-1">Select
                                                                            Dates</v-label>
                                                                        <VueDatePicker
                                                                            input-class-name="dp-custom-input"
                                                                            v-model="stepThreeFields.date_times"
                                                                            :min-date="new Date()" multi-dates required>
                                                                        </VueDatePicker>
                                                                        <flat-pickr v-model="date" :config="{
            enableTime: true,
            dateFormat: 'd-m-Y H:i'
        }" id="datetime-input" class="form-control" placeholder="Date Time" />
                                                                    </VCol>

                                                                    <VCol cols="12" md="12"
                                                                        v-if="stepThreeFields.interview_type == 'online'">
                                                                        <v-label
                                                                            class="text-subtitle-1 font-weight-medium pb-1">Interview
                                                                            Link</v-label>
                                                                        <VTextField type="text"
                                                                            v-model="stepThreeFields.interview_link"
                                                                            :rules="stepThreeFieldRules.interview_link"
                                                                            required variant="outlined"
                                                                            label="Interview Link"
                                                                            :color="stepThreeFields.interview_link.length > 2 ? 'success' : 'primary'">
                                                                        </VTextField>
                                                                    </VCol>

                                                                    <VCol cols="12" md="12"
                                                                        v-if="stepThreeFields.interview_type == 'physical'">
                                                                        <v-label
                                                                            class="text-subtitle-1 font-weight-medium pb-1">Interview
                                                                            Location</v-label>
                                                                        <VTextField type="text"
                                                                            v-model="stepThreeFields.interview_location"
                                                                            :rules="stepThreeFieldRules.interview_location"
                                                                            required variant="outlined"
                                                                            label="Interview Location"
                                                                            :color="stepThreeFields.interview_location.length > 2 ? 'success' : 'primary'">
                                                                        </VTextField>
                                                                    </VCol>


                                                                    <VCol cols="12" lg="12" class="text-right">

                                                                        <v-btn color="error"
                                                                            @click="setAddInviteDialog(false)"
                                                                            variant="text">Cancel</v-btn>

                                                                        <v-btn color="primary" type="submit"
                                                                            :loading="loading" :disabled="!valid"
                                                                            @click="sendInvites">
                                                                            <span v-if="!loading">
                                                                                Send Invites
                                                                            </span>
                                                                            <clip-loader v-else :loading="loading"
                                                                                color="white"
                                                                                :size="'25px'"></clip-loader>
                                                                        </v-btn>

                                                                    </VCol>
                                                                </VRow>
                                                            </VForm>

                                                        </v-card-text>
                                                    </v-card>
                                                </v-dialog>
                                            </v-sheet>
                                        </v-col>

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
                                                        <td>{{ computedIndex(index) }}</td>
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
                                        <v-btn color="primary" @click="changeTab('tab-4')"
                                            v-if="isLoggedInUserIsLead">Next Step</v-btn>
                                    </v-col>
                                </v-row>
                            </v-window-item>
                            <v-window-item value="tab-4" class="pa-1">

                                <div>
                                    <v-row class="mt-3 px-4">


                                        <v-col cols="12">
                                            <template v-if="isLoggedInUserIsMember">
                                                <v-btn color="primary"
                                                    @click="selectItem({}, 'viewFinding', 'evidence')" class="mr-2">Add
                                                    Evidence</v-btn>
                                                <v-btn color="primary" @click="selectItem({}, 'viewFinding', 'root')"
                                                    class="mr-2">Add Root
                                                    Cause</v-btn>
                                                <v-btn color="primary"
                                                    @click="selectItem({}, 'viewFinding', 'immediate')" class="mr-2">Add
                                                    Immediate
                                                    Cause</v-btn>
                                                <v-btn color="primary"
                                                    @click="selectItem({}, 'viewFinding', 'conclusion')"
                                                    class="mr-2">Add
                                                    Conclusion</v-btn>
                                            </template>


                                            <v-sheet>
                                                <v-dialog v-model="viewFindingDialog" max-width="700">
                                                    <v-card>

                                                        <v-card-text>
                                                            <div class="d-flex justify-space-between">
                                                                <h3 class="text-h3">Add </h3>
                                                                <v-btn icon @click="setViewFindingDialog(false)"
                                                                    size="small" flat>
                                                                    <XIcon size="16" />
                                                                </v-btn>
                                                            </div>
                                                        </v-card-text>
                                                        <v-divider></v-divider>

                                                        <v-card-text>

                                                            <VForm v-model="valid" ref="formContainer" fast-fail
                                                                lazy-validation @submit.prevent="sendFindings"
                                                                class="py-1">
                                                                <VRow class="mt-5 mb-3">

                                                                    <VCol cols="12" md="12">
                                                                        <v-label
                                                                            class="text-subtitle-1 font-weight-medium pb-1">Details</v-label>
                                                                        <VTextarea variant="outlined" outlined
                                                                            name="Description" label="Details"
                                                                            v-model="stepFourFields.detail"
                                                                            :rules="stepFourFieldRules.detail" required
                                                                            :color="stepFourFields.detail.length > 10 ? 'success' : 'primary'">
                                                                        </VTextarea>
                                                                    </VCol>
                                                                    <VCol cols="12" md="12"
                                                                        v-if="stepFourFields.type == 'evidence'">
                                                                        <v-label
                                                                            class="font-weight-medium pb-1">Image</v-label>

                                                                        <v-file-input v-model="files" :show-size="1000"
                                                                            color="deep-purple-accent-4"
                                                                            label="File input"
                                                                            placeholder="Select your files"
                                                                            prepend-icon="mdi-paperclip"
                                                                            variant="outlined" counter multiple
                                                                            accept="image/*" @change="selectImage">
                                                                            <template v-slot:selection="{ fileNames }">
                                                                                <template
                                                                                    v-for="(fileName, index) in fileNames"
                                                                                    :key="fileName">
                                                                                    <v-chip v-if="index < 2"
                                                                                        class="me-2"
                                                                                        color="deep-purple-accent-4"
                                                                                        size="small" label>
                                                                                        {{ fileName }}
                                                                                    </v-chip>

                                                                                    <span v-else-if="index === 2"
                                                                                        class="text-overline text-grey-darken-3 mx-2">
                                                                                        +{{ files.length - 2 }} File(s)
                                                                                    </span>
                                                                                </template>
                                                                            </template>
                                                                        </v-file-input>

                                                                        <div v-if="previewImage">
                                                                            <VRow>
                                                                                <VCol cols="4"
                                                                                    v-for="image in previewImage"
                                                                                    :key="image">

                                                                                    <div>
                                                                                        <img class="preview my-3"
                                                                                            :src="image" alt=""
                                                                                            style="max-width: 200px;" />
                                                                                    </div>
                                                                                </VCol>
                                                                            </VRow>
                                                                        </div>
                                                                    </VCol>
                                                                    <VCol cols="12" lg="12" class="text-right">
                                                                        <v-btn color="error"
                                                                            @click="setViewQuestionDialog(false)"
                                                                            variant="text">Cancel</v-btn>

                                                                        <v-btn color="primary" type="submit"
                                                                            :loading="loading" :disabled="!valid"
                                                                            @click="sendFindings">
                                                                            <span v-if="!loading">
                                                                                Submit
                                                                            </span>
                                                                            <clip-loader v-else :loading="loading"
                                                                                color="white"
                                                                                :size="'25px'"></clip-loader>
                                                                        </v-btn>

                                                                    </VCol>
                                                                </VRow>
                                                            </VForm>
                                                        </v-card-text>
                                                    </v-card>
                                                </v-dialog>
                                            </v-sheet>
                                        </v-col>

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
                                                        <th class="text-left">
                                                            Evidence
                                                        </th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr v-for="(interview) in getInvestigation?.findings"
                                                        :key="interview">
                                                        <td>{{ `${interview?.user?.lastName}
                                                            ${interview?.user?.firstName}` }}</td>
                                                        <td>{{ `${interview?.description}` }}</td>
                                                        <td class="text-capitalize">{{ `${interview?.type}
                                                            ${interview?.type == 'conclusion' ?
                '' : 'Cause'}` }}</td>
                                                        <td>

                                                            <v-menu v-if="interview?.media?.length > 0">
                                                                <template v-slot:activator="{ props }">
                                                                    <v-btn color="primary" dark v-bind="props" flat>
                                                                        Files </v-btn>
                                                                </template>

                                                                <v-list>
                                                                    <v-list-item
                                                                        v-for="(media, index) in interview?.media"
                                                                        :key="media">
                                                                        <a :href="media?.full_url" target="_blank">
                                                                            <v-list-item-title>
                                                                                <v-icon class="mr-2" size="small">
                                                                                    mdi-eye
                                                                                </v-icon>
                                                                                {{ `View Evidence
                                                                                ${computedIndex(index)}` }}
                                                                            </v-list-item-title>
                                                                        </a>
                                                                    </v-list-item>

                                                                </v-list>
                                                            </v-menu>
                                                        </td>
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
                                        <v-btn color="primary" @click="changeTab('tab-5')"
                                            v-if="isLoggedInUserIsLead">Next Step</v-btn>
                                    </v-col>
                                </v-row>
                            </v-window-item>
                            <v-window-item value="tab-5" class="pa-1">
                                <div>
                                    <v-row class="mt-3 px-4">


                                        <v-col cols="12">
                                            <v-btn color="primary" @click="setViewRecommendationDialog(true)"
                                                class="mr-2" v-if="isLoggedInUserIsMember">Add
                                                Recommendation</v-btn>


                                            <v-sheet>
                                                <v-dialog v-model="viewRecommendationDialog" max-width="500">
                                                    <v-card>

                                                        <v-card-text>
                                                            <div class="d-flex justify-space-between">
                                                                <h3 class="text-h3">Add </h3>
                                                                <v-btn icon @click="setViewRecommendationDialog(false)"
                                                                    size="small" flat>
                                                                    <XIcon size="16" />
                                                                </v-btn>
                                                            </div>
                                                        </v-card-text>
                                                        <v-divider></v-divider>

                                                        <v-card-text>

                                                            <VForm v-model="valid" ref="formContainer" fast-fail
                                                                lazy-validation @submit.prevent="sendRecommendation"
                                                                class="py-1">
                                                                <VRow class="mt-5 mb-3">

                                                                    <VCol cols="12" md="12">
                                                                        <v-label
                                                                            class="text-subtitle-1 font-weight-medium pb-1">Title</v-label>
                                                                        <VTextField type="text"
                                                                            v-model="stepFiveFields.title"
                                                                            :rules="stepFiveFieldRules.title" required
                                                                            variant="outlined" label="Title"
                                                                            :color="stepFiveFields.title.length > 2 ? 'success' : 'primary'">
                                                                        </VTextField>
                                                                    </VCol>
                                                                    <VCol cols="12" md="12">
                                                                        <v-label
                                                                            class="text-subtitle-1 font-weight-medium pb-1">Description</v-label>
                                                                        <VTextarea variant="outlined" outlined
                                                                            name="Description" label="Description"
                                                                            v-model="stepFiveFields.description"
                                                                            :rules="stepFiveFieldRules.description"
                                                                            required
                                                                            :color="stepFiveFields.description.length > 10 ? 'success' : 'primary'">
                                                                        </VTextarea>
                                                                    </VCol>
                                                                    <VCol cols="12" md="12">
                                                                        <v-label
                                                                            class="font-weight-medium pb-1">User</v-label>
                                                                        <VSelect v-model="stepFiveFields.assigneeId"
                                                                            :items="getMembers"
                                                                            :rules="stepFiveFieldRules.assigneeId"
                                                                            label="Select" :selected="''"
                                                                            item-title='lastName' item-value="uuid"
                                                                            single-line variant="outlined"
                                                                            class="text-capitalize"
                                                                            :item-props="(item: any) => ({ title: `${item?.lastName} ${item?.firstName}`, subtitle: `${item?.email}` })">

                                                                        </VSelect>
                                                                    </VCol>
                                                                    <VCol cols="12" md="12">
                                                                        <v-label
                                                                            class="font-weight-medium pb-1">Severity</v-label>
                                                                        <VSelect v-model="stepFiveFields.priorityId"
                                                                            :items="getPriorities"
                                                                            :rules="stepFiveFieldRules.priorityId"
                                                                            label="Select" :selected="''"
                                                                            item-title="description" item-value="id"
                                                                            single-line variant="outlined"
                                                                            class="text-capitalize">
                                                                        </VSelect>
                                                                    </VCol>

                                                                    <VCol cols="12" md="6">
                                                                        <v-label
                                                                            class="text-subtitle-1 font-weight-medium pb-1">Start
                                                                            Date</v-label>
                                                                        <VueDatePicker
                                                                            input-class-name="dp-custom-input"
                                                                            v-model="stepFiveFields.start_date"
                                                                            :min-date="new Date()" required>
                                                                        </VueDatePicker>
                                                                    </VCol>
                                                                    <VCol cols="12" md="6">
                                                                        <v-label
                                                                            class="text-subtitle-1 font-weight-medium pb-1">End
                                                                            Date</v-label>
                                                                        <VueDatePicker
                                                                            input-class-name="dp-custom-input"
                                                                            :disabled='!stepFiveFields.start_date'
                                                                            v-model="stepFiveFields.end_date"
                                                                            :min-date="stepFiveFields.start_date ? new Date(stepFiveFields.start_date) : new Date()"
                                                                            required></VueDatePicker>
                                                                    </VCol>

                                                                    <VCol cols="12" lg="12" class="text-right">
                                                                        <v-btn color="error"
                                                                            @click="setViewRecommendationDialog(false)"
                                                                            variant="text">Cancel</v-btn>

                                                                        <v-btn color="primary" type="submit"
                                                                            :loading="loading" :disabled="!valid"
                                                                            @click="sendRecommendation">
                                                                            <span v-if="!loading">
                                                                                Submit
                                                                            </span>
                                                                            <clip-loader v-else :loading="loading"
                                                                                color="white"
                                                                                :size="'25px'"></clip-loader>
                                                                        </v-btn>

                                                                    </VCol>
                                                                </VRow>
                                                            </VForm>
                                                        </v-card-text>
                                                    </v-card>
                                                </v-dialog>

                                            </v-sheet>
                                        </v-col>

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
                                        </v-col>
                                    </v-row>

                                </div>
                                <v-row class="mt-3">
                                    <v-col cols="6">
                                        <v-btn color="primary" variant="tonal" @click="changeTab('tab-4')">Back</v-btn>
                                    </v-col>
                                    <v-col cols="6" class="text-right">
                                        <v-btn color="primary" @click="changeTab('tab-6')"
                                            v-if="isLoggedInUserIsLead">Next Step</v-btn>
                                    </v-col>
                                </v-row>
                            </v-window-item>
                            <v-window-item value="tab-6" class="pa-1">

                                <v-btn color="primary" @click="setViewReportDialog(true)" class="mr-2"
                                    v-if="isLoggedInUserIsLead">Add
                                    Report</v-btn>

                                <v-sheet>
                                    <v-dialog v-model="viewReportDialog" max-width="500">
                                        <v-card>

                                            <v-card-text>
                                                <div class="d-flex justify-space-between">
                                                    <h3 class="text-h3">Add Report</h3>
                                                    <v-btn icon @click="setViewReportDialog(false)" size="small" flat>
                                                        <XIcon size="16" />
                                                    </v-btn>
                                                </div>
                                            </v-card-text>
                                            <v-divider></v-divider>

                                            <v-card-text>

                                                <VForm v-model="valid" ref="formContainer" fast-fail lazy-validation
                                                    @submit.prevent="sendReport" class="py-1">
                                                    <VRow class="mt-5 mb-3">

                                                        <VCol cols="12" md="12">
                                                            <v-label
                                                                class="text-subtitle-1 font-weight-medium pb-1">Method
                                                                Of Investigation</v-label>
                                                            <VTextField type="text" v-model="stepSixFields.method"
                                                                :rules="stepSixFieldRules.method" required
                                                                variant="outlined" label="Method"
                                                                :color="stepSixFields.method.length > 2 ? 'success' : 'primary'">
                                                            </VTextField>
                                                        </VCol>
                                                        <VCol cols="12" md="12">
                                                            <v-label
                                                                class="text-subtitle-1 font-weight-medium pb-1">Title</v-label>
                                                            <VTextField type="text" v-model="stepSixFields.title"
                                                                :rules="stepSixFieldRules.title" required
                                                                variant="outlined" label="Title"
                                                                :color="stepSixFields.title.length > 2 ? 'success' : 'primary'">
                                                            </VTextField>
                                                        </VCol>
                                                        <VCol cols="12" md="12">
                                                            <v-label
                                                                class="text-subtitle-1 font-weight-medium pb-1">Description</v-label>
                                                            <VTextarea variant="outlined" outlined name="Description"
                                                                label="Description" v-model="stepSixFields.description"
                                                                :rules="stepSixFieldRules.description" required
                                                                :color="stepSixFields.description.length > 10 ? 'success' : 'primary'">
                                                            </VTextarea>
                                                        </VCol>
                                                        <VCol cols="12" md="12">
                                                            <v-label
                                                                class="text-subtitle-1 font-weight-medium pb-1">Incident
                                                                Date and Time</v-label>
                                                            <VueDatePicker input-class-name="dp-custom-input"
                                                                v-model="stepSixFields.incident_date_time"
                                                                :max-date="new Date()" required>
                                                            </VueDatePicker>
                                                        </VCol>
                                                        <VCol cols="12" md="12">
                                                            <v-label
                                                                class="text-subtitle-1 font-weight-medium pb-1">Location</v-label>
                                                            <VTextField type="text" v-model="stepSixFields.location"
                                                                :rules="stepSixFieldRules.location" required
                                                                variant="outlined" label="location"
                                                                :color="stepSixFields.location.length > 2 ? 'success' : 'primary'">
                                                            </VTextField>
                                                        </VCol>
                                                        <VCol cols="12" md="12">
                                                            <v-label
                                                                class="text-subtitle-1 font-weight-medium pb-1">Details
                                                                of injured workers/damages</v-label>
                                                            <VTextarea variant="outlined" outlined name="Details"
                                                                label="Details" v-model="stepSixFields.damages"
                                                                :rules="stepSixFieldRules.damages" required
                                                                :color="stepSixFields.damages.length > 10 ? 'success' : 'primary'">
                                                            </VTextarea>
                                                        </VCol>


                                                        <VCol cols="12" lg="12" class="text-right">
                                                            <v-btn color="error" @click="setViewReportDialog(false)"
                                                                variant="text">Cancel</v-btn>

                                                            <v-btn color="primary" type="submit" :loading="loading"
                                                                :disabled="!valid" @click="sendReport">
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
                                                <td>{{ '' }}</td>
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
                                    <v-col cols="12" sm="6" class="text-sm-right">
                                        <v-btn color="primary" @click="completeInvestigation"
                                            v-if="isLoggedInUserIsLead">Complete
                                            Investigation</v-btn>

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
