<script setup lang="ts">
import { ref, onMounted, computed, nextTick } from 'vue';


import BaseBreadcrumb from '@/components/shared/BaseBreadcrumb.vue';
import { VDataTable } from 'vuetify/labs/VDataTable'
import { useOrganizationStore } from '@/stores/organizationStore';
import { useTeamMemberStore } from '@/stores/teamMemberStore';
import { useAuthStore } from '@/stores/auth';
import { useOpenLinksStore } from '@/stores/openLinks';
import moment from 'moment'

import { router } from '@/router';


const authStore = useAuthStore();
const organizationStore = useOrganizationStore();
const teamMemberStore = useTeamMemberStore();
const openLinks = useOpenLinksStore();

onMounted(() => {
    teamMemberStore.getTeamMembers();
    openLinks.getAccountRoles();
});

const getTeamMembers = computed(() => (teamMemberStore.members));
const getActiveOrg = computed(() => (organizationStore.getActiveOrg()));
const getAuthUser = computed(() => (authStore.loggedUser));
const getAccountRoles = computed(() => (openLinks.accountRoles));
const isLoggedInUserOwnsActionOrg = computed(() => (getAuthUser.value?.id == getActiveOrg.value?.user_id));


const page = ref({ title: 'Team Member' });
const breadcrumbs = ref([
    {
        text: 'Dashboard',
        disabled: false,
        href: 'dashboard'
    },
    {
        text: 'Team Members',
        disabled: true,
        href: '#'
    }
]);


const valid = ref(true);
const editValid = ref(true);
const formContainer = ref()
const loading = ref(false);
const setLoading = (value: boolean) => {
    loading.value = value;
}
const dialog = ref(false);
const setDialog = (value: boolean) => {
    dialog.value = value;
}
const editDialog = ref(false);
const setEditDialog = (value: boolean) => {
    editDialog.value = value;
    if (value == false) selectItem({})
}
const viewDialog = ref(false);
const setViewDialog = (value: boolean) => {
    viewDialog.value = value;
    if (value == false) selectItem({})
}
const deleteDialog = ref(false);
const setDeleteDialog = (value: boolean) => {
    deleteDialog.value = value;
    if (value == false) selectItem({})
}
const selectedItem = ref({});
const selectItem = (item: any, action: string = '') => {
    selectedItem.value = Object.assign({}, item.raw);
 
    switch (action) {
        case 'view':
            setViewDialog(true)
            break;
        case 'edit':
            editFields.value.user_id = selectedItem.value?.uuid
            editFields.value.firstName = selectedItem.value?.firstName
            editFields.value.lastName = selectedItem.value?.lastName
            editFields.value.phoneNumber = selectedItem.value?.phone
            editFields.value.emailAddress = selectedItem.value?.email
            editFields.value.accountRole = selectedItem.value?.account_role?.name
            setEditDialog(true)
            break;
        case 'delete':
            setDeleteDialog(true)
            break;
        default:
            break;
    }
    
}




const search = ref('');
const headers = ref([
    {
        key: 'sn',
        title: 'SN',
        sortable: false,
    },
    {
        key: 'lastName',
        title: 'Full Name',
        value: (item: any) => `${item.lastName} ${item.firstName}`,
    },
    {
        key: 'email',
        title: 'Email',
        value: (item: any) => `${item.email}`
    },
    {
        key: 'phone',
        title: 'Phone Number',
        value: (item: any) => `${item.phone}`
    },
    {
        key: 'created_at',
        title: 'Date Created',
        value: (item: any) => `${moment(item.created_at).format('MMMM Do YYYY, h:mm:ss a')}`
    },
    {
        key: 'action',
        sortable: false,
        // align: 'end',
        title: 'Action',
    },
]);


const passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]).{8,}$/;
const showPassword = ref(false);

const fields = ref({
    firstName: "",
    lastName: "",
    phoneNumber: "",
    emailAddress: "",
    password: "",
    confirmPassword: "",
    accountRole: "",
    organization_id: getActiveOrg.value?.uuid
});
const editFields = ref({
    firstName: "",
    lastName: "",
    phoneNumber: "",
    emailAddress: "",
    accountRole: "",
    organization_id: getActiveOrg.value?.uuid,
    user_id: ""
});

