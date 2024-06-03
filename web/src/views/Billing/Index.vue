<script setup lang="ts">
import { ref, onMounted, computed, watch } from 'vue';
import { useRoute } from 'vue-router'
import { router } from '@/router';

import { VDataTable } from 'vuetify/labs/VDataTable'
import BaseBreadcrumb from '@/components/shared/BaseBreadcrumb.vue';
import UiParentCard from '@/components/shared/UiParentCard.vue';
import { useOrganizationStore } from '@/stores/organizationStore';
import { useAuthStore } from '@/stores/auth';
import { useBillingStore } from '@/stores/billingStore';
import moment from 'moment'

const page = ref({ title: 'Billing' });
const breadcrumbs = ref([
    {
        text: 'Dashboard',
        disabled: false,
        href: 'dashboard'
    },
    {
        text: 'Billings',
        disabled: true,
        href: '#'
    }
]);
const tab = ref(null);
const route = useRoute()
const authStore = useAuthStore();
const organizationStore = useOrganizationStore();
const billingStore = useBillingStore();


onMounted(() => {
    billingStore.getAllTransaction()
    billingStore.getAllOrganizationFeatures()
    billingStore.getAllPlans()
    billingStore.getAllFeatures()
    billingStore.getAllCurrencies()
});





const computedIndex = (index: any) => ++index;


const getAuthUser: any = computed(() => (authStore.loggedUser));
const getAllTransaction: any = computed(() => (billingStore.all_transactions));
const getAllOrganizationFeatures: any = computed(() => (billingStore.all_org_feature));
const getAllPlans: any = computed(() => (billingStore.plans));
const getAllFeatures: any = computed(() => (billingStore.all_features));
const getAllCurrencies: any = computed(() => (billingStore.currencies));

const getActiveOrg: any = computed(() => (organizationStore.getActiveOrg()));
const isLoggedInUserOwnsActionOrg: any = computed(() => (getAuthUser.value?.id == getActiveOrg.value?.user_id));

const selectedCategory = ref('all');
const includeFiles = ref(true);
const enabled = ref(true);

const delivery = ref('free');
const selectedFeatures = ref([]);
const selectedFeaturesTotal = ref(0);

const total = () => {
    let totalValue: number = 0;
    selectedFeatures.value.forEach((feature: any) => {
        totalValue = parseFloat(feature?.cost) + totalValue
    });
    selectedFeaturesTotal.value = totalValue;
}


const valid = ref(true);;
const formContainer = ref()
const loading = ref(false);
const setLoading = (value: boolean) => {
    loading.value = value;
}


const addAuditorsDialog = ref(false);
const setAddAuditorsDialog = (value: boolean) => {
    addAuditorsDialog.value = value;
}
const fields = ref({

    plan: '',
    currency: '',

    leadRepresentative: '',
    representatives: [],

    organization_id: getActiveOrg.value?.uuid,
});

const fieldRules: any = ref({
    plan: [
        (v: number) => !!v || 'Field is Required',
    ],
    currency: [
        (v: number) => !!v || 'Field is Required',
    ],
    leadRepresentative: [
        (v: number) => !!v || 'Field is Required',
    ],
    representatives: [
        (v: number) => !!v || 'Field is Required',
    ],
    leadInvestigator: [
        (v: number) => !!v || 'Field is Required',
    ],
    teamMember: [
        (v: number) => !!v || 'Field is Required',
    ],
    groupChatName: [
        (v: string) => !!v || 'Field is Required',
    ],
})



const convertedTotal = ref(0);
const payCurrency = ref('$');
const estimatedTotal = ref(0);
const convertTotal = () => {
    convertedTotal.value = selectedFeaturesTotal.value;
    console.log(selectedCurrency())
    console.log(selectedPlan())
    estimatedTotal.value = selectedFeaturesTotal.value * selectedCurrency() * selectedPlan();
}


