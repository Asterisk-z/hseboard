<script setup lang="ts">
import { ref, onMounted, computed, nextTick, watch } from 'vue';
import BaseBreadcrumb from '@/components/shared/BaseBreadcrumb.vue';
import { VDataTable } from 'vuetify/labs/VDataTable'
import { useOrganizationStore } from '@/stores/organizationStore';
import { useTeamMemberStore } from '@/stores/teamMemberStore';
import { useAuthStore } from '@/stores/auth';
import { useOpenLinksStore } from '@/stores/openLinks';
import { useActionStore } from '@/stores/actionStore';
import { useJobHazardStore } from '@/stores/jobHazardStore';
import moment from 'moment'
import { router } from '@/router';

const page = ref({ title: 'Job Hazard Analysis' });
const breadcrumbs = ref([
    {
        text: 'Dashboard',
        disabled: false,
        href: '/dashboard'
    },
    {
        text: 'Job Hazard Analysis',
        disabled: true,
        href: '#'
    }
]);


const authStore = useAuthStore();
const organizationStore = useOrganizationStore();
const teamMemberStore = useTeamMemberStore();
const openLinks = useOpenLinksStore();
const actionStore = useActionStore();
const jobHazardStore = useJobHazardStore();

onMounted(() => {
    // teamMemberStore.getTeamMembersExcept();
    // openLinks.getPriorities();
    jobHazardStore.getAllJobHazardAnalysis();
});


const getAllJobHazardAnalysis : any  = computed(() => (jobHazardStore.jobHazards));

const computedIndex = (index : any) => ++index;

const getTeamMembersExceptMe : any  = computed(() => (teamMemberStore.membersExceptMe));
const getActiveOrg : any  = computed(() => (organizationStore.getActiveOrg()));
const getAuthUser : any  = computed(() => (authStore.loggedUser));
const getObservationTypes : any  = computed(() => (openLinks.observationTypes));
const getPriorities : any  = computed(() => (openLinks.priorities));
const isLoggedInUserOwnsActionOrg : any  = computed(() => (getAuthUser.value?.id == getActiveOrg.value?.user_id));


const valid = ref(true);
const editValid = ref(true);
const formContainer = ref("")
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
            goToRoute(`/update-hazard-analysis/${selectedItem.value?.uuid}`)
            // setViewDialog(true)
            break;
        case 'review':
            goToRoute(`/review-hazard-analysis/${selectedItem.value?.uuid}`)
            // setViewDialog(true)
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
    // {
    //     key: 'description',
    //     title: 'Description',
    //     value: (item: any): string => `${item.description}`
    // },
    {
        key: 'prepared_by',
        title: 'Prepared By',
        value: (item: any): string => `${item.prepared_user?.lastName} ${item.prepared_user?.firstName}`
    },
    {
        key: 'status',
        title: 'Status',
        value: (item: any): string => `${item.status}`
    },
    // {
    //     key: 'priority_id',
    //     title: 'Priority',
    //     value: (item: any): string => `${item.priority.description}`
    // },
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

const fieldRules: any = ref({
    title: [
        (v: string) => !!v || 'Title  is required',
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
        }

        const resp = await jobHazardStore.createJobHazardAnalysis(objectValues)
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

            jobHazardStore.getAllJobHazardAnalysis();


        }

        setLoading(false)
        setDialog(false)



    } catch (error) {
        console.log(error)
        setLoading(false)
        setDialog(false)
    }

}




const goToRoute = (url: string) => {
    router.push(url)
}

</script>

<template>
    <div>
        <BaseBreadcrumb :title="page.title" :breadcrumbs="breadcrumbs"></BaseBreadcrumb>
        <v-row>
            <v-col cols="12" md="12">

                <v-card :title="`Job Hazard Analysis`" flat>


                    <template v-slot:append>
                        <v-sheet>
                            <v-btn color="primary" class="mr-2" @click="setDialog(true)">Add Job Hazard Analysis</v-btn>

                            <v-dialog v-model="dialog" max-width="600">
                                <v-card>
                                    <v-card-text>
                                        <div class="d-flex justify-space-between">
                                            <h3 class="text-h3">Create Job Hazard Analysis</h3>
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




                    <template v-slot:text>
                        <v-text-field v-model="search" label="Search" prepend-inner-icon="mdi-magnify"
                            variant="outlined" hide-details single-line></v-text-field>
                    </template>

                    <VDataTable :headers="headers" :items="getAllJobHazardAnalysis" :search="search" item-key="name"
                        items-per-page="5" item-value="fat" show-select>
                        <template v-slot:item.action="{ item }">

                            <v-menu>
                                <template v-slot:activator="{ props }">
                                    <v-btn color="primary" dark v-bind="props" flat> Action </v-btn>
                                </template>

                                <v-list>
                                    <v-list-item @click="selectItem(item, 'view')"
                                        v-if="item.selectable.is_ongoing || item.selectable.is_declined">
                                        <v-list-item-title>
                                            <v-icon class="mr-2" size="small">
                                                mdi-eye
                                            </v-icon>
                                            View Job
                                        </v-list-item-title>
                                    </v-list-item>
                                    <template v-if="isLoggedInUserOwnsActionOrg && ( item.selectable.is_completed ||  item.selectable.is_approved )">
                                        <v-list-item @click="selectItem(item, 'review')">
                                            <v-list-item-title>
                                                <v-icon class="mr-2" size="small">
                                                    mdi-pencil
                                                </v-icon>
                                                Review Job
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
