<script setup lang="ts">
import { ref, onMounted, computed, nextTick } from 'vue';


import BaseBreadcrumb from '@/components/shared/BaseBreadcrumb.vue';
import { VDataTable } from 'vuetify/labs/VDataTable'
import { useOrganizationStore } from '@/stores/organizationStore';
import { useOfferStore } from '@/stores/offerStore';
import { useAuthStore } from '@/stores/auth';
import moment from 'moment'
import { router } from '@/router';


const authStore = useAuthStore();
const organizationStore = useOrganizationStore();
const offerStore = useOfferStore();

onMounted(() => {
    offerStore.getOffers();
    organizationStore.getAllOrganizations();

});

const computedIndex = (index: any) => ++index;
const getOffers: any = computed(() => (offerStore.offers));
const getActiveOrg: any = computed(() => (organizationStore.getActiveOrg()));
const getAuthUser: any = computed(() => (authStore.loggedUser));
const getAllOrganizations: any = computed(() => (organizationStore.organizations));
const isLoggedInUserOwnsActionOrg: any = computed(() => (getAuthUser.value?.id == getActiveOrg.value?.user_id));


const page = ref({ title: 'Request and Invites' });
const breadcrumbs = ref([
    {
        text: 'Dashboard',
        disabled: false,
        href: 'dashboard'
    },
    {
        text: 'Team Members',
        disabled: true,
        href: 'team-member'
    },
    {
        text: 'Request and Invites',
        disabled: true,
        href: '#'
    }
]);


const valid = ref(true);
const formContainer = ref()
const loading = ref(false);
const setLoading = (value: boolean) => {
    loading.value = value;
}
const dialog = ref(false);
const setInviteDialog = (value: boolean) => {
    dialog.value = value;
}
const requestDialog = ref(false);
const setRequestDialog = (value: boolean) => {
    requestDialog.value = value;
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
        case 'request':
            setRequestDialog(true)
            break;
        case 'invite':
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
        key: 'type',
        title: 'Type',
        value: (item: any): string => `${item.type}`,
    },
    {
        key: 'status',
        title: 'Status',
        value: (item: any): string => `${item.status}`
    },
    {
        key: 'actionUserEmail',
        title: 'Action Email',
        value: (item: any): string => `${item.actionUserEmail}`
    },
    {
        key: 'recipientEmail',
        title: 'Recipient Email',
        value: (item: any): string => `${item.recipientEmail}`
    },
    {
        key: 'message',
        title: 'Message',
        value: (item: any): string => `${item.message}`
    },
    {
        key: 'created_at',
        title: 'Date Created',
        value: (item: any): string => `${moment(item.createdAt).format('MMMM Do YYYY, h:mm:ss a')}`
    },
    {
        key: 'action',
        sortable: false,
        // align: 'end',
        title: 'Action',
    },
]);

type OrganizationType = {
    uuid: string
    name: string,
    token: string,
};


const fields = ref({
    email: "",
    message: "",
    orgToken: '',
    organization: null as null || {} as OrganizationType,
    organization_id: getActiveOrg.value?.uuid
});

const fieldRules: any = ref({
    message: [
        (v: string) => !!v || 'Message is required',
        (v: string) => v.length > 10 || 'More than 10 letters required'
    ],
    orgToken: [
        (v: string) => !!v || 'Token is required',
    ],
    email: [(v: string) => !!v || 'E-mail is required', (v: string) => /.+@.+\..+/.test(v) || 'E-mail must be valid'],
})



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

const sendInvite = async (e: any) => {
    e.preventDefault();

    try {
        setLoading(true)

        if (!isLoggedInUserOwnsActionOrg) {
            throw "You cant perform this action";
            return;
        }


        const values = { ...fields.value }
        let objectValues = {
            "type": 'invite',
            "message": values?.message,
            "organization_id": values?.organization_id,
            "recipient_email": values?.email
        }

        const resp = await offerStore.sendOffer(objectValues)
            .catch((error: any) => {
                console.log(error)
                throw error
            })
            .then((resp: any) => {
                return resp
            });

        if (resp?.message == 'success') {
            setLoading(false)
            setInviteDialog(false)
            // setTimeout(() => {
            //     formContainer.value.reset()
            // }, 2000)
            fields.value.message = ''
            fields.value.email = ''
            offerStore.getOffers();
        }

        setLoading(false)
        setInviteDialog(false)



    } catch (error) {
        console.log(error)
        setLoading(false)
        setInviteDialog(false)
    }

}

const sendRequest = async (e: any) => {
    e.preventDefault();

    try {
        setLoading(true)


        const values = { ...fields.value }
        let objectValues = {
            "type": 'request',
            "message": values?.message,
            "organization_id": values?.organization?.uuid
        }

        const resp = await offerStore.sendOffer(objectValues)
            .catch((error: any) => {
                console.log(error)
                throw error
            })
            .then((resp: any) => {
                return resp
            });

        if (resp?.message == 'success') {
            setLoading(false)
            setRequestDialog(false)

            offerStore.getOffers();
            fields.value.message = ''
            fields.value.orgToken = ''
        }

        setLoading(false)
        setRequestDialog(false)



    } catch (error) {
        setLoading(false)
        setRequestDialog(false)
    }

}


