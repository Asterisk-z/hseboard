<script setup lang="ts">
import { ref, computed } from 'vue';
import { router } from '@/router';
import { useAuthStore } from '@/stores/auth';
import { Form } from 'vee-validate';

/*Social icons*/
import google from '@/assets/images/svgs/google-icon.svg';


// toastWrapper.success('tell me something')
const authStore = useAuthStore();
const checkbox = ref(false);
const showPassword = ref(false);
const valid = ref(false);

const password = ref('');
const username = ref('');

const passwordRules = ref([
    (v: string) => !!v || 'Password is required',
    // (v: string) => (v && v.length >= 10) || 'Password must be less than 10 characters'
]);

const emailRules = ref([(v: string) => !!v || 'E-mail is required', (v: string) => /.+@.+\..+/.test(v) || 'E-mail must be valid']);

const validate = async (values: any, { setErrors }: any) => {
    const request = await authStore.login(username.value, password.value).catch((error) => setErrors({ apiError: error }));
    if (request?.message == 'success') {
        console.log('redirect to dash')
        // router.push('/dashboard');

        setInterval(() => {
            window.location.href = `${import.meta.env.VITE_API_WEB}dashboard`
        }, 2000)

    }
}

</script>

<template>
    <div>
        <v-row class="d-flex mb-3">
            <v-col cols="12" sm="12" class="pr-2">
                <v-btn variant="outlined" size="large" class="border text-subtitle-1" block>
                    <img :src="google" height="16" class="mr-2" alt="google" />
                    <span class="d-sm-flex d-none mr-1">Sign in with</span>Google
                </v-btn>
            </v-col>
        </v-row>
        <div class="d-flex align-center text-center mb-6">
            <div class="text-h6 w-100 px-5 font-weight-regular auth-divider position-relative">
                <span class="bg-surface px-5 py-3 position-relative">OR</span>
            </div>
        </div>
        <Form @submit="validate" v-slot="{ errors, isSubmitting }" class="mt-5">

            <v-label class="text-subtitle-1 font-weight-semibold pb-2 text-lightText">Email</v-label>
            <VTextField v-model="username" :rules="emailRules" class="mb-8" required hide-details="auto" label="Email">
            </VTextField>
            <v-label class="text-subtitle-1 font-weight-semibold pb-2 text-lightText">Password</v-label>

            <VTextField class="pwdInput" hide-details="auto" :type="showPassword ? 'text' : 'password'"
                v-model="password" :rules="passwordRules" required variant="outlined" label="Password"
                :append-inner-icon="showPassword ? 'mdi-eye' : 'mdi-eye-off'"
                @click:append-inner="showPassword = !showPassword"></VTextField>

            <div class="d-flex flex-wrap align-center my-3 ">
                <div class="ml-sm-auto">
                    <RouterLink to="/auth/initiate-reset-password"
                        class="text-primary text-decoration-none text-body-1 opacity-1 font-weight-medium">Forgot
                        Password ?</RouterLink>
                </div>
            </div>
            <v-btn size="large" :loading="isSubmitting" color="primary" :disabled="valid" block type="submit" flat>Log
                In</v-btn>

        </Form>
    </div>
</template>
