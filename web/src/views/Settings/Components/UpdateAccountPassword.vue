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

const passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]).{8,}$/;
const showPassword = ref(false);

const fields = ref({
    password: "",
    currentPassword: "",
    confirmPassword: "",
});

const fieldRules: any = ref({
    currentPassword: [
        (v: string) => !!v || 'Password is required',
    ],
    password: [
        (v: string) => !!v || 'Password is required',
        (v: string) => (v && passwordRegex.test(fields.value.password)) || 'Password must be uppercase, lowercase, numbers and 8 characters',
    ],
    confirmPassword: [
        (v: string) => !!v || 'Password is required',
        (v: string) => (v && v == fields.value.password) || 'Password does not march',
        (v: string) => (v && passwordRegex.test(fields.value.confirmPassword)) || 'Password most be uppercase, lowercase, numbers and 8 characters',
    ],
})

const save = async (e: any) => {
    e.preventDefault();

    try {
        setLoading(true)

        const values = { ...fields.value }

        let objectValues = {
            "password": values?.password,
            "currentPassword": values?.currentPassword,
            "confirmPassword": values?.confirmPassword,
        }

        const resp = await accountStore.updateDetail(objectValues)
            .catch((error: any) => {
                console.log(error)
                throw error
            })
            .then((resp: any) => {
                return resp
            });

        setTimeout(() => {
            setLoading(false)
            props.closeDialog(false)
        }, 3000)

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
                <v-label class="text-subtitle-1 font-weight-medium pb-1">Current
                    Password</v-label>
                <VTextField :type="showPassword ? 'text' : 'password'" v-model="fields.currentPassword"
                    :rules="fieldRules.currentPassword" required variant="outlined" label="Current Password"
                    :append-inner-icon="showPassword ? 'mdi-eye' : 'mdi-eye-off'"
                    @click:append-inner="showPassword = !showPassword">
                </VTextField>
            </VCol>

            <VCol cols="12" md="6">
                <v-label class="text-subtitle-1 font-weight-medium pb-2">New
                    Password</v-label>
                <VTextField :type="showPassword ? 'text' : 'password'" v-model="fields.password"
                    :rules="fieldRules.password" required variant="outlined" label="Password"
                    :append-inner-icon="showPassword ? 'mdi-eye' : 'mdi-eye-off'"
                    @click:append-inner="showPassword = !showPassword"></VTextField>
            </VCol>
            <VCol cols="12" md="6">
                <v-label class="text-subtitle-1 font-weight-medium pb-2">Confirm
                    New Password</v-label>
                <VTextField :type="showPassword ? 'text' : 'password'" v-model="fields.confirmPassword"
                    :rules="fieldRules.confirmPassword" required variant="outlined" label="Password"
                    :append-inner-icon="showPassword ? 'mdi-eye' : 'mdi-eye-off'"
                    @click:append-inner="showPassword = !showPassword"></VTextField>
            </VCol>
            <VCol cols="12" lg="12" class="text-right">
                <v-btn color="error" @click="props.closeDialog(false)" variant="text">Cancel</v-btn>

                <v-btn color="primary" type="submit" :loading="loading" :disabled="!valid" @click="save">
                    <span v-if="!loading">
                        Update
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
