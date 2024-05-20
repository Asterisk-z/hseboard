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



const getAuthUser: any = computed(() => (authStore.loggedUser));
const getOrganizations: any = computed(() => (organizationStore.organization));
const getActiveOrg: any = computed(() => (organizationStore.getActiveOrg()));
const isLoggedInUserOwnsActionOrg: any = computed(() => (getAuthUser.value?.id == getActiveOrg.value?.user_id));


const valid = ref(true);
const formContainer = ref('')
const loading = ref(false);
const setLoading = (value: boolean) => {
    loading.value = value;
}
const dialog = ref(false);
const setDialog = (value: boolean) => {
    dialog.value = value;
}

const fields = ref({
    lastName: "",
    firstName: "",
    email: "",
    phone: "",
    businessName: "",
    address: "",
    bio: "",
});


const fieldRules: any = ref({
    lastName: [
        (v: string) => !!v || 'Last Name is required',
    ],
    firstName: [
        (v: string) => !!v || 'First Name is required',
    ],
    email: [
        (v: string) => !!v || 'Email  is required',
    ],
    businessName: [
        (v: string) => !!v || 'Business Name is required',
    ],
    address: [
        (v: string) => !!v || 'Address is required',
    ],
    bio: [
        (v: string) => !!v || 'Bio  is required',
    ],
    phone: [
        (v: string) => !!v || 'Phone Number is required',
        (v: string) => (v && v.length == 11) || 'Phone Number must be 11 characters',
    ],
})
watch(
    () => getOrganizations.value,
    (value) => {

        fields.value.businessName = getOrganizations.value.name
        fields.value.address = getOrganizations.value.address
        fields.value.bio = getOrganizations.value.bio


    }
)

const resetToken = async () => {


    try {

        let objectValues = {
            "organization_id": getActiveOrg.value?.uuid,
        }

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
                        // if (resp.message == 'success') {
                        //     router.push(`/hse-audit`)
                        // }

                    });


            }
        });

    } catch (error) {
        console.log(error)
    }

}

const save = async (e: any) => {
    e.preventDefault();

    try {
        setLoading(true)

        const values = { ...fields.value }

        let objectValues = {
            "businessName": values?.businessName,
            "address": values?.address,
            "bio": values?.bio,
        }

        const resp = await organizationStore.updateDetails(objectValues)
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

            fields.value.businessName = "";
            fields.value.address = "";
            fields.value.bio = "";

            organizationStore.getOrganizations()


        }

        setLoading(false)
        setDialog(false)



    } catch (error) {
        console.log(error)
        setLoading(false)
        setDialog(false)
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
                            <v-btn color="primary" class="mr-2" @click="resetToken()">Reset Token</v-btn>
                            <v-btn color="primary" @click="setDialog(true)">Update Information</v-btn>
                        </template>
                        <v-sheet>
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

                                        <VForm v-model="valid" ref="formContainer" fast-fail lazy-validation
                                            @submit.prevent="save" class="py-1">
                                            <VRow class="mt-5 mb-3">

                                                <VCol cols="12" md="12">
                                                    <v-label class="text-subtitle-1 font-weight-medium pb-1">Business
                                                        Name</v-label>
                                                    <VTextField type="text" v-model="fields.businessName"
                                                        :rules="fieldRules.businessName" required variant="outlined"
                                                        label="Business Name"
                                                        :color="fields.businessName.length > 2 ? 'success' : 'primary'">
                                                    </VTextField>
                                                </VCol>
                                                <VCol cols="12" md="12">
                                                    <v-label
                                                        class="text-subtitle-1 font-weight-medium pb-1">Address</v-label>
                                                    <VTextField type="text" v-model="fields.address"
                                                        :rules="fieldRules.address" required variant="outlined"
                                                        label="Address"
                                                        :color="fields.address.length > 2 ? 'success' : 'primary'">
                                                    </VTextField>
                                                </VCol>

                                                <VCol cols="12" md="12">
                                                    <v-label
                                                        class="text-subtitle-1 font-weight-medium pb-1">Bio</v-label>
                                                    <VTextField type="text" v-model="fields.bio" :rules="fieldRules.bio"
                                                        required variant="outlined" label="Bio"
                                                        :color="fields.bio.length > 2 ? 'success' : 'primary'">
                                                    </VTextField>
                                                </VCol>

                                                <!-- <VCol cols="12" md="6">
                                                    <v-label class="text-subtitle-1 font-weight-medium pb-1">Phone
                                                        Number</v-label>
                                                    <VTextField type="number" v-model="fields.phone"
                                                        :rules="fieldRules.phone" required variant="outlined"
                                                        label="Phone Number"
                                                        :color="fields.phone.length > 2 ? 'success' : 'primary'">
                                                    </VTextField>
                                                </VCol> -->



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
                    </v-card-text>
                    <v-card-text class="">
                        <VRow v-if="getOrganizations">
                            <VCol cols='12' md="4">
                                <label class="text-subtitle-1">Business Name</label>
                                <p class="text-body-1"> {{ `${getOrganizations?.name ? getOrganizations?.name : ''}` }}
                                </p>
                                <!-- <p class="text-body-1"> {{ `${moment(getCompletedInspection?.inspection?.start_date).format('MMMM Do YYYY, h:mm a')}` }}</p> -->
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
                                <p class="text-body-1"> {{ `${getOrganizations?.industry?.name ?
            getOrganizations?.industry?.name
            : ''}` }}</p>
                            </VCol>
                            <VCol cols='12' md="4">
                                <label class="text-subtitle-1">Country</label>
                                <p class="text-body-1"> {{ `${getOrganizations?.country?.name ?
            getOrganizations?.country?.name :
            ''}` }}</p>
                            </VCol>
                            <VCol cols='12' md="4">
                                <label class="text-subtitle-1">Creation Date</label>
                                <p class="text-body-1"> {{
            `${getOrganizations?.created_at ? moment(getOrganizations?.created_at).format(`MMMM
                                    Do
                                    YYYY, h:
                                    mm a`) : ''}` }}</p>
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
