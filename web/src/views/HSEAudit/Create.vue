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


onMounted(() => {
    auditTemplateStore.getAuditOptions();
    auditTemplateStore.getAuditTypes();

    // teamMemberStore.getTeamMembers();
    // openLinks.getPriorities();
    // investigationStore.getInvestigationQuestions(route.params.observation_id as string);
});

const getAuditOptions: any = computed(() => (auditTemplateStore.auditOptions));
const getAuditTypes: any = computed(() => (auditTemplateStore.auditTypes));

const getActiveOrg: any = computed(() => (organizationStore.getActiveOrg()));
const getAuthUser: any = computed(() => (authStore.loggedUser));
const getMembers: any = computed(() => (teamMemberStore.members));
const getInvestigationQuestions: any = computed(() => (investigationStore.questions));
const getInvestigation: any = computed(() => (investigationStore.investigation));
const getPriorities: any = computed(() => (openLinks.priorities));
const isLoggedInUserOwnsActionOrg: any = computed(() => (getAuthUser.value?.id == getActiveOrg.value?.user_id));
const isLoggedInUserIsLead: any = computed(() => (getAuthUser.value?.id == getInvestigation.value?.lead?.member?.id));


const page = ref({ title: 'Create Audit' });
const breadcrumbs = ref([
    {
        text: 'Dashboard',
        disabled: false,
        href: 'dashboard'
    },
    {
        text: 'Audit Management',
        disabled: false,
        href: '/hse-audit'
    },
    {
        text: 'Create',
        disabled: true,
        href: '#'
    }
]);






