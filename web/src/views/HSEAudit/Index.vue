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

const page = ref({ title: 'HSE Audit' });
const breadcrumbs = ref([
    {
        text: 'Dashboard',
        disabled: false,
        href: 'dashboard'
    },
    {
        text: 'HSE Audit',
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

const getMainAudits = computed(() => (reportStore.mainAudits));

const getActions = computed(() => (actionStore.actions));
const getActiveOrg = computed(() => (organizationStore.getActiveOrg()));
const getAuthUser = computed(() => (authStore.loggedUser));
const getPriorities = computed(() => (openLinks.priorities));
const getReports = computed(() => (reportStore.reports));
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
const selectedItem = ref({});
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

const isAssignee = computed(() => (selectedItem?.value?.assignee_id == getAuthUser?.value?.id));



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
        value: (item: any) => `${item.audit_title} `,
    },
    // {
    //     key: 'audit_template_id',
    //     title: 'Audit Template',
    //     value: (item: any) => `${item.audit_template?.title}`
    // },
    {
        key: 'audit_type_id',
        title: 'Audit Type',
        value: (item: any) => `${item.audit_type?.description}`
    },
    {
        key: 'recipient_organization_id',
        title: 'Recipient Organization',
        value: (item: any) => `${item.recipient_organization?.name}`
    },
    {
        key: 'organization_id',
        title: 'Audit Organization',
        value: (item: any) => `${item.organization?.name}`
    },
    {
        key: 'created_at',
        title: 'Date Created',
        value: (item: any) => `${moment(item.created_at).format('MMMM Do YYYY, h:mm a')}`
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
const previewImage = ref([])

const selectImage = (image: any) => {

    images.value = image.target.files;
    previewImage.value = [];

    for (let index = 0; index < images.value.length; index++) {
        const element = images.value[index];
        previewImage.value.push(URL.createObjectURL(element))
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

                <v-card :title="`HSE Statistics`" flat>


                    <template v-slot:append>
                        <v-sheet v-if="isLoggedInUserOwnsActionOrg">
                            <v-btn color="primary" class="mr-2" @click="goToRoute('/hse-audit-documents')">Audit
                                Documents</v-btn>
                            <v-btn color="primary" class="mr-2" @click="goToRoute('/hse-audit-templates')">Audit
                                Templates</v-btn>
                            <v-btn color="primary" class="mr-2" @click="goToRoute('/create-hse-audit-report')">Create
                                HSE Audit Report</v-btn>
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
                                                <v-list lines="one" v-if="selectedItem">

                                                    <!-- <v-list-item
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
                                                        :title="`Status Reason : ${selectedItem?.statusMessage}`"></v-list-item> -->

                                                </v-list>
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


                                            </VCol>
                                            <VCol cols="12" lg="12"  class="text-right">
                                                
                                                <v-btn color="primary" class="mr-3"
                                                    @click="goToRoute(`/ongoing-hse-audit-report/${selectedItem?.uuid}`)">Continue
                                                    Audit</v-btn>
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
                            {{ ++index }}
                        </template>




                    </VDataTable>

                </v-card>

            </v-col>
        </v-row>
    </div>
</template>
