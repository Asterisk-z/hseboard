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




const page = ref({ title: 'Account Setting' });
const breadcrumbs = ref([
    {
        text: 'Dashboard',
        disabled: false,
        href: '/dashboard'
    },
    {
        text: 'Account Setting',
        disabled: true,
        href: '#'
    }
]);

const route = useRoute()
const authStore = useAuthStore();
const organizationStore = useOrganizationStore();
const accountStore = useAccountStore();


onMounted(() => {
    accountStore.getUserDetail()
});



const getAuthUser = computed(() => (authStore.loggedUser));
const getUserDetail = computed(() => (accountStore.userDetail));
const getActiveOrg = computed(() => (organizationStore.getActiveOrg()));
const isLoggedInUserOwnsActionOrg = computed(() => (getAuthUser.value?.id == getActiveOrg.value?.user_id));


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
                                <label class="text-subtitle-1">Full Name</label>
                                <p class="text-body-1"> {{ `${getUserDetail?.full_name}` }}</p>
                                <!-- <p class="text-body-1"> {{ `${moment(getCompletedInspection?.inspection?.start_date).format('MMMM Do YYYY, h:mm a')}` }}</p> -->
                            </VCol>
                            <VCol cols='12' md="4">
                                <label class="text-subtitle-1">Account Email</label>
                                <p class="text-body-1"> {{ `${getUserDetail?.email}` }}</p>
                            </VCol>
                            <VCol cols='12' md="4">
                                <label class="text-subtitle-1">Phone Number</label>
                                <p class="text-body-1"> {{ `${getUserDetail?.phone}` }}</p>
                            </VCol>
                            <VCol cols='12' md="4">
                                <label class="text-subtitle-1">Creation Date</label>
                                <p class="text-body-1"> {{ `${moment(getUserDetail?.created_at).format('MMMM Do YYYY, h:mm a')}` }}</p>
                            </VCol>
                            <VCol cols='12' md="4">
                                <label class="text-subtitle-1">Account Role</label>
                                <p class="text-body-1"> {{ `${getUserDetail?.account_role?.description}` }}</p>
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

