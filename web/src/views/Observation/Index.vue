<script setup lang="ts">
import { ref, onMounted, computed, nextTick } from 'vue';
import BaseBreadcrumb from '@/components/shared/BaseBreadcrumb.vue';
import { VDataTable } from 'vuetify/labs/VDataTable'
import { useOrganizationStore } from '@/stores/organizationStore';
import { useInvestigationStore } from '@/stores/investigationStore';
import { useAuthStore } from '@/stores/auth';
import { useOpenLinksStore } from '@/stores/openLinks';
import { useObservationStore } from '@/stores/observationStore';
import moment from 'moment'
import { router } from '@/router';
import Swal from 'sweetalert2'


const page = ref({ title: 'Observations' });
const breadcrumbs = ref([
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
const openLinks = useOpenLinksStore();
const observationStore = useObservationStore();
const investigationStore = useInvestigationStore()

onMounted(() => {
    observationStore.getObservations();
    openLinks.getPriorities();
    openLinks.getObservationTypes();
});

const computedIndex = (index: any) => ++index;

const getObservations: any = computed(() => (observationStore.observations));
const getActiveOrg: any = computed(() => (organizationStore.getActiveOrg()));
const getAuthUser: any = computed(() => (authStore.loggedUser));
const getObservationTypes: any = computed(() => (openLinks.observationTypes));
const getPriorities: any = computed(() => (openLinks.priorities));
const isLoggedInUserOwnsActionOrg: any = computed(() => (getAuthUser.value?.id == getActiveOrg.value?.user_id));


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
const selectedItem = ref({} as any);
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
        value: (item: any): string => `${item.observer.lastName}  ${item.observer.firstName}`,
    },
    {
        key: 'description',
        title: 'Description',
        value: (item: any): string => `${item.description.substring(0, 15)} ${item.description.length > 15 ? '...' : ''}`
    },
    {
        key: 'observation_type_id',
        title: 'Observation Type',
        value: (item: any): string => `${item.observation_type.description} `
    },
    {
        key: 'status',
        title: 'Status',
        value: (item: any): string => `${item.status} `
    },
    {
        key: 'incident_datetime',
        title: 'Incident Date/Time',
        value: (item: any): string => `${moment(item.incident_datetime).format('MMMM Do YYYY, h:mm:ss a')} `
    },
    {
        key: 'created_at',
        title: 'Date Created',
        value: (item: any): string => `${moment(item.created_at).format('MMMM Do YYYY, h:mm:ss a')} `
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
    images: [],
    organization_id: getActiveOrg.value?.uuid,

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
    organization_id: getActiveOrg.value?.uuid,

});

