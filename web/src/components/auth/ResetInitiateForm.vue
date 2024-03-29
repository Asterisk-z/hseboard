<script setup lang="ts">
import { ref, computed } from 'vue';
import { router } from '@/router';
import { useAuthStore } from '@/stores/auth';
import { Form } from 'vee-validate';



// toastWrapper.success('tell me something')
const authStore = useAuthStore();
const valid = ref(false);

const username = ref('');

const emailRules = ref([(v: string) => !!v || 'E-mail is required', (v: string) => /.+@.+\..+/.test(v) || 'E-mail must be valid']);

const validate = async (values: any, { setErrors }: any) => {
    try {
        
        const request = await authStore.resetPasswordInitiate({ email: username.value }).catch((error : any) => { throw error });
        // if (request?.message == 'success') {
        //     router.push('/auth/otp-reset-password');
        // }

    } catch (error) {
        console.log(error)
    }
}

</script>

<template>
    <div>
        
        <Form @submit="validate" v-slot="{ isSubmitting }" class="mt-5">

            <v-label class="text-subtitle-1 font-weight-semibold pb-2 text-lightText">Email</v-label>
            <VTextField v-model="username" :rules="emailRules" class="mb-8" required hide-details="auto" label="Email">
            </VTextField>

            <v-btn size="large" :loading="isSubmitting" color="primary" :disabled="valid" block type="submit" flat>Request OTP</v-btn>

        </Form>
    </div>
</template>