const selectedPlan = () => {
    let plan = getAllPlans.value?.filter((item: any) => item?.id == fields.value.plan)
    // console.log(plan[0])
    return plan?.length > 0 ? plan[0]?.noOfMonths : 1;
}
const selectedCurrency = () => {
    let currency = getAllCurrencies.value?.filter((item: any) => item?.id == fields.value.currency)
    // console.log(currency[0])
    payCurrency.value = currency?.length > 0 ? currency[0]?.symbol : "$"
    return currency?.length > 0 ? currency[0]?.exchangeToBase : 1;
}

const initiatePayment = async (e: any) => {
    e.preventDefault();

    try {
        setLoading(true)

        const values = { ...fields.value }



        let objectValues = {
            "organization_id": getActiveOrg.value?.uuid,
            "plan_id": values.plan,
            "currency_id": values?.currency,
            "amount": convertedTotal.value,
            "features": selectedFeatures.value.map((item: any) => item?.uuid),
        }


        const resp = await billingStore.initiatePayment(objectValues)
            .catch((error: any) => {
                console.log(error)
                throw error
            })
            .then((resp: any) => {
                return resp
            });

        if (resp?.message == 'success') {
            setLoading(false)
            setAddAuditorsDialog(false)
            billingStore.getAllFeatures();
            // console.log(resp.data?.data?.data?.link)
            if (resp.data?.data?.status == 'success') {
                window.open(resp.data?.data?.data?.link, '_blank');

            }
        }

        setLoading(false)
        setAddAuditorsDialog(false)



    } catch (error) {
        console.log(error)
        setLoading(false)
        setAddAuditorsDialog(false)
    }

}



const transaction_search = ref('');

