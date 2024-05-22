<script setup lang="ts">
import { ref, onMounted, computed, watch } from 'vue';
import { useRoute } from 'vue-router'
import { router } from '@/router';

import BaseBreadcrumb from '@/components/shared/BaseBreadcrumb.vue';
import UiParentCard from '@/components/shared/UiParentCard.vue';
import { useOrganizationStore } from '@/stores/organizationStore';
import { useInspectionStore } from '@/stores/inspectionStore';
import { useReportStore } from '@/stores/reportStore';
import { useBillingStore } from '@/stores/billingStore';
import { useAuthStore } from '@/stores/auth';
import moment from 'moment'
import Swal from 'sweetalert2'
import {
    CheckIcon,
} from 'vue-tabler-icons';


const page = ref({ title: 'Billing Receipt' });
const breadcrumbs = ref([
    {
        text: 'Dashboard',
        disabled: false,
        href: '/dashboard'
    },
    {
        text: 'Billing',
        disabled: false,
        href: '/billing'
    },
    {
        text: 'Receipt',
        disabled: true,
        href: '#'
    }
]);

const route = useRoute()
const authStore = useAuthStore();
const organizationStore = useOrganizationStore();
const inspectionStore = useInspectionStore();
const reportStore = useReportStore();
const billingStore = useBillingStore();


onMounted(() => {
    billingStore.getTransaction(route.params.transaction_id as string)
});


const computedIndex = (index: any) => ++index;

const getAuthUser: any = computed(() => (authStore.loggedUser));
const getTransaction: any = computed(() => (billingStore.transaction));
const getActiveOrg: any = computed(() => (organizationStore.getActiveOrg()));
const isLoggedInUserOwnsActionOrg: any = computed(() => (getAuthUser.value?.id == getActiveOrg.value?.user_id));




const loading = ref(false);









</script>

<template>
    <div>
        <BaseBreadcrumb :title="page.title" :breadcrumbs="breadcrumbs"></BaseBreadcrumb>


        <v-row v-if="getTransaction">
            <v-col cols="12" md="11">

                <UiParentCard variant="outlined">
                    <v-card-text class="text-center">
                        <h2 class="text-uppercase mb-3">HSE Payment Receipt </h2>
                    </v-card-text>
                    <v-card-text class="">
                        <VRow>

                            <VCol cols='12' class="text-left">
                                <!-- <h2 class="text-uppercase mb-3">Inspection SUMMARY</h2> -->

                                <v-table>
                                    <thead>
                                        <tr>
                                            <th class="text-left" colspan="4">

                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        <tr>
                                            <td colspan="2" rowspan="3">{{ getTransaction?.organization?.name }}</td>
                                            <td>Paid By</td>
                                            <td>{{ `${getTransaction?.user?.full_name}` }}</td>
                                        </tr>
                                        <tr>
                                            <td>Paid Date</td>
                                            <td>{{ ` ${moment(getTransaction?.created_at)}`
                                                }}</td>
                                        </tr>
                                        <tr>
                                            <td>Payment Status</td>
                                            <td>{{ `${getTransaction?.status}` }}</td>
                                        </tr>
                                        <tr>
                                            <th class="text-left">
                                                S/N
                                            </th>
                                            <th class="text-left" colspan="2">
                                                Feature Name
                                            </th>
                                            <th class="text-left">
                                                Cost
                                            </th>
                                        </tr>

                                        <tr v-for="(feat, index) in getTransaction?.features" :key="feat">
                                            <td>{{ computedIndex(index)
                                                }}</td>
                                            <td colspan="2">{{
            `${feat?.org_feature?.feature_name}`
        }}</td>
                                            <td>{{
                `${feat?.currency?.symbol}
                                                ${feat?.cost}` }}
                                            </td>
                                        </tr>

                                        <tr>
                                            <td></td>
                                            <td></td>
                                            <td>Total</td>
                                            <td>{{ `${getTransaction?.currency?.symbol} ${getTransaction?.amount}` }}
                                            </td>
                                        </tr>

                                    </tbody>
                                </v-table>

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
