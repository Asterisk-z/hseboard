<script setup lang="ts">
import { ref, onMounted, computed, nextTick, watch } from 'vue';
import BaseBreadcrumb from '@/components/shared/BaseBreadcrumb.vue';
import { VDataTable } from 'vuetify/labs/VDataTable'
import { useOrganizationStore } from '@/stores/organizationStore';
import { useTeamMemberStore } from '@/stores/teamMemberStore';
import { useAuthStore } from '@/stores/auth';
import { useOpenLinksStore } from '@/stores/openLinks';
import { useActionStore } from '@/stores/actionStore';
import { useInvestigationStore } from '@/stores/investigationStore';
import moment from 'moment'
import { router } from '@/router';

const page = ref({ title: 'HSE Investigation' });
const breadcrumbs = ref([
    {
        text: 'Dashboard',
        disabled: false,
        href: 'dashboard'
    },
    {
        text: 'HSE Investigation',
        disabled: true,
        href: '#'
    }
]);


const authStore = useAuthStore();
const organizationStore = useOrganizationStore();
const actionStore = useActionStore();
const investigationStore = useInvestigationStore();

onMounted(() => {
    investigationStore.getInvestigations();
});

const computedIndex = (index : any) => ++index;

const getInvestigations : any   = computed(() => (investigationStore.investigations));
const getActiveOrg : any   = computed(() => (organizationStore.getActiveOrg()));
const getAuthUser : any   = computed(() => (authStore.loggedUser));
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

const search = ref('');

const headers = ref([
    {
        key: 'sn',
        title: 'SN',
        sortable: false,
    },
    {
        key: 'user',
        title: 'Lunched By',
        sortable: false,
        value: (item: any): string => `${item?.user?.lastName} ${item?.user?.firstName} `,
    },
    {
        key: 'observation',
        title: 'Observation',
        sortable: false,
        value: (item: any): string => `${item?.observation?.description} `,
    },
    {
        key: 'observation_type',
        title: 'Observation Type',
        sortable: false,
        value: (item: any): string => `${item?.observation?.observation_type?.description} `,
    },
    {
        key: 'status',
        title: 'Status',
        sortable: false,
        value: (item: any): string => `${item?.observation?.status} `,
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







const gotoRoute = (link: string) => {
    router.push(link)
}

</script>

<template>
    <div>
        <BaseBreadcrumb :title="page.title" :breadcrumbs="breadcrumbs"></BaseBreadcrumb>
        <v-row>
            <v-col cols="12" md="12">

                <v-card :title="`HSE Investigation`" flat>



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

                                </v-card>
                            </v-dialog>

                        </v-sheet>

                        <v-sheet>
                            <v-dialog v-model="viewDialog" max-width="800">
                                <v-card>
                                    <v-card-text>
                                        <div class="d-flex justify-space-between">
                                            <h3 class="text-h3">View Investigation</h3>
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
                                                                <td>Observation Type </td>
                                                                <td>{{
                                                                    `${selectedItem?.observation?.observation_type?.description}`
                                                                    }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td></td>
                                                                <td>Observer </td>
                                                                <td>{{
                                                                    `${selectedItem?.observation?.observer?.firstName}
                                                                    ${selectedItem?.observation?.observer?.lastName}` }}
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td></td>
                                                                <td>Description </td>
                                                                <td>{{ `${selectedItem?.observation?.description}` }}
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td></td>
                                                                <td>Address </td>
                                                                <td>{{ `${selectedItem?.observation?.address}` }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td></td>
                                                                <td>Location Details </td>
                                                                <td>{{ `${selectedItem?.observation?.location_details}`
                                                                    }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td></td>
                                                                <td>Affected Workers </td>
                                                                <td>{{ `${selectedItem?.observation?.affected_workers}`
                                                                    }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td></td>
                                                                <td>Incident Date </td>
                                                                <td>{{ `${selectedItem?.observation?.incident_datetime}`
                                                                    }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td></td>
                                                                <td>Status </td>
                                                                <td>{{ `${selectedItem?.observation?.status}` }}</td>
                                                            </tr>
                                                        </template>
                                                    </tbody>
                                                </v-table>
                                            </VCol>


                                            <VCol cols="12" lg="12" class="text-right">
                                                <span v-if="isLoggedInUserOwnsActionOrg && selectedItem?.is_ongoing">
                                                    <v-btn color="primary" class="mr-3"
                                                        @click="gotoRoute(`/hse-investigating/${selectedItem?.observation?.uuid}`)">Continue
                                                        Investigation</v-btn>
                                                </span>
                                                <span v-if="selectedItem?.is_completed">
                                                    <v-btn color="primary" class="mr-3"
                                                        @click="gotoRoute(`/hse-investigated/${selectedItem?.uuid}`)">View
                                                        Investigation</v-btn>
                                                </span>


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

                                </v-card>
                            </v-dialog>

                        </v-sheet>
                    </template>



                    <template v-slot:text>
                        <v-text-field v-model="search" label="Search" prepend-inner-icon="mdi-magnify"
                            variant="outlined" hide-details single-line></v-text-field>
                    </template>

                    <VDataTable :headers="headers" :items="getInvestigations" :search="search" item-key="name"
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
                                            View Investigation
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
                                    :text="item.selectable?.observation?.status" class="text-uppercase" size="small"
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