const fieldRules : any = ref({
    firstName: [
        (v: string) => !!v || 'First Name is required',
        (v: string) => v.length > 2 || 'More than two letters required'
    ],
    lastName: [
        (v: string) => !!v || 'Last Name is required',
        (v: string) => v.length > 2 || 'More than two letters required'
    ],
    phoneNumber: [
        (v: string) => !!v || 'Phone Number is required',
        (v: string) => (v && v.length == 11) || 'Phone Number must be 11 characters',
    ],
    password: [
        (v: string) => !!v || 'Password is required',
        (v: string) => (v && passwordRegex.test(fields.value.password)) || 'Password must be uppercase, lowercase, numbers and 8 characters',
    ],
    confirmPassword: [
        (v: string) => !!v || 'Password is required',
        (v: string) => (v && v == fields.value.password) || 'Password does not march',
        (v: string) => (v && passwordRegex.test(fields.value.confirmPassword)) || 'Password most be uppercase, lowercase, numbers and 8 characters',
    ],
    emailAddress: [(v: string) => !!v || 'E-mail is required', (v: string) => /.+@.+\..+/.test(v) || 'E-mail must be valid'],
    accountRole: [
        (v: string) => !!v || 'Account Role is required',
    ],
})


const save = async (e: any) => {
    e.preventDefault();

    try {
        setLoading(true)

        const values = { ...fields.value }
        let objectValues = {
            "firstName": values?.firstName,
            "lastName": values?.lastName,
            "phoneNumber": values?.phoneNumber,
            "emailAddress": values?.emailAddress,
            "accountRole": values?.accountRole,
            "organization_id": values?.organization_id,
            "password": values?.password,
            "password_confirmation": values?.confirmPassword
        }
        
        const resp = await teamMemberStore.addMember(objectValues)
            .catch((error: any) => {
                console.log(error)
                throw error
            })
            .then((resp: any) => {
                return resp
            });
            
        if (resp?.message == 'success') {
            setLoading(false)
            setDialog(false)
            setTimeout(() => {
                formContainer.value.reset()
            }, 2000)
            teamMemberStore.getTeamMembers();
        }

        setLoading(false)
        setDialog(false)



    } catch (error) {
        console.log(error)
        setLoading(false)
        setDialog(false)
    }

}


const update = async (e: any) => {
    e.preventDefault();

    try {
        setLoading(true)

        const values = { ...editFields.value }
        let objectValues = {
            "firstName": values?.firstName,
            "lastName": values?.lastName,
            "phoneNumber": values?.phoneNumber,
            "emailAddress": values?.emailAddress,
            "accountRole": values?.accountRole,
            "organization_id": values?.organization_id,
            "user_id": values?.user_id,
        }

        const resp = await teamMemberStore.editMember(objectValues)
            .catch((error: any) => {
                throw error
            })
            .then((resp: any) => {
                return resp
            });

        if (resp?.message == 'success') {
            setLoading(false)
            setEditDialog(false)
            setTimeout(() => {
                formContainer.value.reset()
            }, 2000)
            teamMemberStore.getTeamMembers();
        }

        setLoading(false)
        setEditDialog(false)



    } catch (error) {
        console.log(error)
        setLoading(false)
        setEditDialog(false)
    }

}

const removeUser = async (team: any) => {

    try {
        setLoading(true)

        let objectValues = {
            "organization_id": getActiveOrg.value?.uuid,
            "user_id": team?.uuid,
        }

        const resp = await teamMemberStore.removeMember(objectValues)
            .catch((error: any) => {
                throw error
            })
            .then((resp: any) => {
                return resp
            });

        if (resp?.message == 'success') {
            setLoading(false)
            setDeleteDialog(false)

            teamMemberStore.getTeamMembers();
        }

        setLoading(false)
        setDeleteDialog(false)



    } catch (error) {
        console.log(error)
        setLoading(false)
        setDeleteDialog(false)
    }

}
const goToRequest = () => {
    router.push('/organization-requests')
}

</script>