const fieldRules: any = ref({
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

const images = ref([])

const save = async (e: any) => {
    e.preventDefault();

    try {
        setLoading(true)

        const values = { ...fields.value }
        const formData = new FormData();

        formData.append('observation_type', values?.observationTypeId);
        formData.append('description', values?.description);
        formData.append('location_details', values?.location_detail);
        formData.append('address', values?.address);
        formData.append('affected_workers', values?.affectedWorkers);
        formData.append('organization_id', values.sendToOrg == 'No' ? null : values?.organization_id);
        formData.append('date_time', moment(fields.value.date_time).format('YYYY-MM-DD hh:mm:ss'));
        formData.append('severity', values?.priorityId);
        // formData.append('images', images.value);
        // formData.append('files[]', fields.value.images?.map((item: any) => (item[0])))
        // formData.append('files[]', files.value)
        files.value.forEach(file => {
            formData.append('files[]', file);
        });
        // formData.append('files[]', values?.images[0])




        const resp = await observationStore.addObservation(formData)
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
            // setTimeout(() => {
            //     formContainer.value.reset()
            // }, 2000)
            fields.value.observationTypeId = '';
            fields.value.description = '';
            fields.value.location_detail = '';
            fields.value.address = '';
            fields.value.affectedWorkers = '';
            fields.value.sendToOrg = 'No';
            fields.value.date_time = '';
            fields.value.priorityId = '';
            fields.value.images = [];
            files.value = []
            previewImage.value = []
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
            // setTimeout(() => {
            //     formContainer.value.reset()
            // }, 2000)
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


const files = ref([])
// const images = ref([])
const previewImage = ref([] as any)

const selectImage = (image: any) => {

    fields.value.images = image.target.files;
    console.log(image.target.files)
    images.value = image.target.files;
    previewImage.value = [];

    for (let index = 0; index < images.value.length; index++) {
        const element = images.value[index];
        previewImage.value.push(URL.createObjectURL(element) as string)
    }

}
const startInvestigation = async (item: any) => {
    // e.preventDefault();

    try {
        setLoading(true)
        setViewDialog(false)

        let objectValues = {
            "organization_id": getActiveOrg.value?.uuid,
            "observation_id": item?.uuid,
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

                startInvestigationAction(objectValues)
            }
        });

        setLoading(false)
        setEditDialog(false)



    } catch (error) {
        console.log(error)
        setLoading(false)
        setEditDialog(false)
    }

}
const startInvestigationAction = async (item: any) => {
    // e.preventDefault();

    try {

        const resp = await investigationStore.startInvestigation(item)
            .catch((error: any) => {
                throw error
            })
            .then((resp: any) => {
                return resp
            });

        if (resp?.message == 'success') {

            router.push(`/hse-investigating/${item.observation_id} `)
        }




    } catch (error) {

    }

}

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
                                                    <VSelect v-model="fields.observationTypeId"
                                                        :items="getObservationTypes"
                                                        :rules="fieldRules.observationTypeId" label="Select"
                                                        item-title="description" item-value="id" single-line
                                                        variant="outlined" class="text-capitalize">
                                                    </VSelect>
                                                </VCol>
                                                <VCol cols="12" md="6">
                                                    <v-label class="font-weight-medium pb-1">Severity</v-label>
                                                    <VSelect v-model="fields.priorityId" :items="getPriorities"
                                                        :rules="fieldRules.priorityId" label="Select"
                                                        item-title="description" item-value="id" single-line
                                                        variant="outlined" class="text-capitalize">
                                                    </VSelect>
                                                </VCol>

                                                <VCol cols="12" md="6">
                                                    <v-label class="text-subtitle-1 font-weight-medium pb-1">Number of
                                                        affected worker(s)</v-label>
                                                    <VTextField type="number" v-model="fields.affectedWorkers"
                                                        :rules="fieldRules.affectedWorkers" required variant="outlined"
                                                        label="Affected Workers"
                                                        :color="fields.affectedWorkers.length > 2 ? 'success' : 'primary'">
                                                    </VTextField>
                                                </VCol>
                                                <VCol cols="12" md="6">
                                                    <v-label class="font-weight-medium pb-1">Send to
                                                        Organization?</v-label>
                                                    <VSelect v-model="fields.sendToOrg" :items="['Yes', 'No']"
                                                        :rules="fieldRules.sendToOrg" label="Select" single-line
                                                        variant="outlined" class="text-capitalize">
                                                    </VSelect>
                                                </VCol>
                                                <VCol cols="12" md="12">
                                                    <v-label class="text-subtitle-1 font-weight-medium pb-1">When did it
                                                        happen?</v-label>
                                                    <VueDatePicker input-class-name="dp-custom-input"
                                                        v-model="fields.date_time" :max-date="new Date()" required>
                                                    </VueDatePicker>
                                                    <!-- <v-datetime-picker label="Select Datetime" v-model="datetime"> </v-datetime-picker> -->
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
                                                    <v-label
                                                        class="text-subtitle-1 font-weight-medium pb-1">Address</v-label>
                                                    <VTextarea variant="outlined" outlined name="Address"
                                                        label="Address" v-model="fields.address"
                                                        :rules="fieldRules.address" required
                                                        :color="fields.address.length > 10 ? 'success' : 'primary'">
                                                    </VTextarea>
                                                </VCol>
                                                <VCol cols="12" md="12">
                                                    <v-label class="text-subtitle-1 font-weight-medium pb-1">Location
                                                        Details</v-label>
                                                    <VTextarea variant="outlined" outlined name="Details"
                                                        label="Location Details" v-model="fields.location_detail"
                                                        :rules="fieldRules.location_detail" required
                                                        :color="fields.location_detail.length > 10 ? 'success' : 'primary'">
                                                    </VTextarea>
                                                </VCol>
                                                <VCol cols="12" md="12">

                                                    <v-file-input v-model="files" :show-size="1000"
                                                        color="deep-purple-accent-4" label="File input"
                                                        placeholder="Select your files" prepend-icon="mdi-paperclip"
                                                        variant="outlined" counter multiple accept="image/*"
                                                        @change="selectImage">
                                                        <template v-slot:selection="{ fileNames }">
                                                            <template v-for="(fileName, index) in fileNames"
                                                                :key="fileName">
                                                                <v-chip v-if="index < 2" class="me-2"
                                                                    color="deep-purple-accent-4" size="small" label>
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
                                                            <VCol cols="4" v-for="image in previewImage" :key="image">

                                                                <div>
                                                                    <img class="preview my-3" :src="image" alt=""
                                                                        style="max-width: 200px;" />
                                                                </div>
                                                            </VCol>
                                                        </VRow>
                                                    </div>
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
                                                    <VSelect v-model="editFields.observationTypeId"
                                                        :items="getObservationTypes"
                                                        :rules="fieldRules.observationTypeId" label="Select"
                                                        item-title="description" item-value="id" single-line
                                                        variant="outlined" class="text-capitalize">
                                                    </VSelect>
                                                </VCol>
                                                <VCol cols="12" md="6">
                                                    <v-label class="font-weight-medium pb-1">Severity</v-label>
                                                    <VSelect v-model="editFields.priorityId" :items="getPriorities"
                                                        :rules="fieldRules.priorityId" label="Select"
                                                        item-title="description" item-value="id" single-line
                                                        variant="outlined" class="text-capitalize">
                                                    </VSelect>
                                                </VCol>

                                                <VCol cols="12" md="6">
                                                    <v-label class="text-subtitle-1 font-weight-medium pb-1">Number of
                                                        affected worker(s)</v-label>
                                                    <VTextField type="number" v-model="editFields.affectedWorkers"
                                                        :rules="fieldRules.affectedWorkers" required variant="outlined"
                                                        label="Affected Workers"
                                                        :color="editFields.affectedWorkers.length > 2 ? 'success' : 'primary'">
                                                    </VTextField>
                                                </VCol>
                                                <VCol cols="12" md="6">
                                                    <v-label class="text-subtitle-1 font-weight-medium pb-1"> When did
                                                        it happen?</v-label>
                                                    <VueDatePicker input-class-name="dp-custom-input"
                                                        v-model="editFields.date_time" :max-date="new Date()" required>
                                                    </VueDatePicker>
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
                                                    <v-label
                                                        class="text-subtitle-1 font-weight-medium pb-1">Address</v-label>
                                                    <VTextarea variant="outlined" outlined name="Address"
                                                        label="Address" v-model="editFields.address"
                                                        :rules="fieldRules.address" required
                                                        :color="editFields.address.length > 10 ? 'success' : 'primary'">
                                                    </VTextarea>
                                                </VCol>
                                                <VCol cols="12" md="12">
                                                    <v-label class="text-subtitle-1 font-weight-medium pb-1">Location
                                                        Details</v-label>
                                                    <VTextarea variant="outlined" outlined name="Details"
                                                        label="Location Details" v-model="editFields.location_detail"
                                                        :rules="fieldRules.location_detail" required
                                                        :color="editFields.location_detail.length > 10 ? 'success' : 'primary'">
                                                    </VTextarea>
                                                </VCol>
                                                <VCol cols="12" lg="12" class="text-right">
                                                    <v-btn color="error" @click="setEditDialog(false)"
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

                                        <VRow class="mt-0 mb-3">

                                            <!-- {{ selectedItem }} -->

                                            <VCol cols="12" lg="12">

                                                <v-table>
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
                                                                <td class="text-subtitle-1 font-weight-medium pb-1">
                                                                    Observation Type </td>
                                                                <td>{{ `${selectedItem?.observation_type?.description}`
                                                                    }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td></td>
                                                                <td class="text-subtitle-1 font-weight-medium pb-1">
                                                                    Observer </td>
                                                                <td>{{ `${selectedItem?.observer?.firstName}
                                                                    ${selectedItem?.observer?.lastName}` }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td></td>
                                                                <td class="text-subtitle-1 font-weight-medium pb-1">
                                                                    Description </td>
                                                                <td>{{ `${selectedItem?.description}` }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td></td>
                                                                <td class="text-subtitle-1 font-weight-medium pb-1">
                                                                    Address </td>
                                                                <td>{{ `${selectedItem?.address}` }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td></td>
                                                                <td class="text-subtitle-1 font-weight-medium pb-1">
                                                                    Location Details </td>
                                                                <td>{{ `${selectedItem?.location_details}` }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td></td>
                                                                <td class="text-subtitle-1 font-weight-medium pb-1">
                                                                    Affected Workers </td>
                                                                <td>{{ `${selectedItem?.affected_workers}` }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td></td>
                                                                <td class="text-subtitle-1 font-weight-medium pb-1">
                                                                    Incident Date </td>
                                                                <td>{{ `${selectedItem?.incident_datetime}` }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td></td>
                                                                <td class="text-subtitle-1 font-weight-medium pb-1">
                                                                    Status </td>
                                                                <td>{{ `${selectedItem?.status}` }}</td>
                                                            </tr>
                                                        </template>
                                                    </tbody>
                                                </v-table>

                                                <template v-if="selectedItem">
                                                    <div v-if="selectedItem?.media?.length > 0">
                                                        <VRow>
                                                            <VCol cols="4" v-for="image in selectedItem?.media"
                                                                :key="image">

                                                                <div>
                                                                    <a :href="image?.full_url" target="_blank">
                                                                        <img class="preview my-3" :src="image?.full_url"
                                                                            alt="" style="max-width: 200px;" />
                                                                    </a>

                                                                </div>
                                                            </VCol>
                                                        </VRow>
                                                    </div>
                                                </template>
                                            </VCol>

                                            <VCol cols="12" lg="12" class="text-right"
                                                v-if="selectedItem?.is_pending_investigation && selectedItem?.organization_id">
                                                <v-btn color="primary" class="mr-3"
                                                    @click="startInvestigation(selectedItem)">Start
                                                    Investigation</v-btn>
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
                        <!-- <template v-slot:item.action="{ item }">
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
                        </template> -->


                        <template v-slot:item.action="{ item }">

                            <v-menu>
                                <template v-slot:activator="{ props }">
                                    <v-btn color="primary" dark v-bind="props" flat v-if="isLoggedInUserOwnsActionOrg">
                                        Action </v-btn>
                                </template>

                                <v-list>
                                    <v-list-item @click="selectItem(item, 'view')">
                                        <v-list-item-title>
                                            <v-icon class="mr-2" size="small">
                                                mdi-eye
                                            </v-icon>
                                            View Observation
                                        </v-list-item-title>
                                    </v-list-item>

                                    <v-list-item @click="selectItem(item, 'edit')">
                                        <v-list-item-title>
                                            <v-icon class="mr-2" size="small">
                                                mdi-pencil
                                            </v-icon>
                                            Edit Observation
                                        </v-list-item-title>
                                    </v-list-item>
                                    <v-list-item @click="selectItem(item, 'delete')">
                                        <v-list-item-title>
                                            <v-icon class="mr-2" size="small">
                                                mdi-delete
                                            </v-icon>
                                            Delete Observation
                                        </v-list-item-title>
                                    </v-list-item>

                                </v-list>
                            </v-menu>
                        </template>

                        <template v-slot:item.sn="{ index }">
                            {{ computedIndex(index) }}
                        </template>


                        <template v-slot:item.status="{ item }">
                            <div class="">
                                <v-chip :color="item.selectable.status == 'invite' ? 'green' : 'orange'"
                                    :text="item.selectable.status" class="text-uppercase" size="small" label></v-chip>
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
