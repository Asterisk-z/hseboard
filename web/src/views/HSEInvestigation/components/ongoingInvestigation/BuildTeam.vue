<script setup lang="ts">
import { ref, onMounted, computed, watch } from 'vue';
import { useRoute } from 'vue-router'
import { router } from '@/router';

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
const reportStore = useReportStore();
const teamMemberStore = useTeamMemberStore();
const openLinks = useOpenLinksStore();
const investigationStore = useInvestigationStore();


onMounted(() => {
    // investigationStore.getInvestigation(route.params.observation_id as string);
    teamMemberStore.getTeamMembers();
    // openLinks.getPriorities();
    // investigationStore.getInvestigationQuestions(route.params.observation_id as string);
});

const computedIndex = (index: any) => ++index;

const getActiveOrg: any = computed(() => (organizationStore.getActiveOrg()));
const getAuthUser: any = computed(() => (authStore.loggedUser));
const getMembers: any = computed(() => (teamMemberStore.members));
// const getInvestigationQuestions: any = computed(() => (investigationStore.questions));
const getInvestigation: any = computed(() => (investigationStore.investigation));
// const getPriorities: any = computed(() => (openLinks.priorities));
const isLoggedInUserOwnsActionOrg: any = computed(() => (getAuthUser.value?.id == getActiveOrg.value?.user_id));
const isLoggedInUserOwnInvestigationOrganization: any = computed(() => (getAuthUser.value?.id == getActiveOrg.value?.user_id && getAuthUser.value?.id == getInvestigation.value.organization?.user_id));
const isLoggedInUserIsLead: any = computed(() => (getAuthUser.value?.id == getInvestigation.value?.lead?.member?.id));






const valid = ref(true);;
const formContainer = ref('')
const loading = ref(false);
const setLoading = (value: boolean) => {
    loading.value = value;
}

const addMemberDialog = ref(false);
const setAddMemberDialog = (value: boolean) => {
    addMemberDialog.value = value;
}
const addExternalMemberDialog = ref(false);
const setAddExternalMemberDialog = (value: boolean) => {
    addExternalMemberDialog.value = value;
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
    leadInvestigator: getInvestigation.value?.lead?.member?.id ?? '',
    externalleadInvestigator: getInvestigation.value?.lead?.member?.id ?? '',
    teamMember: [],
    externalTeamMembers: [],
    groupChatName: '',
    orgToken: '',
    organization: null as null || {} as OrganizationType,
    questions: [],
    externalOrgMembers: null as null || {} as UserType[],
    organization_id: getActiveOrg.value?.uuid,
});

const filteredMember: any = computed(() => (getMembers.value?.filter((member: any) => (member.id != fields.value?.leadInvestigator))));
const filteredExternalMember: any = computed(() => (fields.value.externalOrgMembers?.filter((member: any) => (member.id != fields.value?.externalleadInvestigator))));



const fieldRules: any = ref({
    leadInvestigator: [
        (v: number) => !!v || 'Field is Required',
    ],
    teamMember: [
        (v: number) => !!v || 'Field is Required',
    ],
    orgToken: [
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
            setAddMemberDialog(false)
            investigationStore.getInvestigation(route.params.observation_id as string);
        }

        setLoading(false)
        setAddMemberDialog(false)

        fields.value.leadInvestigator = "";
        fields.value.teamMember = [];
        fields.value.groupChatName = "";


    } catch (error) {
        console.log(error)
        setLoading(false)
        setAddMemberDialog(false)
    }

}



const fetchOrganization = async (token: string) => {

    try {

        const resp = await organizationStore.getTokenOrganizations(fields.value.orgToken)
            .catch((error: any) => {
                console.log(error)
                throw error
            })
            .then((resp: any) => {
                return resp
            });

        if (resp) {

            fields.value.organization = resp
            fetchOrgUser(resp.uuid)
        } else {

            fields.value.organization = {
                uuid: '',
                name: '',
                token: '',
            }
        }


    } catch (error) {

    }
}


const fetchOrgUser = async (token: string) => {

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

            fields.value.externalOrgMembers = resp

        } else {

            fields.value.externalOrgMembers = []
        }


    } catch (error) {

    }
}

