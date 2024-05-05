<script setup lang="ts">
import { ref, onMounted, computed, watch } from 'vue';
import { useRoute } from 'vue-router'
import { router } from '@/router';

import BaseBreadcrumb from '@/components/shared/BaseBreadcrumb.vue';
import UiParentCard from '@/components/shared/UiParentCard.vue';
import { useOrganizationStore } from '@/stores/organizationStore';
import { useAccountStore } from '@/stores/accountStore';
import { useAuthStore } from '@/stores/auth';
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


onMounted(() => {
    organizationStore.getOrganizations()
});



const getAuthUser : any  = computed(() => (authStore.loggedUser));
const getOrganizations : any  = computed(() => (organizationStore.organization));
const getActiveOrg : any  = computed(() => (organizationStore.getActiveOrg()));
const isLoggedInUserOwnsActionOrg : any  = computed(() => (getAuthUser.value?.id == getActiveOrg.value?.user_id));


</script>

<template>
    <div>
        <BaseBreadcrumb :title="page.title" :breadcrumbs="breadcrumbs"></BaseBreadcrumb>


        <v-row>
            <v-col cols="12" md="11">
                <UiParentCard variant="outlined">
                    <v-card-text class="text-right">
                        <v-btn color="primary">Update Information</v-btn>
                        <!-- <h1 class="text-uppercase mb-3"> {{ getCompletedInspection?.inspection?.facility_name }}</h1> -->
                        <!-- <h1 class="text-uppercase mb-3"> {{ getCompletedInspection?.inspection?.inspection_template?.description }}</h1> -->
                        <!-- <h2 class="text-uppercase mb-3">Organization : {{ getCompletedInspection?.inspection?.recipient_organization?.name }}</h2> -->
                    </v-card-text>
                    <v-card-text class="">
                        <VRow>
                            <VCol cols='12' md="4">
                                <label class="text-subtitle-1">Business Name</label>
                                <p class="text-body-1"> {{ `${getOrganizations?.name}` }}</p>
                                <!-- <p class="text-body-1"> {{ `${moment(getCompletedInspection?.inspection?.start_date).format('MMMM Do YYYY, h:mm a')}` }}</p> -->
                            </VCol>
                            <VCol cols='12' md="4">
                                <label class="text-subtitle-1">Address</label>
                                <p class="text-body-1"> {{ `${getOrganizations?.address}` }}</p>
                            </VCol>
                            <VCol cols='12' md="4">
                                <label class="text-subtitle-1">Bio</label>
                                <p class="text-body-1"> {{ `${getOrganizations?.bio}` }}</p>
                            </VCol>
                            <VCol cols='12' md="4">
                                <label class="text-subtitle-1">Industry</label>
                                <p class="text-body-1"> {{ `${getOrganizations?.industry?.name}` }}</p>
                            </VCol>
                            <VCol cols='12' md="4">
                                <label class="text-subtitle-1">Country</label>
                                <p class="text-body-1"> {{ `${getOrganizations?.country?.name}` }}</p>
                            </VCol>
                            <VCol cols='12' md="4">
                                <label class="text-subtitle-1">Creation Date</label>
                                <p class="text-body-1"> {{ `${moment(getOrganizations?.created_at).format('MMMM Do YYYY,  h: mm a')}` }}</p>
                            </VCol>
                            <VCol cols='12' md="4">
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
