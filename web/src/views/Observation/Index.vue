<script setup lang="ts">
import { ref, onMounted, computed, nextTick } from 'vue';
import BaseBreadcrumb from '@/components/shared/BaseBreadcrumb.vue';
import { VDataTable } from 'vuetify/labs/VDataTable'
import { useOrganizationStore } from '@/stores/organizationStore';
import { useTeamMemberStore } from '@/stores/teamMemberStore';
import { useAuthStore } from '@/stores/auth';
import { useOpenLinksStore } from '@/stores/openLinks';
import { useObservationStore } from '@/stores/observationStore';
import moment from 'moment'
import { router } from '@/router';


const files = ref([])
const page = ref({ title: 'Observations' });
const breadcrumbs = ref([ph
    {
        text: 'Dashboard',
        disabled: false,
        href: 'dashboard'
    },
    {
        text: 'Observations',
        disabled: true,
        href: '#'
    }
]);


const authStore = useAuthStore();
const organizationStore = useOrganizationStore();
const teamMemberStore = useTeamMemberStore();
const openLinks = useOpenLinksStore();
const observationStore = useObservationStore();

onMounted(() => {
    observationStore.getObservations();
    teamMemberStore.getTeamMembers();
    openLinks.getPriorities();
    openLinks.getObservationTypes();
});

const getObservations = computed(() => (observationStore.observations));
const getTeamMembers = computed(() => (teamMemberStore.members));
const getActiveOrg = computed(() => (organizationStore.getActiveOrg()));
const getAuthUser = computed(() => (authStore.loggedUser));
const getObservationTypes = computed(() => (openLinks.observationTypes));
const getPriorities = computed(() => (openLinks.priorities));
const isLoggedInUserOwnsActionOrg = computed(() => (getAuthUser.value?.id == getActiveOrg.value?.user_id));


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
            editFields.value.observationId = selectedItem.value?.uuid
            editFields.value.observationTypeId = selectedItem.value?.observation_type_id
            editFields.value.priorityId = selectedItem.value?.priority_id
            editFields.value.affectedWorkers = selectedItem.value?.affected_workers
            editFields.value.date_time = selectedItem.value?.incident_datetime
            editFields.value.description = selectedItem.value?.description
            editFields.value.address = selectedItem.value?.address
            editFields.value.location_detail = selectedItem.value?.location_details
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
        title: 'Observer',
        sortable: false,
        value: (item: any) => `${item.observer.lastName}  ${item.observer.firstName}`,
    },
    {
        key: 'description',
        title: 'Description',
        value: (item: any) => `${item.description}`
    },
    {
        key: 'observation_type_id',
        title: 'Observation Type',
        value: (item: any) => `${item.observation_type.description}`
    },
    {
        key: 'status',
        title: 'Status',
        value: (item: any) => `${item.status}`
    },
    {
        key: 'incident_datetime',
        title: 'Incident Date/Time',
        value: (item: any) => `${moment(item.incident_datetime).format('MMMM Do YYYY, h:mm:ss a')}`
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


const fields = ref({
    observationTypeId: "",
    priorityId: "",
    affectedWorkers: "",
    date_time: "",
    description: "",
    address: "",
    location_detail: "",
    sendToOrg: "No",
    organization_id: getActiveOrg.value.uuid,
    
});

const editFields = ref({
    observationId: "",
    observationTypeId: "",
    priorityId: "",
    affectedWorkers: "",
    date_time: "",
    description: "",
    address: "",
    location_detail: "",
    organization_id: getActiveOrg.value.uuid,
    
});

const fieldRules : any = ref({
    observationTypeId: [
        (v: string) => !!v || 'Observation Type is required',
    ],
    priorityId: [
        (v: string) => !!v || 'Severity is required',
    ],
    sendToOrg: [
        (v: string) => !!v || 'Affected is required',
    ],
    affectedWorkers: [
        (v: string) => !!v || 'Affected is required',
    ],
    date_time: [
        (v: string) => !!v || 'Date Time  is required',
    ],
    description: [
        (v: string) => !!v || 'Description is required',
        (v: string) => v.length > 10 || 'More than ten letters required'
    ],
    address: [
        (v: string) => !!v || 'First Name is required',
        (v: string) => v.length > 10 || 'More than ten letters required'
    ],
    location_detail: [
        (v: string) => !!v || 'First Name is required',
        (v: string) => v.length > 10 || 'More than ten letters required'
    ],
})


const save = async (e: any) => {
    e.preventDefault();

    try {
        setLoading(true)

        const values = { ...fields.value }
        
        let objectValues = {
            "observation_type": values?.observationTypeId,
            "description": values?.description,
            "location_details": values?.location_detail,
            "address": values?.address,
            "affected_workers": values?.affectedWorkers,
            "organization_id": values.sendToOrg == 'No' ? null :values?.organization_id,
            "date_time": moment(fields.value.date_time).format('YYYY-MM-DD hh:mm:ss'),
            "severity": values?.priorityId
        }

        
        const resp = await observationStore.addObservation(objectValues)
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
            observationStore.getObservations();
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
            "observation_type": values?.observationTypeId,
            "observation_id": values?.observationId,
            "description": values?.description,
            "location_details": values?.location_detail,
            "address": values?.address,
            "affected_workers": values?.affectedWorkers,
            "date_time": moment(editFields.value.date_time).format('YYYY-MM-DD hh:mm:ss'),
            "severity": values?.priorityId
        }

        const resp = await observationStore.editObservation(objectValues)
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
            observationStore.getObservations();
        }

        setLoading(false)
        setEditDialog(false)



    } catch (error) {
        console.log(error)
        setLoading(false)
        setEditDialog(false)
    }

}