const saveExternal = async (e: any) => {
    e.preventDefault();

    try {
        setLoading(true)

        const values = { ...fields.value }



        let objectValues = {
            "investigation_id": getInvestigation.value?.uuid,
            "team_members": values?.externalTeamMembers,
            "lead_investigator": values?.externalleadInvestigator,
            'organization_id': values?.organization?.uuid,
            'group_name': values?.groupChatName,
        }



        const resp = await investigationStore.setExternalInvestigationMember(objectValues)
            .catch((error: any) => {
                console.log(error)
                throw error
            })
            .then((resp: any) => {
                return resp
            });

        if (resp?.message == 'success') {
            setLoading(false)
            setAddExternalMemberDialog(false)
            investigationStore.getInvestigation(route.params.observation_id as string);
        }

        setLoading(false)
        setAddExternalMemberDialog(false)

        fields.value.orgToken = "";
        fields.value.externalleadInvestigator = "";
        fields.value.externalTeamMembers = [];
        fields.value.organization = {
            uuid: '',
            name: '',
            token: '',
        }


    } catch (error) {
        console.log(error)
        setLoading(false)
        setAddExternalMemberDialog(false)
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
            text: 'Do you want to remove member?',
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
                        investigationStore.getInvestigation(route.params.observation_id as string);
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

const clearExternalMembers = () => {
    fields.value.externalTeamMembers.length = 0
    fields.value.teamMember.length = 0
}


</script>

<template>
    <div>
        <v-row class="mt-3 px-4">
            <v-col cols="12">
                <template v-if="isLoggedInUserOwnInvestigationOrganization">
                    <v-btn color="primary" @click="setAddMemberDialog(true)" class="mr-2">Add
                        Investigators</v-btn>
                    <v-btn color="primary" @click="setAddExternalMemberDialog(true)" class="mr-2">Add
                        External Investigators</v-btn>
                </template>

                <v-sheet>
                    <v-dialog v-model="addMemberDialog" max-width="800">
                        <v-card>

                            <v-card-text>
                                <div class="d-flex justify-space-between">
                                    <h3 class="text-h3">Add Member </h3>
                                    <v-btn icon @click="setAddMemberDialog(false)" size="small" flat>
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
                                            <v-label class="font-weight-medium pb-1">Select
                                                a lead
                                                investigator</v-label>
                                            <VSelect v-model="fields.leadInvestigator" :items="getMembers"
                                                @update:modelValue="clearExternalMembers"
                                                :rules="fieldRules.leadInvestigator" label="Select"
                                                item-title="lastName" item-value="id"
                                                :item-props="(item: any) => ({ title: `${item?.lastName} ${item?.firstName}`, subtitle: `${item?.email}` })"
                                                single-line variant="outlined" class="text-capitalize">
                                            </VSelect>
                                        </VCol>

                                        <VCol cols="12" md="12" v-if="fields.leadInvestigator">
                                            <v-label class="font-weight-medium pb-1">Select
                                                Team Member</v-label>
                                            <VSelect v-model="fields.teamMember" :items="filteredMember"
                                                :rules="fieldRules.teamMember" label="Select" item-title="lastName"
                                                item-value="id" single-line variant="outlined" class="text-capitalize"
                                                chips
                                                :item-props="(item: any) => ({ title: `${item?.lastName} ${item?.firstName}`, subtitle: `${item?.email}` })"
                                                multiple>

                                            </VSelect>
                                        </VCol>

                                        <VCol cols="12" md="12">
                                            <v-label class="text-subtitle-1 font-weight-medium pb-1">Group
                                                Chat Name</v-label>
                                            <VTextField type="text" v-model="fields.groupChatName"
                                                :rules="fieldRules.groupChatName" required variant="outlined"
                                                label="Group Chat Name"
                                                :color="fields.groupChatName.length > 2 ? 'success' : 'primary'">
                                            </VTextField>
                                        </VCol>


                                        <VCol cols="12" lg="12" class="text-right">
                                            <v-btn color="error" @click="setAddMemberDialog(false)"
                                                variant="text">Cancel</v-btn>

                                            <v-btn color="primary" type="submit" :loading="loading" :disabled="!valid"
                                                @click="save">
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
                        </v-card>
                    </v-dialog>
                    <v-dialog v-model="addExternalMemberDialog" max-width="800">
                        <v-card>

                            <v-card-text>
                                <div class="d-flex justify-space-between">
                                    <h3 class="text-h3">Add External Investigators </h3>
                                    <v-btn icon @click="setAddExternalMemberDialog(false)" size="small" flat>
                                        <XIcon size="16" />
                                    </v-btn>
                                </div>
                            </v-card-text>
                            <v-divider></v-divider>

                            <v-card-text>

                                <VForm v-model="valid" ref="formContainer" fast-fail lazy-validation
                                    @submit.prevent="saveExternal" class="py-1">
                                    <VRow class="mt-5 mb-3">

                                        <VCol cols="12" md="12">
                                            <v-label class="text-subtitle-1 font-weight-medium pb-1">Organization
                                                Invite Token</v-label>
                                            <VTextField type="text" v-model="fields.orgToken"
                                                :rules="fieldRules.orgToken" required variant="outlined" label=""
                                                @change="fetchOrganization"
                                                :color="fields.orgToken.length > 2 ? 'success' : 'primary'">
                                                <template v-slot:append>
                                                    <v-btn color="primary" @click="fetchOrganization"
                                                        :disabled="fields.orgToken?.length < 12">Search</v-btn>
                                                </template>
                                            </VTextField>
                                        </VCol>

                                        <VCol cols="12" md="12" v-if="fields.orgToken?.length > 12">

                                            <div v-if="fields.organization">
                                                <v-card elevation="10" rounded="md">
                                                    <v-card-item>
                                                        <div class="d-flex align-center">
                                                            <div class="pl-4 mt-n1">
                                                                <h5 class="text-h6">{{
                    fields.organization?.name
                }}</h5>
                                                                <h5 class="text-h6">{{
                        fields.organization?.token
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

                                        <VCol cols="12" md="12" v-if="fields.externalOrgMembers?.length > 0">
                                            <v-label class="font-weight-medium pb-1">Select
                                                a lead
                                                investigator</v-label>
                                            <VSelect v-model="fields.externalleadInvestigator"
                                                :items="fields.externalOrgMembers"
                                                @update:modelValue="clearExternalMembers" label="Select"
                                                item-title="full_name" item-value="id"
                                                :item-props="(item: any) => ({ title: `${item?.lastName} ${item?.firstName}`, subtitle: `${item?.email}` })"
                                                single-line variant="outlined" class="text-capitalize">
                                            </VSelect>
                                        </VCol>


                                        <VCol cols="12" md="12" v-if="fields.externalOrgMembers?.length > 0">
                                            <v-label class="font-weight-medium pb-1">Select
                                                Team Member</v-label>
                                            <VSelect v-model="fields.externalTeamMembers"
                                                :items="filteredExternalMember" :rules="fieldRules.externalTeamMember"
                                                label="Select" item-title="full_name" item-value="id" single-line
                                                variant="outlined" class="text-capitalize" chips multiple>

                                            </VSelect>
                                        </VCol>

                                        <VCol cols="12" md="12">
                                            <v-label class="text-subtitle-1 font-weight-medium pb-1">Group
                                                Chat Name</v-label>
                                            <VTextField type="text" v-model="fields.groupChatName"
                                                :rules="fieldRules.groupChatName" required variant="outlined"
                                                label="Group Chat Name"
                                                :color="fields.groupChatName.length > 2 ? 'success' : 'primary'">
                                            </VTextField>
                                        </VCol>

                                        <VCol cols="12" lg="12" class="text-right">
                                            <v-btn color="error" @click="setAddExternalMemberDialog(false)"
                                                variant="text">Cancel</v-btn>

                                            <v-btn color="primary" type="submit" :loading="loading" :disabled="!valid"
                                                @click="saveExternal">
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
                                Full Name
                            </th>
                            <th class="text-left">
                                Email
                            </th>
                            <th class="text-left">
                                Position
                            </th>
                            <th class="text-left">
                                Action
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(member, index) in getInvestigation?.all_members" :key="member">
                            <td>{{ computedIndex(index) }}</td>
                            <td>{{ `${member?.member?.lastName}
                                ${member?.member?.firstName}` }}</td>
                            <td>{{ `${member?.member?.email}` }}</td>
                            <td>{{ `${member?.position}` }}</td>
                            <td>
                                <!-- There is a  -->
                                <v-btn color='error' size='small' @click="removeMember(member?.member?.id)"
                                    v-if="isLoggedInUserOwnInvestigationOrganization">
                                    Remove
                                </v-btn>
                            </td>
                        </tr>
                    </tbody>
                </v-table>
            </v-col>
        </v-row>
    </div>
</template>
<style lang="scss"></style>