const actionOffer = async (offer: any, status: string) => {

    try {
        setLoading(true)

        let objectValues = {
            "status": status,
            "offer_id": offer?.uuid,
        }

        const resp = await offerStore.actionOffer(objectValues)
            .catch((error: any) => {
                throw error
            })
            .then((resp: any) => {
                return resp
            });

        if (resp?.message == 'success') {
            setLoading(false)
            setDeleteDialog(false)

            offerStore.getOffers();
        }

        setLoading(false)
        setDeleteDialog(false)



    } catch (error) {
        console.log(error)
        setLoading(false)
        setDeleteDialog(false)
    }

}

const goBack = () => {
    router.push('/team-member')
}

const getColor = (status: string) => {
    if (status == 'rejected') return 'red'
    else if (status == 'pending') return 'orange'
    else return 'green'
}

</script>

<template>
    <div>
        <BaseBreadcrumb :title="page.title" :breadcrumbs="breadcrumbs"></BaseBreadcrumb>
        <v-row>
            <v-col cols="12" md="12">

                <v-btn color="primary" class="ml-2" @click="goBack">Back</v-btn>
                <v-card :title="`Invitations and Requests`" flat>



                    <template v-slot:append>
                        <v-sheet>
                            <v-btn color="primary" class="mr-2" @click="setInviteDialog(true)"
                                v-if="isLoggedInUserOwnsActionOrg">Send Invite</v-btn>
                            <v-btn color="primary" class="mr-2" @click="setRequestDialog(true)">Send Request</v-btn>

                            <v-dialog v-model="dialog" max-width="800">
                                <v-card v-if="isLoggedInUserOwnsActionOrg">
                                    <v-card-text>
                                        <div class="d-flex justify-space-between">
                                            <h3 class="text-h3">Send Offer</h3>
                                            <v-btn icon @click="setInviteDialog(false)" size="small" flat>
                                                <XIcon size="16" />
                                            </v-btn>
                                        </div>
                                    </v-card-text>
                                    <v-divider></v-divider>

                                    <v-card-text>
                                        <!-- <Form > -->
                                        <VForm v-model="valid" ref="formContainer" fast-fail lazy-validation
                                            @submit.prevent="sendInvite" class="py-1">
                                            <VRow class="mt-5 mb-3">

                                                <VCol cols="12" md="12">
                                                    <v-label
                                                        class="text-subtitle-1 font-weight-medium pb-1">Email</v-label>
                                                    <VTextField v-model="fields.email" :rules="fieldRules.email"
                                                        required variant="outlined" label="Email Address"
                                                        :color="fields.email.length > 2 ? 'success' : 'primary'">
                                                    </VTextField>
                                                </VCol>
                                                <VCol cols="12" md="12">
                                                    <v-label
                                                        class="text-subtitle-1 font-weight-medium pb-1">Messages</v-label>
                                                    <VTextarea variant="outlined" outlined name="Note"
                                                        label="Message..." v-model="fields.message"
                                                        :rules="fieldRules.message" required
                                                        :color="fields.message.length > 10 ? 'success' : 'primary'">
                                                    </VTextarea>
                                                </VCol>
                                                <VCol cols="12" lg="12" class="text-right">
                                                    <v-btn color="error" @click="setInviteDialog(false)"
                                                        variant="text">Cancel</v-btn>

                                                    <v-btn color="primary" type="submit" :loading="loading"
                                                        :disabled="!valid" @click="sendInvite">
                                                        <span v-if="!loading">
                                                            Send Invite
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

                            <v-dialog v-model="requestDialog" max-width="800">

                                <v-card>
                                    <v-card-text>
                                        <div class="d-flex justify-space-between">
                                            <h3 class="text-h3">Send Request</h3>
                                            <v-btn icon @click="setRequestDialog(false)" size="small" flat>
                                                <XIcon size="16" />
                                            </v-btn>
                                        </div>
                                    </v-card-text>
                                    <v-divider></v-divider>

                                    <v-card-text>
                                        <!-- <Form > -->
                                        <VForm v-model="valid" ref="formContainer" fast-fail lazy-validation
                                            @submit.prevent="sendRequest" class="py-1">
                                            <VRow class="mt-5 mb-3">

                                                <VCol cols="12" md="12">
                                                    <v-label
                                                        class="text-subtitle-1 font-weight-medium pb-1">Organization
                                                        Invite Token</v-label>
                                                    <VTextField type="text" v-model="fields.orgToken"
                                                        :rules="fieldRules.orgToken" required variant="outlined"
                                                        label="" @change="fetchOrganization"
                                                        :color="fields.orgToken.length > 2 ? 'success' : 'primary'">
                                                        <template v-slot:append>
                                                            <v-btn color="primary" @click="fetchOrganization"
                                                                :disabled="fields.orgToken?.length < 12">Search</v-btn>
                                                        </template>
                                                    </VTextField>
                                                </VCol>

                                                <VCol cols="12" md="12" v-if="fields.orgToken?.length > 12">

                                                    <div v-if="Object.keys(fields.organization).length > 1">
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

                                                <template v-if="Object.keys(fields.organization).length > 1">

                                                    <VCol cols="12" md="12">
                                                        <v-label
                                                            class="text-subtitle-1 font-weight-medium pb-1">Messages</v-label>
                                                        <VTextarea variant="outlined" outlined name="Note"
                                                            label="Custom Message" v-model="fields.message"
                                                            :rules="fieldRules.message" required
                                                            :color="fields.message.length > 10 ? 'success' : 'primary'">
                                                        </VTextarea>
                                                    </VCol>
                                                    <VCol cols="12" lg="12" class="text-right">
                                                        <v-btn color="error" @click="setRequestDialog(false)"
                                                            variant="text">Cancel</v-btn>

                                                        <v-btn color="primary" type="submit" :loading="loading"
                                                            :disabled="!valid" @click="sendRequest">
                                                            <span v-if="!loading">
                                                                Send Request
                                                            </span>
                                                            <clip-loader v-else :loading="loading" color="white"
                                                                :size="'25px'"></clip-loader>
                                                        </v-btn>

                                                    </VCol>
                                                </template>
                                            </VRow>
                                        </VForm>
                                    </v-card-text>
                                </v-card>
                            </v-dialog>

                        </v-sheet>
                    </template>

                    <template v-slot:prepend>

                        <v-sheet>
                            <v-dialog v-model="deleteDialog" max-width="400">
                                <v-card>
                                    <v-card-text>
                                        <div class="d-flex justify-space-between">
                                            <h3 class="text-h3">Offer Action</h3>
                                            <v-btn icon @click="setDeleteDialog(false)" size="small" flat>
                                                <XIcon size="16" />
                                            </v-btn>
                                        </div>
                                    </v-card-text>
                                    <v-divider></v-divider>

                                    <v-card-text>

                                        <VRow class="mt-1 mb-3">


                                            <VCol cols="12" lg="12">
                                                <v-card text="You can not revert this action"
                                                    title="Do you want to accept this action?"></v-card>
                                            </VCol>

                                            <VCol cols="12" lg="12" class="text-right">

                                                <v-btn color="primary" class="mr-3" :loading="loading"
                                                    :disabled="loading" @click="actionOffer(selectedItem, 'accept')">
                                                    <span v-if="!loading">
                                                        Accept
                                                    </span>
                                                    <clip-loader v-else :loading="loading" color="white"
                                                        :size="'25px'"></clip-loader>
                                                </v-btn>

                                                <v-btn color="error" class="mr-3" :loading="loading" :disabled="loading"
                                                    @click="actionOffer(selectedItem, 'reject')">
                                                    <span v-if="!loading">
                                                        Reject
                                                    </span>
                                                    <clip-loader v-else :loading="loading" color="white"
                                                        :size="'25px'"></clip-loader>
                                                </v-btn>

                                                <!-- <v-btn color="error" class="mr-3" :disabled="loading"
                                                    @click="setDeleteDialog(false)">Reject</v-btn> -->

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

                    <VDataTable :headers="headers" :items="getOffers" :search="search" item-key="name"
                        items-per-page="5" item-value="fat" show-select>
                        <template v-slot:item.action="{ item }">
                            <!-- <div v-if="item.selectable.type == 'invite'"> -->
                            <v-btn size="small" @click="selectItem(item, 'invite')"
                                v-if="item.selectable.recipientEmail == getAuthUser.email && item.selectable.status == 'pending'">
                                <v-icon class="" size="small">
                                    mdi-gesture-double-tap
                                </v-icon>
                            </v-btn>
                            <!-- </div>
                            <div v-else>
                                <v-btn size="small" @click="selectItem(item, 'delete')" v-if="item.selectable.recipientEmail == getAuthUser.email &&  item.selectable.status == 'pending'">
                                    <v-icon class="" size="small">
                                        mdi-gesture-double-tap
                                    </v-icon>
                                </v-btn>
                            </div> -->

                        </template>

                        <template v-slot:item.sn="{ index }">
                            {{ computedIndex(index) }}
                        </template>

                        <template v-slot:item.status="{ item }">
                            <v-chip :color="getColor(item.selectable.status)" :text="item.selectable.status"
                                class="text-uppercase" size="small" label>
                            </v-chip>
                        </template>

                        <template v-slot:item.type="{ item }">
                            <div class="text-end">
                                <v-chip :color="item.selectable.type == 'invite' ? 'green' : 'orange'"
                                    :text="item.selectable.type" class="text-uppercase" size="small" label></v-chip>
                            </div>
                        </template>

                    </VDataTable>

                </v-card>

            </v-col>
        </v-row>
    </div>
</template>