const removeObservation = async (observation: any) => {

    try {
        setLoading(true)

        let objectValues = {
            "observation_id": observation?.uuid,
        }

        const resp = await observationStore.removeObservation(objectValues)
            .catch((error: any) => {
                throw error
            })
            .then((resp: any) => {
                return resp
            });

        if (resp?.message == 'success') {
            setLoading(false)
            setDeleteDialog(false)

            observationStore.getObservations();
        }

        setLoading(false)
        setDeleteDialog(false)



    } catch (error) {
        console.log(error)
        setLoading(false)
        setDeleteDialog(false)
    }

}


const datetime = ref('')

</script>

<template>
    <div>
        <BaseBreadcrumb :title="page.title" :breadcrumbs="breadcrumbs"></BaseBreadcrumb>
        <v-row>
            <v-col cols="12" md="12">

                <v-card :title="`Observations`" flat>


                    <template v-slot:append>
                        <v-sheet>
                            <v-btn color="primary" class="mr-2" @click="setDialog(true)">Add Observation</v-btn>

                            <v-dialog v-model="dialog" max-width="800">
                                <v-card>
                                    <v-card-text>
                                        <div class="d-flex justify-space-between">
                                            <h3 class="text-h3">Add Observation</h3>
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

                                                <VCol cols="12" md="6">
                                                    <v-label class="font-weight-medium pb-1">Observation Type</v-label>
                                                    <VSelect v-model="fields.observationTypeId" :items="getObservationTypes"
                                                        :rules="fieldRules.observationTypeId" label="Select"
                                                        :selected="''" item-title="description" item-value="id"
                                                        single-line variant="outlined" class="text-capitalize">
                                                    </VSelect>
                                                </VCol>
                                                <VCol cols="12" md="6">
                                                    <v-label class="font-weight-medium pb-1">Severity</v-label>
                                                    <VSelect v-model="fields.priorityId" :items="getPriorities"
                                                        :rules="fieldRules.priorityId" label="Select"
                                                        :selected="''" item-title="description" item-value="id"
                                                        single-line variant="outlined" class="text-capitalize">
                                                    </VSelect>
                                                </VCol>

                                                <VCol cols="12" md="6">
                                                    <v-label class="text-subtitle-1 font-weight-medium pb-1">Number of affected worker(s)</v-label>
                                                    <VTextField type="number" v-model="fields.affectedWorkers" :rules="fieldRules.affectedWorkers"
                                                        required variant="outlined" label="Affected Workers"
                                                        :color="fields.affectedWorkers.length > 2 ? 'success' : 'primary'">
                                                    </VTextField>
                                                </VCol>
                                                <VCol cols="12" md="6">
                                                    <v-label class="font-weight-medium pb-1">Send to Organization?</v-label>
                                                    <VSelect v-model="fields.sendToOrg" :items="['Yes', 'No']"
                                                        :rules="fieldRules.sendToOrg" label="Select"
                                                        :selected="''"
                                                        single-line variant="outlined" class="text-capitalize">
                                                    </VSelect>
                                                </VCol>
                                                <VCol cols="12" md="12">
                                                    <v-label class="text-subtitle-1 font-weight-medium pb-1">When did it happen?</v-label>
                                                    <VueDatePicker input-class-name="dp-custom-input"  v-model="fields.date_time"  :max-date="new Date()" required></VueDatePicker>
                                                    <!-- <v-datetime-picker label="Select Datetime" v-model="datetime"> </v-datetime-picker> -->
                                                </VCol>
                                                <VCol cols="12" md="12">
                                                    <v-label class="text-subtitle-1 font-weight-medium pb-1">Description</v-label>
                                                    <VTextarea variant="outlined"  outlined name="Description" label="Description" v-model="fields.description" :rules="fieldRules.description" required :color="fields.description.length > 10 ? 'success' : 'primary'"></VTextarea>
                                                </VCol>
                                                <VCol cols="12" md="12">
                                                    <v-label class="text-subtitle-1 font-weight-medium pb-1">Address</v-label>
                                                    <VTextarea variant="outlined"  outlined name="Address" label="Address" v-model="fields.address" :rules="fieldRules.address" required :color="fields.address.length > 10 ? 'success' : 'primary'"></VTextarea>
                                                </VCol>
                                                <VCol cols="12" md="12">
                                                    <v-label class="text-subtitle-1 font-weight-medium pb-1">Location Details</v-label>
                                                    <VTextarea variant="outlined"  outlined name="Details" label="Location Details" v-model="fields.location_detail" :rules="fieldRules.location_detail" required :color="fields.location_detail.length > 10 ? 'success' : 'primary'"></VTextarea>
                                                </VCol>
                                                <VCol cols="12" md="12">
                                                    
  <v-file-input
    v-model="files"
    :show-size="1000"
    color="deep-purple-accent-4"
    label="File input"
    placeholder="Select your files"
    prepend-icon="mdi-paperclip"
    variant="outlined"
    counter
    multiple
  >
    <template v-slot:selection="{ fileNames }">
      <template v-for="(fileName, index) in fileNames" :key="fileName">
        <v-chip
          v-if="index < 2"
          class="me-2"
          color="deep-purple-accent-4"
          size="small"
          label
        >
          {{ fileName }}
        </v-chip>

        <span
          v-else-if="index === 2"
          class="text-overline text-grey-darken-3 mx-2"
        >
          +{{ files.length - 2 }} File(s)
        </span>
      </template>
    </template>
  </v-file-input>
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
                                            <h3 class="text-h3">Edit Observation</h3>
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

                                                <VCol cols="12" md="6">
                                                    <v-label class="font-weight-medium pb-1">Observation Type</v-label>
                                                    <VSelect v-model="editFields.observationTypeId" :items="getObservationTypes"
                                                        :rules="fieldRules.observationTypeId" label="Select"
                                                        :selected="''" item-title="description" item-value="id"
                                                        single-line variant="outlined" class="text-capitalize">
                                                    </VSelect>
                                                </VCol>
                                                <VCol cols="12" md="6">
                                                    <v-label class="font-weight-medium pb-1">Severity</v-label>
                                                    <VSelect v-model="editFields.priorityId" :items="getPriorities"
                                                        :rules="fieldRules.priorityId" label="Select"
                                                        :selected="''" item-title="description" item-value="id"
                                                        single-line variant="outlined" class="text-capitalize">
                                                    </VSelect>
                                                </VCol>

                                                <VCol cols="12" md="6">
                                                    <v-label class="text-subtitle-1 font-weight-medium pb-1">Number of affected worker(s)</v-label>
                                                    <VTextField type="number" v-model="editFields.affectedWorkers" :rules="fieldRules.affectedWorkers"
                                                        required variant="outlined" label="Affected Workers"
                                                        :color="editFields.affectedWorkers.length > 2 ? 'success' : 'primary'">
                                                    </VTextField>
                                                </VCol>
                                                <VCol cols="12" md="6">
                                                    <v-label class="text-subtitle-1 font-weight-medium pb-1"> When did it happen?</v-label>
                                                    <VueDatePicker input-class-name="dp-custom-input"  v-model="editFields.date_time"  :max-date="new Date()" required></VueDatePicker>
                                                </VCol>
                                                <VCol cols="12" md="12">
                                                    <v-label class="text-subtitle-1 font-weight-medium pb-1">Description</v-label>
                                                    <VTextarea variant="outlined"  outlined name="Description" label="Description" v-model="editFields.description" :rules="fieldRules.description" required :color="editFields.description.length > 10 ? 'success' : 'primary'"></VTextarea>
                                                </VCol>
                                                <VCol cols="12" md="12">
                                                    <v-label class="text-subtitle-1 font-weight-medium pb-1">Address</v-label>
                                                    <VTextarea variant="outlined"  outlined name="Address" label="Address" v-model="editFields.address" :rules="fieldRules.address" required :color="editFields.address.length > 10 ? 'success' : 'primary'"></VTextarea>
                                                </VCol>
                                                <VCol cols="12" md="12">
                                                    <v-label class="text-subtitle-1 font-weight-medium pb-1">Location Details</v-label>
                                                    <VTextarea variant="outlined"  outlined name="Details" label="Location Details" v-model="editFields.location_detail" :rules="fieldRules.location_detail" required :color="editFields.location_detail.length > 10 ? 'success' : 'primary'"></VTextarea>
                                                </VCol>
                                                <VCol cols="12" lg="12" class="text-right">
                                                    <v-btn color="error" @click="dialog = false"
                                                        variant="text">Cancel</v-btn>

                                                    <v-btn color="primary" type="submit" :loading="loading"
                                                        :disabled="!valid" @click="update">
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
                                            <h3 class="text-h3">View Observation</h3>
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
                                                        :title="`Observation Type : ${selectedItem?.observation_type.description}`"></v-list-item>
                                                    <v-list-item
                                                        :title="`Observer : ${selectedItem?.observer.firstName} ${selectedItem?.observer?.lastName}`"></v-list-item>
                                                    <v-list-item
                                                        :title="`Description : ${selectedItem?.description}`"></v-list-item>
                                                    <v-list-item
                                                        :title="`Address : ${selectedItem?.address}`"></v-list-item>
                                                    <v-list-item
                                                        :title="`Location Details : ${selectedItem?.location_details}`"></v-list-item>
                                                    <v-list-item
                                                        :title="`Affected Workers : ${selectedItem?.affected_workers}`"></v-list-item>
                                                    <v-list-item
                                                        :title="`Incident Date : ${selectedItem?.incident_datetime}`"></v-list-item>
                                                    <v-list-item
                                                        :title="`Status : ${selectedItem?.status}`"></v-list-item>
                                                        
                                                </v-list>
                                            </VCol>

                                            <VCol cols="12" lg="12" class="text-right">
                                                <v-btn color="primary" class="mr-3" @click="setViewDialog(false)">Start Investigation</v-btn>
                                                <!-- <v-btn color="primary" class="mr-3" @click="setViewDialog(false)">Verify</v-btn> -->

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
                            <v-dialog v-model="deleteDialog" max-width="500">
                                <v-card>
                                    <v-card-text>
                                        <div class="d-flex justify-space-between">
                                            <h3 class="text-h3">Delete Observation</h3>
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
                                                    title="Do you want to delete observation?"></v-card>
                                            </VCol>

                                            <VCol cols="12" lg="12" class="text-right">

                                                <v-btn color="primary" class="mr-3" :loading="loading"
                                                    :disabled="loading" @click="removeObservation(selectedItem)">
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

                    <VDataTable :headers="headers" :items="getObservations" :search="search" item-key="name"
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

                        
                        <template v-slot:item.status="{ item }">
                            <div class="text-end">
                                <v-chip
                                    :color="item.selectable.status=='invite' ? 'green' : 'orange'"
                                    :text="item.selectable.status"
                                    class="text-uppercase"
                                    size="small"
                                    label
                                ></v-chip>
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
