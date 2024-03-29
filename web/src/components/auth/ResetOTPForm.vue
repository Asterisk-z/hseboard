<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import { router } from '@/router';
import { useRoute } from 'vue-router'
import { useAuthStore } from '@/stores/auth';
import { Form } from 'vee-validate';



// toastWrapper.success('tell me something')
const route = useRoute();
const authStore = useAuthStore();
const valid = ref(false);

const email = ref<string>('');
const signature = ref<string>('');
const type = ref<string>('');

onMounted(() => {
    email.value = route.query?.email || null
    signature.value = route.query?.signature || null
    type.value = route.query?.type || null
});

const otp = ref('');

const otpRules = ref([
    (v: string) => (v && v.length == 6) || 'OTP must be 6 characters',
]);

const validate = async (values: any, { setErrors }: any) => {
    try {

        const request = await authStore.resetPasswordOtp({ "email": email.value, "signature": signature.value, "type": type.value, "otp": otp.value }).catch((error: any) => { throw error });
        if (request?.message == 'success') {
            router.push('/auth/set-reset-password');
        }

    } catch (error) {
        console.log(error)
    }
}

</script>

<template>
    <div>

        <Form @submit="validate" v-slot="{ isSubmitting }" class="mt-5">

            <v-label class="text-subtitle-1 font-weight-semibold pb-2 text-lightText">Enter your 6 digits OTP code</v-label>
            <VTextField v-model="otp" :rules="otpRules" class="mb-8" required hide-details="auto" label="OTP">
            </VTextField>

            <v-btn size="large" :loading="isSubmitting" color="primary" :disabled="valid" block type="submit"
                flat>Validate OTP</v-btn>

        </Form>
    </div>
</template>
