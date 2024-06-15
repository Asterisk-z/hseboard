<script setup lang="ts">
import { ref, onMounted, computed, watch } from 'vue';

import { useOrganizationStore } from '@/stores/organizationStore';

const organizationStore = useOrganizationStore();

const props = defineProps({
    closeDialog: {
        type: Function,
        required: true,
        default(rawProps: boolean) {
            return rawProps;
        }
    },
});

const getOrganizations: any = computed(() => (organizationStore.organization));

const valid = ref(true);
const formContainer = ref('')

const loading = ref(false);
const setLoading = (value: boolean) => {
    loading.value = value;
}

const fields = ref({
    rcNumber: "",
    ispon: "",
    email: "",
    phone: "",
    businessName: "",
    address: "",
    bio: "",
});



const fieldRules: any = ref({
    rcNumber: [
        (v: string) => !!v || 'Field is required',
    ],
    ispon: [
        (v: string) => !!v || 'Field is required',
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
        fields.value.rcNumber = getOrganizations.value.rcNumber
        fields.value.ispon = getOrganizations.value.ispon
    }
)


onMounted(() => {

    if (getOrganizations.value) {
        fields.value.businessName = getOrganizations.value.name
        fields.value.address = getOrganizations.value.address
        fields.value.bio = getOrganizations.value.bio
        fields.value.rcNumber = getOrganizations.value.rcNumber
        fields.value.ispon = getOrganizations.value.ispon
    } else {
        organizationStore.getOrganizations()
    }

});

const save = async (e: any) => {
    e.preventDefault();

    try {
        setLoading(true)

        const values = { ...fields.value }

        let objectValues = {
            "businessName": values?.businessName,
            "address": values?.address,
            "bio": values?.bio,
            "rcNumber": values?.rcNumber,
            "ispon": values?.ispon,
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
            props.closeDialog(false)

            fields.value.businessName = "";
            fields.value.address = "";
            fields.value.bio = "";


        }

        setLoading(false)
        props.closeDialog(false)



    } catch (error) {
        console.log(error)
        setLoading(false)
        props.closeDialog(false)
    }

}



</script>

<template>

    <VForm v-model="valid" ref="formContainer" fast-fail lazy-validation @submit.prevent="save" class="py-1">
        <VRow class="mt-5 mb-3">
            

            <VCol cols="12" md="12">
                <v-label class="text-subtitle-1 font-weight-medium pb-1">RC
                    Number</v-label>
                <VTextField type="text" v-model="fields.rcNumber" :rules="fieldRules.rcNumber" required
                    variant="outlined" label="RC Number" :color="fields.rcNumber.length > 2 ? 'success' : 'primary'">
                </VTextField>
            </VCol>
            <VCol cols="12" md="12">
                <v-label class="text-subtitle-1 font-weight-medium pb-1">ISPON
                    Number</v-label>
                <VTextField type="text" v-model="fields.ispon" :rules="fieldRules.ispon" required variant="outlined"
                    label="ISPON" :color="fields.ispon.length > 2 ? 'success' : 'primary'">
                </VTextField>
            </VCol>
            <VCol cols="12" md="12">
                <v-label class="text-subtitle-1 font-weight-medium pb-1">Business
                    Name</v-label>
                <VTextField type="text" v-model="fields.businessName" :rules="fieldRules.businessName" required
                    variant="outlined" label="Business Name"
                    :color="fields.businessName.length > 2 ? 'success' : 'primary'">
                </VTextField>
            </VCol>
            <VCol cols="12" md="12">
                <v-label class="text-subtitle-1 font-weight-medium pb-1">Address</v-label>
                <VTextField type="text" v-model="fields.address" :rules="fieldRules.address" required variant="outlined"
                    label="Address" :color="fields.address.length > 2 ? 'success' : 'primary'">
                </VTextField>
            </VCol>

            <VCol cols="12" md="12">
                <v-label class="text-subtitle-1 font-weight-medium pb-1">Bio</v-label>
                <VTextField type="text" v-model="fields.bio" :rules="fieldRules.bio" required variant="outlined"
                    label="Bio" :color="fields.bio.length > 2 ? 'success' : 'primary'">
                </VTextField>
            </VCol>


            <VCol cols="12" lg="12" class="text-right">
                <v-btn color="error" @click="props.closeDialog(false)" variant="text">Cancel</v-btn>

                <v-btn color="primary" type="submit" :loading="loading" :disabled="!valid" @click="save">
                    <span v-if="!loading">
                        Submit
                    </span>
                    <clip-loader v-else :loading="loading" color="white" :size="'25px'"></clip-loader>
                </v-btn>

            </VCol>
        </VRow>
    </VForm>

</template>
<style lang="scss"></style>