const transaction_headers = ref([
    {
        key: 'sn',
        title: 'SN',
        sortable: false,
    },
    {
        key: 'id',
        title: 'Transaction',
        value: (item: any): string => `HSE-SUB-${item.id}12 `,
    },
    {
        key: 'status',
        title: 'Status',
        value: (item: any): string => `${item.status}`
    },
    {
        key: 'amount',
        title: 'Amount',
        value: (item: any): string => `${item?.currency?.symbol} ${item.amount}`
    },
    {
        key: 'features',
        title: 'Total Features',
        sortable: false,
        value: (item: any): string => (`${item.features?.length}`)
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


const viewSelectedFeatures: any = ref([]);
const viewSelectedSubscription: any = ref('');
const viewDialog = ref(false);
const setViewDialog = (value: boolean, item = [], uuid: string = '') => {
    viewDialog.value = value;
    viewSelectedFeatures.value = item
    viewSelectedSubscription.value = uuid
    if (value == false) {
        // selectItem({})

    }
}


const unlocked_search = ref('');

const unlocked_headers = ref([
    {
        key: 'sn',
        title: 'SN',
        sortable: false,
    },
    {
        key: 'featureName',
        title: 'Feature Name',
        value: (item: any): string => `${item.featureName} `,
    },
    {
        key: 'isActive',
        title: 'Status',
        value: (item: any): string => `${item.isActive}`
    },
    {
        key: 'endDate',
        title: 'Expiry Date',
        value: (item: any): string => `${moment(item.endDate).format('MMMM Do YYYY, h:mm a')}`
    },
    {
        key: 'createdAt',
        title: 'Date Created',
        value: (item: any): string => `${moment(item.createdAt).format('MMMM Do YYYY, h:mm a')}`
    }
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
                <UiParentCard title="">

                    <v-row class="justify-center">
                        <!--Account Settings tabs-->
                        <v-col cols="12" md="12">
                            <v-card elevation="10" class=" " rounded="md">
                                <v-tabs v-model="tab" bg-color="transparent" min-height="70" height="70" color="primary"
                                    v-if="isLoggedInUserOwnsActionOrg">
                                    <v-tab value="OrganizationFeatures" class="text-medium-emphasis">
                                        <ArticleIcon class="mr-2" size="20" />Unlocked Features
                                    </v-tab>
                                    <v-tab value="Notification" class="text-medium-emphasis">
                                        <BellIcon class="mr-2" size="20" />Unlock Features
                                    </v-tab>
                                    <v-tab value="Account" class="text-medium-emphasis">
                                        <UserCircleIcon class="mr-2" size="20" /> All Subscriptions
                                    </v-tab>
                                </v-tabs>
                                <v-divider></v-divider>
                                <v-card-text class="pa-sm-6 pa-3 pb-sm-6 pb-6">
                                    <v-window v-model="tab">
                                        <v-window-item value="OrganizationFeatures">

                                            <v-row no-gutters>
                                                <v-col cols="12" sm="12">
                                                    <h1 class="pa-4">Organization Features</h1>
                                                </v-col>
                                                <v-col cols="12">

                                                    <v-card>


                                                        <template v-slot:text>
                                                            <v-text-field v-model="unlocked_search" label="Search"
                                                                prepend-inner-icon="mdi-magnify" variant="outlined"
                                                                hide-details single-line></v-text-field>
                                                        </template>

                                                        <VDataTable :headers="unlocked_headers"
                                                            :items="getAllOrganizationFeatures"
                                                            :search="unlocked_search" item-key="name" items-per-page="5"
                                                            item-value="fat" show-select>



                                                            <template v-slot:item.sn="{ index }">
                                                                {{ computedIndex(index) }}
                                                            </template>


                                                            <template v-slot:item.isActive="{ item }">
                                                                <div class="">
                                                                    <v-chip
                                                                        :color="item.selectable.isActive ? 'green' : 'orange'"
                                                                        :text="item.selectable.isActive ? 'Active' : 'Not Active'"
                                                                        class="text-uppercase" size="small"
                                                                        label></v-chip>
                                                                </div>
                                                            </template>



                                                        </VDataTable>
                                                    </v-card>

                                                </v-col>
                                            </v-row>
                                        </v-window-item>
                                        <v-window-item value="Notification">

                                            <v-row>
                                                <v-col cols="12">
                                                    <v-card>

                                                        <v-col cols="12" sm="12">
                                                            <h1 class="pa-4">Features</h1>
                                                            <h5 class="text-h5 pa-4" v-if="selectedFeatures.length > 0">
                                                                Total : ${{
            `${selectedFeaturesTotal}` }}</h5>
                                                        </v-col>
                                                        <v-row v-for="(feature) in getAllFeatures" :key="feature"
                                                            class="m-5">
                                                            <!-- <template> -->
                                                            <v-col cols="12 " sm="12">
                                                                <h5 class="text-h5 mb-1">{{ feature.description }}</h5>
                                                            </v-col>
                                                            <v-col cols="12 " sm="4"
                                                                v-for="(sub_feature) in feature.subFeatures"
                                                                :key="sub_feature">
                                                                <v-checkbox v-model="selectedFeatures" @change="total"
                                                                    :value="{ 'cost': sub_feature?.orgFeature?.cost || sub_feature.cost, 'uuid': sub_feature.uuid }"
                                                                    hide-details
                                                                    :readonly="sub_feature?.orgFeature?.isActive">
                                                                    <template v-slot:label>
                                                                        <div class="border rounded-md w-100">
                                                                            <div class="py-5 px-4 border rounded-md "
                                                                                :class="{ 'bg-secondary text-black': sub_feature?.orgFeature?.isActive, 'bg-primary text-black': selectedFeatures.map((item: any) => item?.uuid).includes(sub_feature.uuid), }">
                                                                                <h6 class="text-h6 mb-1">{{
            sub_feature.description }}</h6>
                                                                                <span
                                                                                    class="d-block text-subtitle-1 font-weight-medium"
                                                                                    v-if="sub_feature?.orgFeature?.isActive">Running</span>

                                                                                <span
                                                                                    class="d-block text-subtitle-1 font-weight-medium">{{
            `${sub_feature.currency.symbol}
                                                                                    ${sub_feature?.orgFeature?.cost ||
            sub_feature.cost}` }}</span>
                                                                            </div>
                                                                        </div>
                                                                    </template>
                                                                </v-checkbox>

                                                            </v-col>
                                                            <!-- </template> -->
                                                        </v-row>

                                                    </v-card>


                                                    <v-row class="mt-3 px-4" v-if="selectedFeatures.length > 0">

                                                        <v-col cols="12">

                                                            <v-btn color="primary" @click="setAddAuditorsDialog(true)"
                                                                class="mr-2" v-if="isLoggedInUserOwnsActionOrg">Unlock
                                                                Features</v-btn>
                                                            <v-sheet>
                                                                <v-dialog v-model="addAuditorsDialog" max-width="800">
                                                                    <v-card>


                                                                        <v-card-text>

                                                                            <div class="d-flex justify-space-between">
                                                                                <h3 class="text-h3">Make Payment </h3>
                                                                                <v-btn icon
                                                                                    @click="setAddAuditorsDialog(false)"
                                                                                    size="small" flat>
                                                                                    <XIcon size="16" />
                                                                                </v-btn>
                                                                            </div>

                                                                        </v-card-text>
                                                                        <v-divider></v-divider>

                                                                        <v-card-text>

                                                                            <VForm v-model="valid" ref="formContainer"
                                                                                fast-fail lazy-validation
                                                                                @submit.prevent="initiatePayment"
                                                                                class="py-1">
                                                                                <VRow class="mt-5 mb-3">

                                                                                    <VCol cols="12" md="12">
                                                                                        <v-label
                                                                                            class="font-weight-medium pb-1">Select
                                                                                            Plan</v-label>
                                                                                        <VSelect v-model="fields.plan"
                                                                                            :items="getAllPlans"
                                                                                            @update:modelValue="convertTotal"
                                                                                            :rules="fieldRules.plan"
                                                                                            label="Select"
                                                                                            item-title="description"
                                                                                            item-value="id" single-line
                                                                                            variant="outlined"
                                                                                            class="text-capitalize">
                                                                                        </VSelect>
                                                                                    </VCol>
                                                                                    <VCol cols="12" md="12">
                                                                                        <v-label
                                                                                            class="font-weight-medium pb-1">Select
                                                                                            Currency</v-label>
                                                                                        <VSelect
                                                                                            v-model="fields.currency"
                                                                                            :items="getAllCurrencies"
                                                                                            @update:modelValue="convertTotal"
                                                                                            :rules="fieldRules.currency"
                                                                                            label="Select"
                                                                                            item-title="description"
                                                                                            item-value="id" single-line
                                                                                            variant="outlined"
                                                                                            class="text-capitalize">
                                                                                        </VSelect>
                                                                                    </VCol>

                                                                                    <VCol cols="12" md="12">
                                                                                        <p>Feature Cost : ${{
            `${selectedFeaturesTotal}`
        }}</p>
                                                                                        <p>Estimated Total Cost : {{
                `${payCurrency}${estimatedTotal}`
            }}
                                                                                        </p>
                                                                                    </VCol>



                                                                                    <VCol cols="12" lg="12"
                                                                                        class="text-right">
                                                                                        <v-btn color="error"
                                                                                            @click="setAddAuditorsDialog(false)"
                                                                                            variant="text">Cancel</v-btn>

                                                                                        <v-btn color="primary"
                                                                                            type="submit"
                                                                                            :loading="loading"
                                                                                            :disabled="!valid"
                                                                                            @click="initiatePayment">
                                                                                            <span v-if="!loading">
                                                                                                Unlock
                                                                                            </span>
                                                                                            <clip-loader v-else
                                                                                                :loading="loading"
                                                                                                color="white"
                                                                                                :size="'25px'"></clip-loader>
                                                                                        </v-btn>

                                                                                    </VCol>
                                                                                </VRow>
                                                                            </VForm>
                                                                        </v-card-text>
                                                                    </v-card>
                                                                </v-dialog>
                                                            </v-sheet>
                                                        </v-col>
                                                    </v-row>

                                                </v-col>
                                            </v-row>

                                        </v-window-item>
                                        <v-window-item value="Account">

                                            <v-row no-gutters>
                                                <v-col cols="12" sm="12">
                                                    <h1 class="pa-4">Subscription</h1>
                                                </v-col>
                                                <v-col cols="12">

                                                    <v-card>


                                                        <template v-slot:text>
                                                            <v-text-field v-model="transaction_search" label="Search"
                                                                prepend-inner-icon="mdi-magnify" variant="outlined"
                                                                hide-details single-line></v-text-field>
                                                        </template>

                                                        <VDataTable :headers="transaction_headers"
                                                            :items="getAllTransaction" :search="transaction_search"
                                                            item-key="name" items-per-page="5" item-value="fat"
                                                            show-select>

                                                            <template v-slot:item.action="{ item }">

                                                                <v-btn color="primary" dark flat
                                                                    @click="setViewDialog(true, item.selectable.features, item.selectable.uuid)">
                                                                    Details </v-btn>
                                                            </template>


                                                            <template v-slot:item.sn="{ index }">
                                                                {{ computedIndex(index) }}
                                                            </template>

                                                            <template v-slot:item.status="{ item }">
                                                                <div class="">
                                                                    <v-chip
                                                                        :color="item.selectable.status == 'success' ? 'green' : 'orange'"
                                                                        :text="item.selectable?.status"
                                                                        class="text-uppercase" size="small"
                                                                        label></v-chip>
                                                                </div>
                                                            </template>



                                                        </VDataTable>

                                                        <v-sheet>
                                                            <v-dialog v-model="viewDialog" max-width="800">
                                                                <v-card>


                                                                    <v-card-text>

                                                                        <div class="d-flex justify-space-between">
                                                                            <h3 class="text-h3">Features </h3>
                                                                            <v-btn icon
                                                                                @click="setViewDialog(false, [])"
                                                                                size="small" flat>
                                                                                <XIcon size="16" />
                                                                            </v-btn>
                                                                        </div>

                                                                    </v-card-text>
                                                                    <v-divider></v-divider>

                                                                    <v-card-text>

                                                                        <VRow class="mt-5 mb-3">
                                                                            <VCol cols="12">

                                                                                <v-table>
                                                                                    <thead>
                                                                                        <tr>
                                                                                            <th class="text-left">
                                                                                                #
                                                                                            </th>
                                                                                            <th class="text-left">
                                                                                                Feature Name
                                                                                            </th>
                                                                                            <th class="text-left">
                                                                                                Cost
                                                                                            </th>
                                                                                        </tr>
                                                                                    </thead>
                                                                                    <tbody>

                                                                                        <tr v-for="(feat, index) in viewSelectedFeatures"
                                                                                            :key="feat">
                                                                                            <td>{{ computedIndex(index)
                                                                                                }}</td>
                                                                                            <td>{{
                                                                                                `${feat?.org_feature?.feature_name}`
                                                                                                }}</td>
                                                                                            <td>{{
                                                                                                `${feat?.currency.symbol}
                                                                                                ${feat?.cost}` }}
                                                                                            </td>
                                                                                        </tr>

                                                                                    </tbody>
                                                                                </v-table>


                                                                            </VCol>

                                                                            <VCol cols="12" lg="12" class="text-right">
                                                                                <v-btn color="primary"
                                                                                    @click="gotoRoute(`/billing/receipt/${viewSelectedSubscription}`)">Receipt</v-btn>
                                                                                <v-btn color="error"
                                                                                    @click="setViewDialog(false, [])"
                                                                                    variant="text">Close</v-btn>
                                                                            </VCol>
                                                                        </VRow>
                                                                    </v-card-text>
                                                                </v-card>
                                                            </v-dialog>
                                                        </v-sheet>

                                                    </v-card>

                                                </v-col>
                                            </v-row>
                                        </v-window-item>
                                    </v-window>
                                </v-card-text>
                            </v-card>
                        </v-col>
                    </v-row>
                </UiParentCard>
            </v-col>
        </v-row>
    </div>
</template>
