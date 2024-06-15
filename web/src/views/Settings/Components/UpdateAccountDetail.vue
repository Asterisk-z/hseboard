<script setup lang="ts">
import { ref, onMounted, computed, watch } from 'vue';

import { useAccountStore } from '@/stores/accountStore';

const accountStore = useAccountStore();

const props = defineProps({
    closeDialog: {
        type: Function,
        required: true,
        default(rawProps: boolean) {
            return rawProps;
        }
    },
});

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
    ispon: "",
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
    ispon: [
        (v: string) => !!v || 'ISPON Number  is required',
    ],
    phone: [
        (v: string) => !!v || 'Phone Number is required',
        (v: string) => (v && v.length == 11) || 'Phone Number must be 11 characters',
    ],
})


const getUserDetail: any = computed(() => (accountStore.userDetail));


const save = async (e: any) => {
    e.preventDefault();

    try {
        setLoading(true)

        const values = { ...fields.value }

        let objectValues = {
            "lastName": values?.lastName,
            "firstName": values?.firstName,
            "email": values?.email,
            "ispon": values?.ispon,
            "phone": values?.phone,
        }

        const resp = await accountStore.updateDetail(objectValues)
            .catch((error: any) => {
                console.log(error)
                throw error
            })
            .then((resp: any) => {
                return resp
            });

        props.closeDialog(false)

    } catch (error) {
        console.log(error)
        setLoading(false)
        props.closeDialog(false)
    }

}



watch(
    () => getUserDetail.value,
    (value) => {
        fields.value.lastName = getUserDetail.value?.lastName
        fields.value.firstName = getUserDetail.value?.firstName
        fields.value.email = getUserDetail.value?.email
        fields.value.phone = getUserDetail.value?.phone
        fields.value.ispon = getUserDetail.value?.ispon
    }
)


onMounted(() => {

    if (getUserDetail.value) {
        fields.value.lastName = getUserDetail.value?.lastName
        fields.value.firstName = getUserDetail.value?.firstName
        fields.value.email = getUserDetail.value?.email
        fields.value.phone = getUserDetail.value?.phone
        fields.value.ispon = getUserDetail.value?.ispon
    } else {
        accountStore.getUserDetail()
    }

});

</script>

<template>


    <VForm v-model="valid" ref="formContainer" fast-fail lazy-validation @submit.prevent="save" class="py-1">
        <VRow class="mt-5 mb-3">

            <VCol cols="12" md="6">
                <v-label class="text-subtitle-1 font-weight-medium pb-1">Last
                    Name</v-label>
                <VTextField type="text" v-model="fields.lastName" :rules="fieldRules.lastName" required
                    variant="outlined" label="Last Name">
                </VTextField>
            </VCol>
            <VCol cols="12" md="6">
                <v-label class="text-subtitle-1 font-weight-medium pb-1">First
                    Name</v-label>
                <VTextField type="text" v-model="fields.firstName" :rules="fieldRules.firstName" required
                    variant="outlined" label="First Name">
                </VTextField>
            </VCol>

            <VCol cols="12" md="6">
                <v-label class="text-subtitle-1 font-weight-medium pb-1">Email</v-label>
                <VTextField type="email" v-model="fields.email" :rules="fieldRules.email" required variant="outlined"
                    label="Email">
                </VTextField>
            </VCol>

            <VCol cols="12" md="6">
                <v-label class="text-subtitle-1 font-weight-medium pb-1">Phone
                    Number</v-label>
                <VTextField type="number" v-model="fields.phone" :rules="fieldRules.phone" required variant="outlined"
                    label="Phone Number">
                </VTextField>
            </VCol>

            <VCol cols="12" md="12">
                <v-label class="text-subtitle-1 font-weight-medium pb-1">ISPON
                    Number</v-label>
                <VTextField type="text" v-model="fields.ispon" :rules="fieldRules.ispon" required variant="outlined"
                    label="ISPON Number">
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
<style lang="scss">
.customTab {
    min-height: 68px;
}
</style>