<template>
    <div>
        <BaseBreadcrumb :title="page.title" :breadcrumbs="breadcrumbs"></BaseBreadcrumb>
        <v-row>
            <v-col cols="12" md="12">

                <v-card :title="`${getActiveOrg.name} Team Members`" flat>


                    <template v-slot:append>
                        <v-sheet>
                            <v-btn color="primary" class="mr-2" @click="setDialog(true)">Add Team Member</v-btn>
                            <v-btn color="primary" class="mr-2" @click="goToRequest">Requests Team Member</v-btn>

                            <v-dialog v-model="dialog" max-width="800">
                                <v-card>
                                    <v-card-text>
                                        <div class="d-flex justify-space-between">
                                            <h3 class="text-h3">Add Team Member</h3>
                                            <v-btn icon @click="setDialog(false)" size="small" flat>
                                                <XIcon size="16" />
                                            </v-btn>
                                        </div>
                                    </v-card-text>
                                    <v-divider></v-divider>

                                    <v-card-text>
                                        <!-- <Form > -->
                                        <VForm v-model="valid" ref="formContainer" fast-fail lazy-validation
                                            @submit.prevent="save" class="py-1">
                                            <VRow class="mt-5 mb-3">

                                                <VCol cols="12" md="12">
                                                    <v-label class="font-weight-medium pb-2">Account Role</v-label>
                                                    <VSelect v-model="fields.accountRole" :items="getAccountRoles"
                                                        :rules="fieldRules.accountRole" label="Select"
                                                        :selected="'member'" item-title="description" item-value="name"
                                                        single-line variant="outlined" class="text-capitalize">
                                                    </VSelect>
                                                </VCol>

                                                <VCol cols="12" md="6">
                                                    <v-label class="text-subtitle-1 font-weight-medium pb-1">First
                                                        Name</v-label>
                                                    <VTextField v-model="fields.firstName" :rules="fieldRules.firstName"
                                                        required variant="outlined" label="First Name"
                                                        :color="fields.firstName.length > 2 ? 'success' : 'primary'">
                                                    </VTextField>
                                                </VCol>
                                                <VCol cols="12" md="6">
                                                    <v-label class="text-subtitle-1 font-weight-medium pb-1">Last
                                                        Name</v-label>
                                                    <VTextField v-model="fields.lastName" :rules="fieldRules.lastName"
                                                        required variant="outlined" label="Last Name"
                                                        :color="fields.lastName.length > 2 ? 'success' : 'primary'">
                                                    </VTextField>
                                                </VCol>
                                                <VCol cols="12" md="6">
                                                    <v-label class="text-subtitle-1 font-weight-medium pb-1">Email
                                                        Address</v-label>
                                                    <VTextField v-model="fields.emailAddress"
                                                        :rules="fieldRules.emailAddress" required variant="outlined"
                                                        label="Email Address"></VTextField>
                                                </VCol>
                                                <VCol cols="12" md="6">
                                                    <v-label class="text-subtitle-1 font-weight-medium pb-1">Phone
                                                        Number</v-label>
                                                    <VTextField type="number" v-model="fields.phoneNumber"
                                                        :rules="fieldRules.phoneNumber" required variant="outlined"
                                                        label="Phone Number"></VTextField>
                                                </VCol>
                                                <VCol cols="12" md="6">
                                                    <v-label
                                                        class="text-subtitle-1 font-weight-medium pb-2">Password</v-label>
                                                    <VTextField :type="showPassword ? 'text' : 'password'"
                                                        v-model="fields.password" :rules="fieldRules.password" required
                                                        variant="outlined" label="Password"
                                                        :append-inner-icon="showPassword ? 'mdi-eye' : 'mdi-eye-off'"
                                                        @click:append-inner="showPassword = !showPassword">
                                                    </VTextField>
                                                </VCol>
                                                <VCol cols="12" md="6">
                                                    <v-label class="text-subtitle-1 font-weight-medium pb-2">Confirm
                                                        Password</v-label>
                                                    <VTextField :type="showPassword ? 'text' : 'password'"
                                                        v-model="fields.confirmPassword"
                                                        :rules="fieldRules.confirmPassword" required variant="outlined"
                                                        label="Password"
                                                        :append-inner-icon="showPassword ? 'mdi-eye' : 'mdi-eye-off'"
                                                        @click:append-inner="showPassword = !showPassword">
                                                    </VTextField>
                                                </VCol>
                                                <VCol cols="12" lg="12" class="text-right">
                                                    <v-btn color="error" @click="dialog = false"
                                                        variant="text">Cancel</v-btn>

                                                    <v-btn color="primary" type="submit" :loading="loading"
                                                        :disabled="!valid" @click="save">
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
                    </template>

                    <template v-slot:prepend>
                        <v-sheet>
                            <v-dialog v-model="editDialog" max-width="800">
                                <v-card>
                                    <v-card-text>
                                        <div class="d-flex justify-space-between">
                                            <h3 class="text-h3">Edit Team Member</h3>
                                            <v-btn icon @click="setEditDialog(false)" size="small" flat>
                                                <XIcon size="16" />
                                            </v-btn>
                                        </div>
                                    </v-card-text>
                                    <v-divider></v-divider>

                                    <v-card-text>
                                        <!-- <Form > -->
                                        <VForm v-model="editValid" ref="formContainer" fast-fail lazy-validation
                                            @submit.prevent="update" class="py-1">
                                            <VRow class="mt-5 mb-3">

                                                <VCol cols="12" md="12">
                                                    <v-label class="font-weight-medium pb-2">Account Role</v-label>
                                                    <VSelect v-model="editFields.accountRole" :items="getAccountRoles"
                                                        :rules="fieldRules.accountRole" label="Select"
                                                        :selected="'member'" item-title="description" item-value="name"
                                                        single-line variant="outlined" class="text-capitalize">
                                                    </VSelect>
                                                </VCol>
                                                <VCol cols="12" md="6">
                                                    <v-label class="text-subtitle-1 font-weight-medium pb-1">First
                                                        Name</v-label>
                                                    <VTextField v-model="editFields.firstName"
                                                        :rules="fieldRules.firstName" required variant="outlined"
                                                        label="First Name"
                                                        :color="editFields.firstName.length > 2 ? 'success' : 'primary'">
                                                    </VTextField>
                                                </VCol>
                                                <VCol cols="12" md="6">
                                                    <v-label class="text-subtitle-1 font-weight-medium pb-1">Last
                                                        Name</v-label>
                                                    <VTextField v-model="editFields.lastName"
                                                        :rules="fieldRules.lastName" required variant="outlined"
                                                        label="Last Name"
                                                        :color="editFields.lastName.length > 2 ? 'success' : 'primary'">
                                                    </VTextField>
                                                </VCol>
                                                <VCol cols="12" md="6">
                                                    <v-label class="text-subtitle-1 font-weight-medium pb-1">Email
                                                        Address</v-label>
                                                    <VTextField v-model="editFields.emailAddress"
                                                        :rules="fieldRules.emailAddress" required variant="outlined"
                                                        label="Email Address"></VTextField>
                                                </VCol>
                                                <VCol cols="12" md="6">
                                                    <v-label class="text-subtitle-1 font-weight-medium pb-1">Phone
                                                        Number</v-label>
                                                    <VTextField type="number" v-model="editFields.phoneNumber"
                                                        :rules="fieldRules.phoneNumber" required variant="outlined"
                                                        label="Phone Number"></VTextField>
                                                </VCol>
                                                <VCol cols="12" lg="12" class="text-right">
                                                    <v-btn color="error" @click="setEditDialog(false)"
                                                        variant="text">Cancel</v-btn>

                                                    <v-btn color="primary" type="submit" :loading="loading"
                                                        :disabled="!editValid" @click="update">
                                                        <span v-if="!loading">
                                                            Update
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

                        <v-sheet>
                            <v-dialog v-model="viewDialog" max-width="800">
                                <v-card>
                                    <v-card-text>
                                        <div class="d-flex justify-space-between">
                                            <h3 class="text-h3">View Team Member</h3>
                                            <v-btn icon @click="setViewDialog(false)" size="small" flat>
                                                <XIcon size="16" />
                                            </v-btn>
                                        </div>
                                    </v-card-text>
                                    <v-divider></v-divider>

                                    <v-card-text>

                                        <VRow class="mt-5 mb-3">

                                            <!-- {{ selectedItem }} -->

                                            <VCol cols="12" lg="12">
                                                <v-list lines="one" v-if="selectedItem">

                                                    <v-list-item
                                                        :title="`Full Name : ${selectedItem?.firstName} ${selectedItem?.lastName}`"></v-list-item>
                                                    <v-list-item
                                                        :title="`Email Address : ${selectedItem?.email}`"></v-list-item>
                                                    <v-list-item
                                                        :title="`Phone Number : ${selectedItem?.phone}`"></v-list-item>
                                                    <!-- <v-list-item
                                                        :title="`Full Name : ${selectedItem?.firstName} ${selectedItem?.lastName}`"></v-list-item>
                                                    <v-list-item
                                                        :title="`Full Name : ${selectedItem?.firstName} ${selectedItem?.lastName}`"></v-list-item> -->
                                                </v-list>
                                            </VCol>

                                            <VCol cols="12" lg="12" class="text-right">
                                                <v-btn color="primary" class="mr-3" @click="setViewDialog(false)">Remove
                                                    From
                                                    Organization</v-btn>
                                                <v-btn color="primary" class="mr-3" @click="setViewDialog(false)">Verify
                                                    Email</v-btn>
                                                <v-btn color="primary" class="mr-3" @click="setViewDialog(false)">Send a
                                                    message</v-btn>

                                            </VCol>
                                            <VCol cols="12" lg="12" class="text-right">
                                                <v-btn color="primary" @click="setViewDialog(false)"
                                                    variant="text">Close</v-btn>
                                            </VCol>
                                        </VRow>

                                    </v-card-text>
                                </v-card>
                            </v-dialog>

                        </v-sheet>

                        <v-sheet>
                            <v-dialog v-model="deleteDialog" max-width="400">
                                <v-card>
                                    <v-card-text>
                                        <div class="d-flex justify-space-between">
                                            <h3 class="text-h3">Remove Team Member</h3>
                                            <v-btn icon @click="setDeleteDialog(false)" size="small" flat>
                                                <XIcon size="16" />
                                            </v-btn>
                                        </div>
                                    </v-card-text>
                                    <v-divider></v-divider>

                                    <v-card-text>

                                        <VRow class="mt-1 mb-3">


                                            <VCol cols="12" lg="12">
                                                <v-card text="If you go this you will have to invite the user to revert"
                                                    title="Do you want to remove user from organization?"></v-card>
                                            </VCol>

                                            <VCol cols="12" lg="12" class="text-right">

                                                <v-btn color="primary" class="mr-3" :loading="loading"
                                                    :disabled="loading" @click="removeUser(selectedItem)">
                                                    <span v-if="!loading">
                                                        Yes
                                                    </span>
                                                    <clip-loader v-else :loading="loading" color="white"
                                                        :size="'25px'"></clip-loader>
                                                </v-btn>

                                                <v-btn color="error" class="mr-3" :disabled="loading"
                                                    @click="setDeleteDialog(false)">No</v-btn>

                                            </VCol>
                                        </VRow>

                                    </v-card-text>
                                </v-card>
                            </v-dialog>

                        </v-sheet>
                    </template>



                    <template v-slot:text>
                        <v-text-field v-model="search" label="Search" prepend-inner-icon="mdi-magnify"
                            variant="outlined" hide-details single-line></v-text-field>
                    </template>

                    <VDataTable :headers="headers" :items="getTeamMembers" :search="search" item-key="name"
                        items-per-page="5" item-value="fat" show-select>
                        <template v-slot:item.action="{ item }">
                            <div v-if="isLoggedInUserOwnsActionOrg">
                                <v-btn size="small" @click="selectItem(item, 'view')">
                                    <v-icon class="" size="small">
                                        mdi-eye
                                    </v-icon>
                                </v-btn>
                                <v-btn size="small" @click="selectItem(item, 'edit')">
                                    <v-icon class="" size="small">
                                        mdi-pencil
                                    </v-icon>
                                </v-btn>
                                <v-btn size="small" @click="selectItem(item, 'delete')">
                                    <v-icon class="" size="small">
                                        mdi-delete
                                    </v-icon>
                                </v-btn>
                            </div>
                        </template>

                        <template v-slot:item.sn="{ index }">
                            {{ ++index }}
                        </template>


                        <!-- <template v-slot:items="{ column }">
                            {{ column.toUpperCase() }}
                        </template> -->
                    </VDataTable>

                </v-card>

            </v-col>
        </v-row>
    </div>
</template>
