<script setup lang="ts">
import { ref, computed } from 'vue';
import { router } from '@/router';
import { useAuthStore } from '@/stores/auth';
import { Form } from 'vee-validate';



// toastWrapper.success('tell me something')
const authStore = useAuthStore();
const valid = ref(false);

const passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]).{8,}$/;
const showPassword = ref(false);

const username = ref(localStorage.getItem('hse_reset_email'));
const password = ref('');
const confirmPassword = ref('');

const fieldRules = Object.freeze({
    password: [
        (v: string) => !!v || 'Password is required',
        (v: string) => (v && passwordRegex.test(password.value)) || 'Password must be uppercase, lowercase, numbers and 8 characters',
    ],
    confirmPassword: [
        (v: string) => !!v || 'Password is required',
        (v: string) => (v && v == password.value) || 'Password does not march',
        (v: string) => (v && passwordRegex.test(confirmPassword.value)) || 'Password most be uppercase, lowercase, numbers and 8 characters',
    ],
    emailAddress: [(v: string) => !!v || 'E-mail is required', (v: string) => /.+@.+\..+/.test(v) || 'E-mail must be valid'],
})

const validate = async (values: any, { setErrors }: any) => {
    try {
        let setPassword = '';
        if (password.value == confirmPassword.value) {
            setPassword = confirmPassword.value
        }

        const request = await authStore.resetPasswordSet({ "email": username.value, "password": setPassword }).catch((error: any) => { throw error });
        if (request?.message == 'success') {
            router.push('/auth/login');
        }

    } catch (error) {
        console.log(error)
    }
}


</script>

<template>
    <div>

        <Form @submit="validate" v-slot="{ isSubmitting }" class="mt-5">

            <v-label class="text-subtitle-1 font-weight-semibold pb-2 text-lightText">Email</v-label>
            <VTextField v-model="username" :rules="fieldRules.emailAddress" :disabled="true" class="mb-8 text-black" required hide-details="auto"
                label="Email">
            </VTextField>
                
            <VRow  class="mb-3">
            
                <VCol cols="12" md="12">
                    <v-label class="text-subtitle-1 font-weight-medium pb-2">Password</v-label>
                    <VTextField :type="showPassword ? 'text' : 'password'" v-model="password" :rules="fieldRules.password" required variant="outlined"   label="Password" :append-inner-icon="showPassword ? 'mdi-eye' : 'mdi-eye-off'" @click:append-inner="showPassword = !showPassword"></VTextField>
                </VCol>
                <VCol cols="12" md="12">
                    <v-label class="text-subtitle-1 font-weight-medium pb-2">Confirm Password</v-label>
                    <VTextField :type="showPassword ? 'text' : 'password'" v-model="confirmPassword" :rules="fieldRules.confirmPassword" required variant="outlined"   label="Password" :append-inner-icon="showPassword ? 'mdi-eye' : 'mdi-eye-off'" @click:append-inner="showPassword = !showPassword"></VTextField>
                </VCol>
            </VRow>

            <v-btn size="large" :loading="isSubmitting" color="primary" :disabled="valid" block type="submit" flat>Set
                Password</v-btn>

        </Form>
    </div>
</template>
