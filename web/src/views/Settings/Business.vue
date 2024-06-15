<script setup lang="ts">
import { ref, onMounted, computed, watch } from 'vue';
import { useRoute } from 'vue-router'
import { router } from '@/router';

import BaseBreadcrumb from '@/components/shared/BaseBreadcrumb.vue';
import UiParentCard from '@/components/shared/UiParentCard.vue';
import UpdatedBusinessDetail from '@/views/Settings/Components/UpdatedBusinessDetail.vue';
import UpdatedBusinessLogo from '@/views/Settings/Components/UpdatedBusinessLogo.vue';
import { useOrganizationStore } from '@/stores/organizationStore';
import { useAccountStore } from '@/stores/accountStore';
import { useAuthStore } from '@/stores/auth';
import { useFormatter } from '@/composables/formatter';
import moment from 'moment'
import Swal from 'sweetalert2'
import {
    CheckIcon,
} from 'vue-tabler-icons';

const page = ref({ title: 'Business Setting' });
const breadcrumbs = ref([
    {
        text: 'Dashboard',
        disabled: false,
        href: '/dashboard'
    },
    {
        text: 'Business Setting',
        disabled: true,
        href: '#'
    }
]);

const route = useRoute()
const authStore = useAuthStore();
const organizationStore = useOrganizationStore();
const accountStore = useAccountStore();
const { formatDate } = useFormatter();


onMounted(() => {
    organizationStore.getOrganizations()
});

const getAuthUser: any = computed(() => (authStore.loggedUser));
const getOrganizations: any = computed(() => (organizationStore.organization));
const getActiveOrg: any = computed(() => (organizationStore.getActiveOrg()));
const isLoggedInUserOwnsActionOrg: any = computed(() => (getAuthUser.value?.id == getActiveOrg.value?.user_id));




const dialog = ref(false);
const setDialog = (value: boolean) => {
    dialog.value = value;
}

const logoDialog = ref(false);
const setLogoDialog = (value: boolean) => {
    logoDialog.value = value;
}


const resetToken = async () => {


    try {

        Swal.fire({
            title: 'Info!',
            text: 'Are you sure?',
            icon: 'info',
            confirmButtonText: 'Yes',
            cancelButtonText: 'No',
            showCancelButton: true,
            allowOutsideClick: false,
        }).then((result) => {
            if (result.isConfirmed) {


                accountStore.updateToken()
                    .catch((error: any) => {
                        throw error
                    })
                    .then((resp: any) => {

                        organizationStore.getOrganizations()

                    });


            }
        });

    } catch (error) {
        console.log(error)
    }

}



</script>

<template>
    <div>
        <BaseBreadcrumb :title="page.title" :breadcrumbs="breadcrumbs"></BaseBreadcrumb>


        <v-row>
            <v-col cols="12" md="11">
                <UiParentCard variant="outlined">
                    <v-card-text class="text-right">
                        <template v-if="isLoggedInUserOwnsActionOrg">
                            <v-btn color="primary" class="mr-2" @click="setLogoDialog(true)">Update Logo</v-btn>
                            <v-btn color="primary" class="mr-2" @click="resetToken()">Reset Token</v-btn>
                            <v-btn color="primary" @click="setDialog(true)">Update Information</v-btn>
                        </template>
                        <v-sheet>
                            <v-dialog v-model="logoDialog" max-width="600">
                                <v-card>
                                    <v-card-text>
                                        <div class="d-flex justify-space-between">
                                            <h3 class="text-h3">Organization Logo</h3>
                                            <v-btn icon @click="setLogoDialog(false)" size="small" flat>
                                                <XIcon size="16" />
                                            </v-btn>
                                        </div>
                                    </v-card-text>
                                    <v-divider></v-divider>

                                    <v-card-text>
                                        <UpdatedBusinessLogo :closeDialog="setLogoDialog" />
                                    </v-card-text>
                                </v-card>
                            </v-dialog>
                            <v-dialog v-model="dialog" max-width="600">
                                <v-card>
                                    <v-card-text>
                                        <div class="d-flex justify-space-between">
                                            <h3 class="text-h3">Account Details</h3>
                                            <v-btn icon @click="setDialog(false)" size="small" flat>
                                                <XIcon size="16" />
                                            </v-btn>
                                        </div>
                                    </v-card-text>
                                    <v-divider></v-divider>

                                    <v-card-text>
                                        <UpdatedBusinessDetail :closeDialog="setDialog" />
                                    </v-card-text>
                                </v-card>
                            </v-dialog>
                        </v-sheet>
                    </v-card-text>
                    <v-card-text class="">
                        <VRow v-if="getOrganizations">
                            <VCol cols='12' md="12" class="text-center" v-if="getOrganizations?.media">

                                <a :href="getOrganizations?.media?.fullUrl" target="_blank">
                                    <v-avatar size="140">
                                        <img :src="getOrganizations?.media?.fullUrl" height="140" alt="image" />
                                    </v-avatar>
                                </a>
                            </VCol>
                            <VCol cols='12' md="4">
                                <label class="text-subtitle-1">Business Name</label>
                                <p class="text-body-1"> {{ `${getOrganizations?.name ? getOrganizations?.name : ''}` }}
                                </p>
                            </VCol>
                            <VCol cols='12' md="4">
                                <label class="text-subtitle-1">Address</label>
                                <p class="text-body-1"> {{ `${getOrganizations?.address ? getOrganizations?.address :
            ''}` }}</p>
                            </VCol>
                            <VCol cols='12' md="4">
                                <label class="text-subtitle-1">Bio</label>
                                <p class="text-body-1"> {{ `${getOrganizations?.bio ? getOrganizations?.bio : ''}` }}
                                </p>
                            </VCol>
                            <VCol cols='12' md="4">
                                <label class="text-subtitle-1">Industry</label>
                                <p class="text-body-1"> {{ `${getOrganizations?.industry ?
            getOrganizations?.industry?.name
            : ''}` }}</p>
                            </VCol>
                            <VCol cols='12' md="4">
                                <label class="text-subtitle-1">Country</label>
                                <p class="text-body-1"> {{ `${getOrganizations ?
            getOrganizations?.country?.name :
            ''}` }}</p>
                            </VCol>
                            <VCol cols='12' md="4">
                                <label class="text-subtitle-1">Creation Date</label>
                                <p class="text-body-1"> {{
            `${getOrganizations ? formatDate(getOrganizations?.createdAt) : ''}` }}
                                </p>
                            </VCol>
                            <VCol cols='12' md="4">
                                <label class="text-subtitle-1">RC Number</label>
                                <p class="text-body-1"> {{ `${getOrganizations?.rcNumber ? getOrganizations?.rcNumber :
            ''}`
                                    }}</p>
                            </VCol>
                            <VCol cols='12' md="4">
                                <label class="text-subtitle-1">ISPON Number</label>
                                <p class="text-body-1"> {{ `${getOrganizations?.ispon ? getOrganizations?.ispon : ''}`
                                    }}</p>
                            </VCol>
                            <VCol cols='12' md="4" v-if="isLoggedInUserOwnsActionOrg">
                                <label class="text-subtitle-1">Invite Token</label>
                                <p class="text-body-1"> {{ `${getOrganizations?.token}` }}</p>
                            </VCol>
                        </VRow>
                    </v-card-text>
                </UiParentCard>

            </v-col>

        </v-row>
    </div>
</template>
<style lang="scss">
.customTab {
    min-height: 68px;
}
</style>
