<script setup lang="ts">
import { ref, onMounted, computed, nextTick, watch } from 'vue';
import BaseBreadcrumb from '@/components/shared/BaseBreadcrumb.vue';
import { VDataTable } from 'vuetify/labs/VDataTable'
import { useOrganizationStore } from '@/stores/organizationStore';
import { useTeamMemberStore } from '@/stores/teamMemberStore';
import { useAuthStore } from '@/stores/auth';
import { useOpenLinksStore } from '@/stores/openLinks';
import { useActionStore } from '@/stores/actionStore';
import { useReportStore } from '@/stores/reportStore';
import moment from 'moment'
import { router } from '@/router';

const page = ref({ title: 'HSE Statistics' });
const breadcrumbs = ref([
    {
        text: 'Dashboard',
        disabled: false,
        href: 'dashboard'
    },
    {
        text: 'HSE Statistics',
        disabled: true,
        href: '#'
    }
]);


const authStore = useAuthStore();
const organizationStore = useOrganizationStore();
const teamMemberStore = useTeamMemberStore();
const openLinks = useOpenLinksStore();
const actionStore = useActionStore();
const reportStore = useReportStore();

onMounted(() => {
    teamMemberStore.getTeamMembersExcept();
    openLinks.getPriorities();
    actionStore.getActions();
    reportStore.getReports()
});


const computedIndex = (index: any) => ++index;

const getActions: any = computed(() => (actionStore.actions));
const getActiveOrg: any = computed(() => (organizationStore.getActiveOrg()));
const getAuthUser: any = computed(() => (authStore.loggedUser));
const getPriorities: any = computed(() => (openLinks.priorities));
const getReports: any = computed(() => (reportStore.reports));
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
            editFields.value.assigneeId = selectedItem.value?.assignee_id
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

const isAssignee: any = computed(() => (selectedItem?.value?.assignee_id == getAuthUser?.value?.id));



const search = ref('');

const headers = ref([
    {
        key: 'sn',
        title: 'SN',
        sortable: false,
    },
    {
        key: 'start_date',
        title: 'Start Date',
        value: (item: any): string => `${moment(item.start_date).format('MMMM Do YYYY')} `,
    },
    {
        key: 'end_date',
        title: 'End Date',
        value: (item: any): string => `${moment(item.end_date).format('MMMM Do YYYY')}`
    },
    {
        key: 'actual_man_hour',
        title: 'Actual Man Hour',
        value: (item: any): string => `${item.actual_man_hour}`
    },
    {
        key: 'total_man_hours',
        title: 'Total Man Hours',
        value: (item: any): string => `${item.total_man_hours}`
    },
    {
        key: 'total_overtime',
        title: 'Total Overtime',
        value: (item: any): string => `${item.total_overtime}`
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
    assigneeId: "",
    description: "",
    priorityId: "",
    end_date: "",
    start_date: "",
    organization_id: getActiveOrg.value?.uuid,

});

const fieldRules: any = ref({
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
        previewImage.value.push(URL.createObjectURL(element) as string)
    }

}

const goToRoute = (value: string) => {
    router.push(value)
}

</script>

<template>
    <div>
        <BaseBreadcrumb :title="page.title" :breadcrumbs="breadcrumbs"></BaseBreadcrumb>
        <v-row>
            <v-col cols="12" md="12">

                <v-card :title="`HSE Statistics`" flat>


                    <template v-slot:append>
                        <v-sheet v-if="isLoggedInUserOwnsActionOrg">
                            <v-btn color="primary" class="mr-2" @click="goToRoute('/create-statistics')">Create HSE
                                Statistics</v-btn>
                        </v-sheet>
                    </template>

                    <template v-slot:prepend>

                        <v-sheet>
                            <v-dialog v-model="viewDialog" max-width="800">
                                <v-card>
                                    <v-card-text>
                                        <div class="d-flex justify-space-between">
                                            <h3 class="text-h3">View Statistics</h3>
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
                                                <v-list lines="one" v-if="selectedItem">

                                                    <v-list-item
                                                        :title="`Title : ${selectedItem?.title}`"></v-list-item>
                                                    <v-list-item
                                                        :title="`Description : ${selectedItem?.description}`"></v-list-item>
                                                    <v-list-item
                                                        :title="`Assignee : ${selectedItem?.assignee?.firstName} ${selectedItem?.assignee?.lastName}`"></v-list-item>
                                                    <v-list-item
                                                        :title="`Assignor : ${selectedItem?.creator?.firstName} ${selectedItem?.creator?.lastName}`"></v-list-item>
                                                    <v-list-item
                                                        :title="`Status : ${selectedItem?.status}`"></v-list-item>
                                                    <v-list-item
                                                        :title="`Start Date time : ${selectedItem?.start_datetime}`"></v-list-item>
                                                    <v-list-item
                                                        :title="`End Date time : ${selectedItem?.end_datetime}`"></v-list-item>
                                                    <v-list-item
                                                        :title="`Priority : ${selectedItem?.priority?.description}`"></v-list-item>
                                                    <v-list-item v-if="selectedItem?.statusMessage"
                                                        :title="`Status Reason : ${selectedItem?.statusMessage}`"></v-list-item>

                                                </v-list>
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

                    </template>



                    <template v-slot:text>
                        <v-text-field v-model="search" label="Search" prepend-inner-icon="mdi-magnify"
                            variant="outlined" hide-details single-line></v-text-field>
                    </template>

                    <VDataTable :headers="headers" :items="getReports" :search="search" item-key="name"
                        items-per-page="5" item-value="fat" show-select>
                        <template v-slot:item.action="{ item }">

                            <v-btn color="primary" dark
                                @click="goToRoute(`/hse-statistics-report/${item.selectable.uuid}`)" flat>
                                Report </v-btn>

                        </template>

                        <template v-slot:item.sn="{ index }">
                            {{ computedIndex(index) }}
                        </template>




                    </VDataTable>

                </v-card>

            </v-col>
        </v-row>
    </div>
</template>