const valid = ref(true);;
const formContainer = ref()
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
const viewQuestionTeamDialog = ref(false);
const setSelectQuestionaireTeam = (value: boolean) => {
    viewQuestionTeamDialog.value = value;
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

const fields = ref({
    leadInvestigator: getInvestigation?.lead ?? '',
    teamMember: [],
    groupChatName: '',

    questions: [],
    organization_id: getActiveOrg.value?.uuid,
});

const filteredMember: any = computed(() => (getMembers.value?.filter((member: any) => (member.id != fields.value?.leadInvestigator))));

const members_ids: any = computed(() => (getInvestigation.value?.all_members.map((member: any) => (member.member_id))));
const filteredNonMember: any = computed(() => (getMembers.value?.filter((member: any) => (!members_ids.value?.includes(member.id)))));


const fieldRules: any = ref({
    leadInvestigator: [
        (v: number) => !!v || 'Field is Required',
    ],
    teamMember: [
        (v: number) => !!v || 'Field is Required',
    ],
    groupChatName: [
        (v: string) => !!v || 'Field is Required',
    ],
})



const save = async (e: any) => {
    e.preventDefault();

    try {
        setLoading(true)

        const values = { ...fields.value }



        let objectValues = {
            "organization_id": getActiveOrg.value?.uuid,
            "investigation_id": getInvestigation.value?.uuid,
            "lead_investigator": values?.leadInvestigator,
            "team_members": values?.teamMember,
            'group_name': values?.groupChatName,
        }

        const resp = await investigationStore.setInvestigationMember(objectValues)
            .catch((error: any) => {
                console.log(error)
                throw error
            })
            .then((resp: any) => {
                return resp
            });

        if (resp?.message == 'success') {
            setLoading(false)
            // setAddMemberDialog(false)
            // investigationStore.getInvestigation(route.params.observation_id as string);
        }

        setLoading(false)
        // setAddMemberDialog(false)



    } catch (error) {
        console.log(error)
        setLoading(false)
        // setAddMemberDialog(false)
    }

}

const removeMember = async (member: any) => {

    try {
        setLoading(true)

        let objectValues = {
            "organization_id": getActiveOrg.value?.uuid,
            "investigation_id": getInvestigation.value?.uuid,
            "team_member": member
        }

        Swal.fire({
            title: 'Info!',
            text: 'Do you want to start investigation?',
            icon: 'info',
            confirmButtonText: 'Yes',
            cancelButtonText: 'No',
            showCancelButton: true,
            allowOutsideClick: false,
        }).then((result) => {
            if (result.isConfirmed) {


                investigationStore.removeMember(objectValues)
                    .catch((error: any) => {
                        throw error
                    })
                    .then((resp: any) => {
                        //  investigationStore.getInvestigation(route.params.observation_id as string);
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

const stepTwoFields = ref({
    question: "",
    questions: [],
    members: [],
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
        console.log(values)

        let objectValues = {
            "organization_id": getActiveOrg.value?.uuid,
            "investigation_id": getInvestigation.value?.uuid,
            "questions": values?.questions,
            "members": values?.members,
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

            // investigationStore.getInvestigation(route.params.observation_id as string);
        }

        setLoading(false)



    } catch (error) {
        console.log(error)
        setLoading(false)
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
            // investigationStore.getInvestigation(route.params.observation_id as string);
        }

        setLoading(false)
        setAddInviteDialog(false)



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


        let objectValues = {
            "organization_id": getActiveOrg.value?.uuid,
            "investigation_id": getInvestigation.value?.uuid,
            "type": values?.type,
            "detail": values?.detail,
        }

        const resp = await investigationStore.sendInvestigationFindings(objectValues)
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
            // investigationStore.getInvestigation(route.params.observation_id as string);
        }

        setLoading(false)
        setViewFindingDialog(false)
        stepFourFields.value.detail = ''


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
            // investigationStore.getInvestigation(route.params.observation_id as string);
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
            // investigationStore.getInvestigation(route.params.observation_id as string);
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

            // investigationStore.getInvestigationQuestions(route.params.observation_id as string);
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
                        //  investigationStore.getInvestigation(route.params.observation_id as string);route.params.main_audit_id as string
                        // router.push(`/hse-investigation`)
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

// ----------------------------------------------------------------------------------------------------
// ----------------------------------------------------------------------------------------------------
// ----------------------------------------------------------------------------------------------------
// ----------------------------------------------------------------------------------------------------
// ----------------------------------------------------------------------------------------------------
// ----------------------------------------------------------------------------------------------------
// ----------------------------------------------------------------------------------------------------
// ----------------------------------------------------------------------------------------------------
// ----------------------------------------------------------------------------------------------------
// ----------------------------------------------------------------------------------------------------
// ----------------------------------------------------------------------------------------------------
// ----------------------------------------------------------------------------------------------------
// ----------------------------------------------------------------------------------------------------
// ----------------------------------------------------------------------------------------------------
// ----------------------------------------------------------------------------------------------------
// ----------------------------------------------------------------------------------------------------
// ----------------------------------------------------------------------------------------------------
// ----------------------------------------------------------------------------------------------------
// ----------------------------------------------------------------------------------------------------
// ----------------------------------------------------------------------------------------------------
// ----------------------------------------------------------------------------------------------------
// ----------------------------------------------------------------------------------------------------
// ----------------------------------------------------------------------------------------------------
// ----------------------------------------------------------------------------------------------------
// ----------------------------------------------------------------------------------------------------
// ----------------------------------------------------------------------------------------------------
// ----------------------------------------------------------------------------------------------------
// ----------------------------------------------------------------------------------------------------
// ----------------------------------------------------------------------------------------------------

type OrganizationType = {
    uuid: string,
    name: string,
    token: string,
};

const stepOneFields = ref({
    orgToken: '',
    organization: null as null || {} as OrganizationType,
    auditTitle: '',
    auditType: '',
    auditScope: '',
    auditTemplate: '',
    auditTemplates: '',
    auditOption: '',
    organization_id: getActiveOrg.value?.uuid,
});

const addAuditSetupDialog = ref(false);
const setAddAuditSetupDialog = (value: boolean) => {
    addAuditSetupDialog.value = value;
}

const stepOneFieldRules: any = ref({
    auditType: [
        (v: number) => !!v || 'Field is Required',
    ],
    orgToken: [
        (v: number) => !!v || 'Field is Required',
    ],
    organization: [
        (v: string) => !!v || 'Field is Required',
    ],
    auditTitle: [
        (v: string) => !!v || 'Field is Required',
    ],
    auditScope: [
        (v: string) => !!v || 'Field is Required',
    ],
})

const fetchOrganization = async (token: string) => {
    // console.log(stepOneFields.value.orgToken);

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
            };
        }


    } catch (error) {

    }
}

const fetchTemplate = async (token: string) => {

    try {


        const resp = await auditTemplateStore.getAuditTypeTemplate(stepOneFields.value.auditType)
            .catch((error: any) => {
                console.log(error)
                throw error
            })
            .then((resp: any) => {
                return resp
            });


        if (resp) {
            stepOneFields.value.auditTemplates = resp
        } else {
            stepOneFields.value.auditTemplates = ''
        }

        stepOneFields.value.auditTemplate = ''

    } catch (error) {

    }
}


const sendAuditOption = async (e: any) => {
    e.preventDefault();

    try {
        setLoading(true)

        const values = { ...stepOneFields.value }


        let objectValues = {
            "organization_id": getActiveOrg.value?.uuid,
            "recipient_organization_id": Object.keys(values?.organization).length === 0 ? getActiveOrg.value?.uuid : values.organization?.uuid,
            'audit_type_id': values?.auditType,
            'audit_template_id': values?.auditTemplate,
            'audit_scope': values?.auditScope,
            'audit_title': values?.auditTitle,
        }

        const resp = await mainAuditStore.startMainAudit(objectValues)
            .catch((error: any) => {
                console.log(error)
                throw error
            })
            .then((resp: any) => {
                return resp
            });

        if (resp?.message == 'success') {
            setLoading(false)

            stepOneFields.value.auditOption = '';
            stepOneFields.value.auditType = '';
            stepOneFields.value.orgToken = '';
            stepOneFields.value.organization = {
                uuid: '',
                name: '',
                token: '',
            };
            stepOneFields.value.auditTitle = '';
            stepOneFields.value.auditScope = '';
            stepOneFields.value.auditTemplate = '';
            stepOneFields.value.auditTemplates = '';

            router.push(`/ongoing-hse-audit-report/${resp.data?.data?.uuid}`)

            setAddAuditSetupDialog(false)
        }

        setLoading(false)
        setAddAuditSetupDialog(false)



    } catch (error) {
        console.log(error)
        setLoading(false)
        setAddAuditSetupDialog(false)
    }

}

const gotoRoute = (value: string) => {
    router.push(value)
}

</script>

<template>
    <div>
        <BaseBreadcrumb :title="page.title" :breadcrumbs="breadcrumbs"></BaseBreadcrumb>
        <!-- {{ getInvestigation }} -->
        <v-row>
            <!-- <v-row v-if="getInvestigation?.length > 0"> -->
            <v-col cols="12" md="7">

                <UiParentCard variant="outlined">
                    <v-card-text>

                        <VForm v-model="valid" ref="formContainer" fast-fail lazy-validation
                            @submit.prevent="sendAuditOption" class="py-1">
                            <VRow class="mt-5 mb-3">

                                <VCol cols="12" md="12">
                                    <v-label class="font-weight-medium pb-1">Type Of
                                        Audit</v-label>
                                    <VSelect v-model="stepOneFields.auditOption" :items="getAuditOptions" label="Select"
                                        single-line variant="outlined" class="text-capitalize"
                                        :rules="[(v: any) => !!v || 'You must select to continue!']"
                                        item-title='description' item-value="name" required>
                                    </VSelect>
                                </VCol>

                                <VCol cols="12" md="12" v-if="stepOneFields.auditOption == 'external'">
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
                                    v-if="stepOneFields.auditOption == 'external' && stepOneFields.orgToken?.length > 12">

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



                                <template v-if="stepOneFields.auditOption">

                                    <VCol cols="12" md="12">
                                        <v-label class="text-subtitle-1 font-weight-medium pb-1">Audit
                                            Title</v-label>
                                        <VTextField type="text" v-model="stepOneFields.auditTitle"
                                            :rules="stepOneFieldRules.auditTitle" required variant="outlined" label=""
                                            :color="stepOneFields.auditTitle.length > 2 ? 'success' : 'primary'">
                                        </VTextField>
                                    </VCol>

                                    <VCol cols="12" md="12">
                                        <v-label class="font-weight-medium pb-1">Audit
                                            Template Type</v-label>
                                        <VSelect v-model="stepOneFields.auditType" :items="getAuditTypes" label="Select"
                                            single-line variant="outlined" @update:modelValue="fetchTemplate"
                                            class="text-capitalize"
                                            :rules="[(v: any) => !!v || 'You must select to continue!']"
                                            item-title='description' item-value="id" required>
                                        </VSelect>
                                    </VCol>

                                    <VCol cols="12" md="12" v-if="stepOneFields.auditType">
                                        <v-label class="font-weight-medium pb-1">Template</v-label>
                                        <VSelect v-model="stepOneFields.auditTemplate"
                                            :items="stepOneFields.auditTemplates" label="Select" single-line
                                            variant="outlined" class="text-capitalize"
                                            :rules="[(v: any) => !!v || 'You must select to continue!']"
                                            item-title='description' item-value="id" required>
                                        </VSelect>
                                    </VCol>

                                    <VCol cols="12" md="12">
                                        <v-label class="text-subtitle-1 font-weight-medium pb-1">Audit
                                            Scope</v-label>
                                        <VTextField type="text" v-model="stepOneFields.auditScope"
                                            :rules="stepOneFieldRules.auditScope" required variant="outlined" label=""
                                            :color="stepOneFields.auditScope.length > 2 ? 'success' : 'primary'">
                                        </VTextField>
                                    </VCol>
                                </template>



                                <VCol cols="12" lg="12" class="text-right">
                                    <v-btn color="error" @click="gotoRoute('hse-audit')" variant="text">Cancel</v-btn>

                                    <v-btn color="primary" type="submit" :loading="loading" :disabled="!valid"
                                        @click="sendAuditOption">
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
