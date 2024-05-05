<script setup lang="ts">
import { ref, onMounted, computed, nextTick, watch } from 'vue';
import BaseBreadcrumb from '@/components/shared/BaseBreadcrumb.vue';
import { VDataTable } from 'vuetify/labs/VDataTable'
import { useOrganizationStore } from '@/stores/organizationStore';
import { useTeamMemberStore } from '@/stores/teamMemberStore';
import { useAuthStore } from '@/stores/auth';
import { useOpenLinksStore } from '@/stores/openLinks';
import { useActionStore } from '@/stores/actionStore';
import moment from 'moment'
import { router } from '@/router';

const page = ref({ title: 'Actions' });
const breadcrumbs = ref([
    {
        text: 'Dashboard',
        disabled: false,
        href: 'dashboard'
    },
    {
        text: 'Actions',
        disabled: true,
        href: '#'
    }
]);


const authStore = useAuthStore();
const organizationStore = useOrganizationStore();
const teamMemberStore = useTeamMemberStore();
const openLinks = useOpenLinksStore();
const actionStore = useActionStore();

onMounted(() => {
    teamMemberStore.getTeamMembersExcept();
    openLinks.getPriorities();
    actionStore.getActions();
});


const computedIndex = (index : any) => ++index;

const getActions : any   = computed(() => (actionStore.actions));
const getTeamMembersExceptMe : any   = computed(() => (teamMemberStore.membersExceptMe));
const getActiveOrg : any   = computed(() => (organizationStore.getActiveOrg()));
const getAuthUser : any   = computed(() => (authStore.loggedUser));
const getObservationTypes : any   = computed(() => (openLinks.observationTypes));
const getPriorities : any   = computed(() => (openLinks.priorities));
const isLoggedInUserOwnsActionOrg : any   = computed(() => (getAuthUser.value?.id == getActiveOrg.value?.user_id));


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
    if (value == false) {
        selectItem({})
        setSubViewDialog(false)
    }
}
watch(viewDialog, () => {
    if (viewDialog.value == false) selectItem({})
})
const subViewDialog = ref(false);
const setSubViewDialog = (value: boolean, status: string = '') => {
    subViewDialog.value = value;
    sFields.value.status = status
}

const deleteDialog = ref(false);
const setDeleteDialog = (value: boolean) => {
    deleteDialog.value = value;
    if (value == false) selectItem({})
}
const selectedItem = ref({} as any);
const selectItem = (item: any, action: string = '') => {
    selectedItem.value = Object.assign({}, item.raw);
 
    switch (action) {
        case 'view':
            setViewDialog(true)
            break;
        case 'edit':
            editFields.value.actionId = selectedItem.value?.uuid
            editFields.value.title = selectedItem.value?.title
            editFields.value.description = selectedItem.value?.description
            // editFields.value.assigneeId = selectedItem.value?.assignee_id
            editFields.value.priorityId = selectedItem.value?.priority_id
            editFields.value.end_date = selectedItem.value?.end_datetime
            editFields.value.start_date = selectedItem.value?.start_datetime
            
            setEditDialog(true)
            break;
        case 'delete':
            setDeleteDialog(true)
            break;
        default:
            break;
    }
    
}

const isAssignee : any  = computed(() => (selectedItem?.value?.assignee_id == getAuthUser?.value?.id));



const search = ref('');

const headers = ref([
    {
        key: 'sn',
        title: 'SN',
        sortable: false,
    },
    {
        key: 'title',
        title: 'Title',
        sortable: false,
        value: (item: any): string => `${item.title} `,
    },
    {
        key: 'description',
        title: 'Description',
        value: (item: any): string => `${item.description}`
    },
    {
        key: 'assignee_id',
        title: 'Assignee',
        value: (item: any): string => `${item.assignee.lastName} ${item.assignee.firstName}`
    },
    {
        key: 'status',
        title: 'Status',
        value: (item: any): string => `${item.status}`
    },
    {
        key: 'priority_id',
        title: 'Priority',
        value: (item: any): string => `${item.priority.description}`
    },
    {
        key: 'created_at',
        title: 'Date Created',
        value: (item: any): string => `${moment(item.created_at).format('MMMM Do YYYY, h:mm a')}`
    },
    {
        key: 'action',
        sortable: false,
        // align: 'end',
        title: 'Action',
    },
]);


