<script setup lang="ts">
import { ref, onMounted, computed, nextTick, watch } from 'vue';
import BaseBreadcrumb from '@/components/shared/BaseBreadcrumb.vue';
import { VDataTable } from 'vuetify/labs/VDataTable'
import { useOrganizationStore } from '@/stores/organizationStore';
import { useTeamMemberStore } from '@/stores/teamMemberStore';
import { useAuthStore } from '@/stores/auth';
import { useOpenLinksStore } from '@/stores/openLinks';
import { useActionStore } from '@/stores/actionStore';
import { useMainAuditStore } from '@/stores/mainAuditStore';

import moment from 'moment'
import { router } from '@/router';

const page = ref({ title: 'Audit Management' });
const breadcrumbs = ref([
    {
        text: 'Dashboard',
        disabled: false,
        href: 'dashboard'
    },
    {
        text: 'Audit Management',
        disabled: true,
        href: '#'
    }
]);


const authStore = useAuthStore();
const organizationStore = useOrganizationStore();
const teamMemberStore = useTeamMemberStore();
const openLinks = useOpenLinksStore();
const actionStore = useActionStore();
const reportStore = useMainAuditStore();
const mainAuditStore = useMainAuditStore();

onMounted(() => {
    // teamMemberStore.getTeamMembersExcept();
    // openLinks.getPriorities();
    // actionStore.getActions();
    mainAuditStore.getMainAudits()
});

const computedIndex = (index: any) => ++index;

const getMainAudits: any = computed(() => (reportStore.mainAudits));

const getActions: any = computed(() => (actionStore.actions));
const getActiveOrg: any = computed(() => (organizationStore.getActiveOrg()));
const getAuthUser: any = computed(() => (authStore.loggedUser));
const getPriorities: any = computed(() => (openLinks.priorities));
// const getReports : any  = computed(() => (reportStore.reports));
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
        key: 'audit_title',
        title: 'Audit Title',
        value: (item: any): string => `${item.audit_title.substring(0, 15)} ${item.audit_title.length > 15 ? '...' : ''}`
    },
    // {
    //     key: 'audit_template_id',
    //     title: 'Audit Template',
    //     value: (item: any): string => `${item.audit_template?.title}`
    // },
    {
        key: 'audit_type_id',
        title: 'Audit Type',
        value: (item: any): string => `${item.audit_type?.description.substring(0, 15)} ${item.audit_type?.description.length > 15 ? '...' : ''}`
    },
    {
        key: 'recipient_organization_id',
        title: 'Recipient Organization',
        value: (item: any): string => `${item.recipient_organization?.name}`
    },
    {
        key: 'organization_id',
        title: 'Audit Organization',
        value: (item: any): string => `${item.organization?.name}`
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


const goToRoute = (url: string) => {
    router.push(url)
}


</script>

<template>
    <div>
        <BaseBreadcrumb :title="page.title" :breadcrumbs="breadcrumbs"></BaseBreadcrumb>
        <v-row>
            <v-col cols="12" md="12">

                <v-card :title="`Audit Management`" flat>


                    <template v-slot:append>
                        <v-sheet v-if="isLoggedInUserOwnsActionOrg">
                            <v-btn color="primary" class="mr-2" @click="goToRoute('/hse-audit-documents')">Audit
                                Documents</v-btn>
                            <v-btn color="primary" class="mr-2" @click="goToRoute('/hse-audit-templates')">Audit
                                Templates</v-btn>
                            <v-btn color="primary" class="mr-2" @click="goToRoute('/create-hse-audit-report')">Create
                                Audit Report</v-btn>
                        </v-sheet>
                    </template>

                    <template v-slot:prepend>

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
                                                <!-- {{ selectedItem }} -->
                                                <v-table v-if="selectedItem">
                                                    <thead>
                                                        <tr>
                                                            <th></th>
                                                            <th></th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <th>Audit Title</th>
                                                            <th>{{ `${selectedItem?.audit_title}` }}</th>
                                                        </tr>
                                                        <tr>
                                                            <th>Audit Type</th>
                                                            <th>{{ `${selectedItem?.audit_type?.description}` }}</th>
                                                        </tr>
                                                        <tr>
                                                            <th>Auditee</th>
                                                            <th>{{ `${selectedItem?.recipient_organization?.name}` }}
                                                            </th>
                                                        </tr>
                                                        <tr>
                                                            <th>Auditor</th>
                                                            <th>{{ `${selectedItem?.organization?.name}` }}</th>
                                                        </tr>
                                                        <tr>
                                                            <th>Status</th>
                                                            <th>{{ `${selectedItem?.status}` }}</th>
                                                        </tr>
                                                        <tr v-if="selectedItem?.status_reason">
                                                            <th>Comment</th>
                                                            <th>{{ `${selectedItem?.status_reason}` }}</th>
                                                        </tr>
                                                    </tbody>
                                                </v-table>
                                            </VCol>

                                            <VCol cols="12" lg="12" class="text-right">

                                                <v-btn color="primary" class="mr-3"
                                                    v-if="selectedItem?.is_ongoing || selectedItem?.is_pending || selectedItem?.is_accepted"
                                                    @click="goToRoute(`/ongoing-hse-audit-report/${selectedItem?.uuid}`)">Continue
                                                    Audit</v-btn>
                                                <template v-if="selectedItem?.is_completed">
                                                    <v-btn color="primary" class="mr-3"
                                                        @click="goToRoute(`/view-hse-audit-report/${selectedItem?.uuid}`)">View
                                                        Audit</v-btn>
                                                    <v-btn color="primary" class="mr-3"
                                                        @click="goToRoute(`/full-report-hse-audit-report/${selectedItem?.uuid}`)">Full
                                                        Audit Report</v-btn>
                                                    <v-btn color="primary" class="mr-3"
                                                        @click="goToRoute(`/report-hse-audit-report/${selectedItem?.uuid}`)">Audit
                                                        Summary</v-btn>
                                                </template>

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

                    <VDataTable :headers="headers" :items="getMainAudits" :search="search" item-key="name"
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
                                    <!-- <template v-if="isLoggedInUserOwnsActionOrg && item.selectable.is_pending">
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
                                    </template> -->
                                </v-list>
                            </v-menu>
                        </template>

                        <!-- <template v-slot:item.action="{ item }">

                            <v-menu>
                                <template v-slot:activator="{ props }">
                                    <v-btn color="primary" dark v-bind="props" flat> Action </v-btn>
                                </template>

                                <v-list>
                                    <template>
                                        <v-list-item @click="selectItem(item, 'view')">
                                            <v-list-item-title>
                                                <v-icon class="mr-2" size="small">
                                                    mdi-pencil
                                                </v-icon>
                                                Edit Action
                                            </v-list-item-title>
                                        </v-list-item>
                                    </template>
                                </v-list>
                            </v-menu>
                        </template> -->

                        <template v-slot:item.sn="{ index }">
                            {{ computedIndex(index) }}
                        </template>




                    </VDataTable>

                </v-card>

            </v-col>
        </v-row>
    </div>
</template>