const sFields = ref({
    status: "",
    message: "",
});

const fields = ref({
    title: "",
    description: "",
    assigneeId: "",
    priorityId: "",
    end_date: "",
    start_date: "",
    organization_id: getActiveOrg.value?.uuid,
    
});

const editFields = ref({
    actionId: "",
    title: "",
    description: "",
    priorityId: "",
    end_date: "",
    start_date: "",
    organization_id: getActiveOrg.value?.uuid,
    
});

const fieldRules : any = ref({
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


const save = async (e: any) => {
    e.preventDefault();

    try {
        setLoading(true)

        const values = { ...fields.value }

        let objectValues = {
            "organization_id": values?.organization_id,
            "title": values?.title,
            "description": values?.description,
            "assignee_id": values?.assigneeId,
            "start_date": moment(values?.start_date).format('YYYY-MM-DD HH:mm:ss'),
            "end_date":  moment(values?.end_date).format('YYYY-MM-DD HH:mm:ss'),  
            "priority_id": values?.priorityId
        }

        const resp = await actionStore.addAction(objectValues)
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

            fields.value.title = "";
            fields.value.description = "";
            fields.value.assigneeId = "";
            fields.value.priorityId = "";
            fields.value.end_date = "";
            fields.value.start_date = "";

            actionStore.getActions();
            

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
            "organization_id": values?.organization_id,
            "title": values?.title,
            "description": values?.description,
            "action_id": values?.actionId,
            "start_date": moment(values?.start_date).format('YYYY-MM-DD HH:mm:ss'),
            "end_date":  moment(values?.end_date).format('YYYY-MM-DD HH:mm:ss'),  
            "priority_id": values?.priorityId
        }

        const resp = await actionStore.editAction(objectValues)
            .catch((error: any) => {
                throw error
            })
            .then((resp: any) => {
                return resp
            });

        if (resp?.message == 'success') {
            setLoading(false)
            setEditDialog(false)

            actionStore.getActions();

            editFields.value.title = "";
            editFields.value.description = "";
            editFields.value.actionId = "";
            editFields.value.priorityId = "";
            editFields.value.end_date = "";
            editFields.value.start_date = "";
        }

        setLoading(false)
        setEditDialog(false)



    } catch (error) {
        console.log(error)
        setLoading(false)
        setEditDialog(false)
    }

}

const removeAction = async (action: any) => {

    try {
        setLoading(true)

        let objectValues = {
            "action_id": action?.uuid,
        }

        const resp = await actionStore.deleteAction(objectValues)
            .catch((error: any) => {
                throw error
            })
            .then((resp: any) => {
                return resp
            });

        if (resp?.message == 'success') {
            setLoading(false)
            setDeleteDialog(false)

            actionStore.getActions();
        }

        setLoading(false)
        setDeleteDialog(false)



    } catch (error) {
        console.log(error)
        setLoading(false)
        setDeleteDialog(false)
    }

}

const statusUpdate = async (action: any) => {

    try {
        setLoading(true)

        const values = { ...sFields.value }

        let objectValues = {
            "action_id": action?.uuid,
            "status": values?.status,
            "message": values?.message,
        }

        const resp = await actionStore.statusUpdate(objectValues)
            .catch((error: any) => {
                throw error
            })
            .then((resp: any) => {
                return resp
            });

        if (resp?.message == 'success') {
            setLoading(false)
            setSubViewDialog(false)
            setViewDialog(false)
            sFields.value.status = ''
            sFields.value.message = ''
            actionStore.getActions();
        }

        setLoading(false)
        setSubViewDialog(false)
        setViewDialog(false)



    } catch (error) {
        console.log(error)
        setLoading(false)
        setSubViewDialog(false)
        setViewDialog(false)
    }

}


const files = ref([])
const images = ref([])
const previewImage = ref([] as any)

const selectImage = (image: any) => {

    images.value = image.target.files;
    previewImage.value = [];

    for (let index = 0; index < images.value.length; index++) {
        const element = images.value[index];
        previewImage.value.push(URL.createObjectURL(element))
    }

}

</script>

<template>
    <div>
        <BaseBreadcrumb :title="page.title" :breadcrumbs="breadcrumbs"></BaseBreadcrumb>
        <v-row>
            <v-col cols="12" md="12">

                <v-card :title="`Actions`" flat>


                    <template v-slot:append>
                        <v-sheet v-if="isLoggedInUserOwnsActionOrg">
                            <v-btn color="primary" class="mr-2" @click="setDialog(true)">Add Action</v-btn>

                            <v-dialog v-model="dialog" max-width="600">
                                <v-card>
                                    <v-card-text>
                                        <div class="d-flex justify-space-between">
                                            <h3 class="text-h3">Add Action</h3>
                                            <v-btn icon @click="setDialog(false)" size="small" flat>
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
                                                    <v-label
                                                        class="text-subtitle-1 font-weight-medium pb-1">Title</v-label>
                                                    <VTextField type="text" v-model="fields.title"
                                                        :rules="fieldRules.title" required variant="outlined"
                                                        label="Title"
                                                        :color="fields.title.length > 2 ? 'success' : 'primary'">
                                                    </VTextField>
                                                </VCol>
                                                <VCol cols="12" md="12">
                                                    <v-label
                                                        class="text-subtitle-1 font-weight-medium pb-1">Description</v-label>
                                                    <VTextarea variant="outlined" outlined name="Description"
                                                        label="Description" v-model="fields.description"
                                                        :rules="fieldRules.description" required
                                                        :color="fields.description.length > 10 ? 'success' : 'primary'">
                                                    </VTextarea>
                                                </VCol>
                                                <VCol cols="12" md="12">
                                                    <v-label class="font-weight-medium pb-1">User</v-label>
                                                    <VSelect v-model="fields.assigneeId" :items="getTeamMembersExceptMe"
                                                        :rules="fieldRules.assigneeId" label="Select" :selected="''"
                                                        item-title='lastName' item-value="uuid" single-line
                                                        variant="outlined" class="text-capitalize">

                                                        <!-- <template v-slot:selection="{ item }">

                                                        <span>
                                                            {{ `${item.lastName} ${item.firstName}` }}
                                                        </span>
                                                        </template> -->
                                                    </VSelect>
                                                </VCol>
                                                <VCol cols="12" md="12">
                                                    <v-label class="font-weight-medium pb-1">Severity</v-label>
                                                    <VSelect v-model="fields.priorityId" :items="getPriorities"
                                                        :rules="fieldRules.priorityId" label="Select" :selected="''"
                                                        item-title="description" item-value="id" single-line
                                                        variant="outlined" class="text-capitalize">
                                                    </VSelect>
                                                </VCol>

                                                <VCol cols="12" md="6">
                                                    <v-label class="text-subtitle-1 font-weight-medium pb-1">Start
                                                        Date</v-label>
                                                    <VueDatePicker input-class-name="dp-custom-input"
                                                        v-model="fields.start_date" :min-date="new Date()" required>
                                                    </VueDatePicker>
                                                </VCol>
                                                <VCol cols="12" md="6">
                                                    <v-label class="text-subtitle-1 font-weight-medium pb-1">End
                                                        Date</v-label>
                                                    <VueDatePicker input-class-name="dp-custom-input"
                                                        :disabled='!fields.start_date' v-model="fields.end_date"
                                                        :min-date="fields.start_date ? new Date(fields.start_date) : new Date()"
                                                        required></VueDatePicker>
                                                </VCol>



                                                <VCol cols="12" lg="12" class="text-right">
                                                    <v-btn color="error" @click="setDialog(false)"
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
                                            <h3 class="text-h3">Edit Action</h3>
                                            <v-btn icon @click="setEditDialog(false)" size="small" flat>
                                                <XIcon size="16" />
                                            </v-btn>
                                        </div>
                                    </v-card-text>
                                    <v-divider></v-divider>

                                    <v-card-text>
                                        <VForm v-model="valid" ref="formContainer" fast-fail lazy-validation
                                            @submit.prevent="update" class="py-1">
                                            <VRow class="mt-5 mb-3">

                                                <VCol cols="12" md="12">
                                                    <v-label
                                                        class="text-subtitle-1 font-weight-medium pb-1">Title</v-label>
                                                    <VTextField type="text" v-model="editFields.title"
                                                        :rules="fieldRules.title" required variant="outlined"
                                                        label="Title"
                                                        :color="editFields.title.length > 2 ? 'success' : 'primary'">
                                                    </VTextField>
                                                </VCol>
                                                <VCol cols="12" md="12">
                                                    <v-label
                                                        class="text-subtitle-1 font-weight-medium pb-1">Description</v-label>
                                                    <VTextarea variant="outlined" outlined name="Description"
                                                        label="Description" v-model="editFields.description"
                                                        :rules="fieldRules.description" required
                                                        :color="editFields.description.length > 10 ? 'success' : 'primary'">
                                                    </VTextarea>
                                                </VCol>
                                                <VCol cols="12" md="12">
                                                    <v-label class="font-weight-medium pb-1">Severity</v-label>
                                                    <VSelect v-model="editFields.priorityId" :items="getPriorities"
                                                        :rules="fieldRules.priorityId" label="Select" :selected="''"
                                                        item-title="description" item-value="id" single-line
                                                        variant="outlined" class="text-capitalize">
                                                    </VSelect>
                                                </VCol>

                                                <VCol cols="12" md="6">
                                                    <v-label class="text-subtitle-1 font-weight-medium pb-1">Start
                                                        Date</v-label>
                                                    <VueDatePicker input-class-name="dp-custom-input"
                                                        v-model="editFields.start_date" :min-date="new Date()" required>
                                                    </VueDatePicker>
                                                </VCol>
                                                <VCol cols="12" md="6">
                                                    <v-label class="text-subtitle-1 font-weight-medium pb-1">End
                                                        Date</v-label>
                                                    <VueDatePicker input-class-name="dp-custom-input"
                                                        :disabled='!editFields.start_date' v-model="editFields.end_date"
                                                        :min-date="editFields.start_date ? new Date(editFields.start_date) : new Date()"
                                                        required></VueDatePicker>
                                                </VCol>


                                                <VCol cols="12" lg="12" class="text-right">
                                                    <v-btn color="error" @click="setEditDialog(false)"
                                                        variant="text">Cancel</v-btn>

                                                    <v-btn color="primary" type="submit" :loading="loading"
                                                        :disabled="!valid" @click="update">
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

                        <v-sheet>
                            <v-dialog v-model="viewDialog" max-width="800">
                                <v-card>
                                    <v-card-text>
                                        <div class="d-flex justify-space-between">
                                            <h3 class="text-h3">View Action</h3>
                                            <v-btn icon @click="setViewDialog(false)" size="small" flat>
                                                <XIcon size="16" />
                                            </v-btn>
                                        </div>
                                    </v-card-text>
                                    <v-divider></v-divider>

                                    <v-card-text>

                                        <VRow class="mt-5 mb-3">

                                            <!-- {{ selectedItem }} -->

                                            <VCol cols="12" lg="12" v-if="!subViewDialog">

                                                <v-table v-if="selectedItem">
                                                    <thead>
                                                        <tr>
                                                            <th class="text-left">

                                                            </th>
                                                            <th class="text-left">

                                                            </th>
                                                            <th class="text-left">

                                                            </th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>

                                                        <template v-if="selectedItem">
                                                            <tr>
                                                                <td></td>
                                                                <td>Title </td>
                                                                <td>{{ `${selectedItem?.title}`
                                                                    }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td></td>
                                                                <td>Description </td>
                                                                <td>{{ `${selectedItem?.description}` }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td></td>
                                                                <td>Assignee </td>
                                                                <td>{{ `${selectedItem?.assignee?.firstName}
                                                                    ${selectedItem?.assignee?.lastName}` }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td></td>
                                                                <td>Assignor </td>
                                                                <td>{{ `${selectedItem?.creator?.firstName}
                                                                    ${selectedItem?.creator?.lastName}` }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td></td>
                                                                <td>Status </td>
                                                                <td>{{ `${selectedItem?.status}` }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td></td>
                                                                <td>Start Date time </td>
                                                                <td>{{ `${selectedItem?.start_datetime}` }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td></td>
                                                                <td>End Date time </td>
                                                                <td>{{ `${selectedItem?.end_datetime}` }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td></td>
                                                                <td>Priority </td>
                                                                <td>{{ `${selectedItem?.priority?.description}` }}</td>
                                                            </tr>
                                                            <tr v-if="selectedItem?.statusMessage">
                                                                <td></td>
                                                                <td>Status Reason </td>
                                                                <td>{{ `${selectedItem?.statusMessage}` }}</td>
                                                            </tr>
                                                        </template>
                                                    </tbody>
                                                </v-table>
                                            </VCol>
                                            <VCol cols="12" lg="12" v-else>

                                                <VForm v-model="valid" fast-fail lazy-validation
                                                    @submit.prevent="statusUpdate(selectedItem)" class="py-1">
                                                    <VRow class="mt-5 mb-3">

                                                        <VCol cols="12" md="12"
                                                            v-if="selectedItem?.is_pending && selectedItem?.can_accept">
                                                            <v-label class="font-weight-medium pb-1">What do you
                                                                what?</v-label>
                                                            <VSelect v-model="sFields.status"
                                                                :items="[{ 'name': 'accepted', 'description': 'Accept Action' }, { 'name': 'rejected', 'description': 'Reject Action' }]"
                                                                label="Select" single-line variant="outlined"
                                                                class="text-capitalize"
                                                                :rules="[(v: any) => !!v || 'You must select to continue!']"
                                                                item-title='description' item-value="name" required>
                                                            </VSelect>
                                                        </VCol>
                                                        <VCol cols="12" md="12"
                                                            v-if="selectedItem?.is_accepted && selectedItem?.can_start">
                                                            <v-label class="font-weight-medium pb-1">What do you
                                                                what?</v-label>
                                                            <VSelect v-model="sFields.status"
                                                                :items="[{ 'name': 'ongoing', 'description': 'Start Action' }, { 'name': 'canceled', 'description': 'Cancel Action' }]"
                                                                label="Select" single-line variant="outlined"
                                                                class="text-capitalize"
                                                                :rules="[(v: any) => !!v || 'You must select to continue!']"
                                                                item-title='description' item-value="name" required>
                                                            </VSelect>
                                                        </VCol>
                                                        <VCol cols="12" md="12">
                                                            <v-label
                                                                class="text-subtitle-1 font-weight-medium pb-1">Status
                                                                Information</v-label>
                                                            <VTextarea variant="outlined" outlined name="Reason"
                                                                label="Reason" v-model="sFields.message" required
                                                                :color="sFields.message.length > 10 ? 'success' : 'primary'">
                                                            </VTextarea>
                                                        </VCol>


                                                        <VCol cols="12" md="12" v-if="selectedItem?.is_ongoing">
                                                            <v-label class="font-weight-medium pb-1">Do you have
                                                                picture?</v-label>

                                                            <v-file-input v-model="files" :show-size="1000"
                                                                color="deep-purple-accent-4" label="File input"
                                                                placeholder="Select your files"
                                                                prepend-icon="mdi-paperclip" variant="outlined" counter
                                                                multiple accept="image/*" @change="selectImage">
                                                                <template v-slot:selection="{ fileNames }">
                                                                    <template v-for="(fileName, index) in fileNames"
                                                                        :key="fileName">
                                                                        <v-chip v-if="index < 2" class="me-2"
                                                                            color="deep-purple-accent-4" size="small"
                                                                            label>
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
                                                                    <VCol cols="4" v-for="image in previewImage"
                                                                        :key="image">

                                                                        <div>
                                                                            <img class="preview my-3" :src="image"
                                                                                alt="" style="max-width: 200px;" />
                                                                        </div>
                                                                    </VCol>
                                                                </VRow>
                                                            </div>
                                                        </VCol>


                                                        <VCol cols="12" lg="12" class="text-right">
                                                            <v-btn color="error" @click="setSubViewDialog(false)"
                                                                variant="text">Cancel</v-btn>

                                                            <v-btn color="primary" type="submit" :loading="loading"
                                                                :disabled="!valid">
                                                                <span v-if="!loading">
                                                                    Submit
                                                                </span>
                                                                <clip-loader v-else :loading="loading" color="white"
                                                                    :size="'25px'"></clip-loader>
                                                            </v-btn>

                                                        </VCol>
                                                    </VRow>
                                                </VForm>

                                            </VCol>

                                            <VCol cols="12" lg="12" class="text-right"
                                                v-if="!subViewDialog && isAssignee">
                                                <span v-if="selectedItem?.is_pending && selectedItem?.can_accept">
                                                    <v-btn color="primary" class="mr-3"
                                                        @click="setSubViewDialog(true, 'accepted')">Accept
                                                        Action</v-btn>
                                                    <v-btn color="error" class="mr-3"
                                                        @click="setSubViewDialog(true, 'rejected')">Reject
                                                        Action</v-btn>
                                                </span>
                                                <span
                                                    v-if="selectedItem?.is_accepted && selectedItem?.can_start && !selectedItem?.is_ended">
                                                    <v-btn color="primary" class="mr-3"
                                                        @click="setSubViewDialog(true, 'ongoing')">Start Action</v-btn>
                                                    <v-btn color="error" class="mr-3"
                                                        @click="setSubViewDialog(true, 'canceled')">Cancel
                                                        Action</v-btn>
                                                </span>
                                                <span v-if="selectedItem?.is_ongoing">
                                                    <v-btn color="success" class="mr-3"
                                                        @click="setSubViewDialog(true, 'completed')">Action
                                                        Completed</v-btn>
                                                </span>


                                                <!-- <v-btn color="primary" class="mr-3" @click="setViewDialog(false)">Verify</v-btn> -->

                                            </VCol>
                                            <VCol cols="12" lg="12" class="text-right" v-if="!subViewDialog">

                                                <v-btn color="primary" @click="setViewDialog(false)"
                                                    variant="text">Close</v-btn>

                                            </VCol>
                                        </VRow>

                                    </v-card-text>
                                </v-card>
                            </v-dialog>

                        </v-sheet>

                        <v-sheet>
                            <v-dialog v-model="deleteDialog" max-width="500">
                                <v-card>
                                    <v-card-text>
                                        <div class="d-flex justify-space-between">
                                            <h3 class="text-h3">Delete Action</h3>
                                            <v-btn icon @click="setDeleteDialog(false)" size="small" flat>
                                                <XIcon size="16" />
                                            </v-btn>
                                        </div>
                                    </v-card-text>
                                    <v-divider></v-divider>

                                    <v-card-text>

                                        <VRow class="mt-1 mb-3">


                                            <VCol cols="12" lg="12">
                                                <v-card text="You can't revert this action"
                                                    title="Do you want to delete this action?"></v-card>
                                            </VCol>

                                            <VCol cols="12" lg="12" class="text-right">

                                                <v-btn color="primary" class="mr-3" :loading="loading"
                                                    :disabled="loading" @click="removeAction(selectedItem)">
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

                    <VDataTable :headers="headers" :items="getActions" :search="search" item-key="name"
                        items-per-page="5" item-value="fat" show-select>
                        <template v-slot:item.action="{ item }">

                            <v-menu>
                                <template v-slot:activator="{ props }">
                                    <v-btn color="primary" dark v-bind="props" flat> Action </v-btn>
                                </template>

                                <v-list>
                                    <v-list-item @click="selectItem(item, 'view')">
                                        <v-list-item-title>
                                            <v-icon class="mr-2" size="small">
                                                mdi-eye
                                            </v-icon>
                                            View Action
                                        </v-list-item-title>
                                    </v-list-item>
                                    <template v-if="isLoggedInUserOwnsActionOrg && item.selectable.is_pending">
                                        <v-list-item @click="selectItem(item, 'edit')">
                                            <v-list-item-title>
                                                <v-icon class="mr-2" size="small">
                                                    mdi-pencil
                                                </v-icon>
                                                Edit Action
                                            </v-list-item-title>
                                        </v-list-item>
                                        <v-list-item @click="selectItem(item, 'delete')">
                                            <v-list-item-title>
                                                <v-icon class="mr-2" size="small">
                                                    mdi-delete
                                                </v-icon>
                                                Delete Action
                                            </v-list-item-title>
                                        </v-list-item>
                                    </template>
                                </v-list>
                            </v-menu>
                        </template>

                        <template v-slot:item.sn="{ index }">
                            {{ computedIndex(index) }}
                        </template>


                        <template v-slot:item.status="{ item }">
                            <div class="">
                                <v-chip :color="item.selectable.status=='invite' ? 'green' : 'orange'"
                                    :text="item.selectable.status" class="text-uppercase" size="small" label></v-chip>
                            </div>
                        </template>

                        <template v-slot:item.priority_id="{ item }">
                            <div class="">
                                <v-chip :color="item.selectable.priority.description=='invite' ? 'green' : 'orange'"
                                    :text="item.selectable.priority.description" class="text-uppercase" size="small"
                                    label></v-chip>
                            </div>
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
