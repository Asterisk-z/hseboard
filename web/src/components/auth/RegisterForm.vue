<script setup lang="ts">
import { ref, onMounted, computed } from 'vue';

import { Form } from 'vee-validate';
import { useAuthStore } from '@/stores/auth';
import { useOpenLinksStore } from '@/stores/openLinks';
/*Social icons*/
import google from '@/assets/images/svgs/google-icon.svg';


const authStore = useAuthStore();
const openLinks = useOpenLinksStore();

onMounted(() => {
    openLinks.getAccountTypes();
    openLinks.getIndustries();
    openLinks.getCountries();
});

const getAccountTypes = computed(() => (openLinks.accountTypes));
const getIndustries = computed(() => (openLinks.industries));
const getCountries = computed(() => (openLinks.countries));

// const accountTypess = getAccountTypes;
// const accountTypes = 
const checkbox = ref(false);
const valid = ref(true);


const password = ref('');
const email = ref('');
const select = ref('');
const fname = ref('');

// const passwordRules = ref([
//     (v: string) => !!v || 'Password is required',
//     (v: string) => (v && v.length <= 10) || 'Password must be less than 10 characters'
// ]);

const emailRules = ref([(v: string) => !!v || 'E-mail is required', (v: string) => /.+@.+\..+/.test(v) || 'E-mail must be valid']);

// const fnameRules = ref([
//     (v: string) => !!v || 'Name is required',
//     (v: string) => (v && v.length <= 10) || 'Name must be less than 10 characters'
// ]);
// const accountTypeRules = ref([
//     (v: string) => !!v || 'Name is required',
//     (v: string) => (v && v.length <= 10) || 'Name must be less than 10 characters'
// ]);


// const defaultForm = Object.freeze({
//   age: "",
// });
// const form = ref(Object.assign({}, defaultForm));
// const rules = ref({
//   age: [(val: any) => (val || "").length > 0 || "This field is required"],
// });
// const age = ref(["Ten", "Twenty", "Thirty"]);


function onInvalidSubmit() {
    console.log('invalied')
}



const showSecondFormValue = ref('corporate')

// const accountTypes = ref([
//     {   
//         'title' : 'Cooperate',
//         'value' : 'cooperate'
//     },
//     {   
//         'title' : 'Individual',
//         'value' : 'individual'
//     }]);


const passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]).{8,}$/;
const showPassword = ref(false);

const fields = ref({
    firstName: "",
    lastName: "",
    phoneNumber: "",
    emailAddress: "",
    accountType: "individual",
    password: "",
    confirmPassword: "",
});

const fieldRules = Object.freeze({
    firstName: [
        (v: string) => !!v || 'First Name is required',
    ],
    lastName: [
        (v: string) => !!v || 'Last Name is required',
    ],
    phoneNumber: [
        (v: string) => !!v || 'Phone Number is required',
        (v: string) => (v && v.length == 11) || 'Phone Number must be 11 characters',
    ],
    accountType: [
        (v: string) => !!v || 'Account Type is required',
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
    emailAddress: [(v: string) => !!v || 'E-mail is required', (v: string) => /.+@.+\..+/.test(v) || 'E-mail must be valid'],
})

        
const orgFields = ref({
    orgName: "",
    industry: "",
    country: "",
    orgBio: "",
    orgAddress: "",
});

const orgFieldRules = Object.freeze({
    orgName: [
        (v: string) => !!v || 'Organization Name is required',
    ],
    industry: [
        (v: string) => !!v || 'Industry is required',
    ],
    country: [
        (v: string) => !!v || 'Country is required',
    ],
    orgBio: [
        (v: string) => !!v || 'Bio is required',
    ],
    orgAddress: [
        (v: string) => !!v || 'Address is required',
    ],
})





function save(e: any, setErrors ?: any) {
    e.preventDefault();

    try {
        console.log(fields.value)
        console.log(orgFields.value)
        const values = { ...fields.value, ...orgFields.value }
        console.log(values)
        // const authStore = useAuthStore();
        // return authStore.login(email.value, password.value).catch((error) => setErrors({ apiError: error }));
    } catch (error) {
        setErrors({ apiError: error })
    }



}


</script>
<template>
    <v-row class="d-flex mb-6">
        <v-col cols="12" sm="12"  class="pr-2">
            <v-btn variant="outlined" size="large" class="border text-subtitle-1" block>
                <img :src="google" height="20" class="mr-2" alt="google" />
                <span class="d-sm-flex d-none mr-1">Sign up with</span>Google
            </v-btn>
        </v-col>
    </v-row>
    <div class="d-flex align-center text-center mb-6">
        <div class="text-h6 w-100 px-5 font-weight-regular auth-divider position-relative">
            <span class="bg-surface px-5 py-3 position-relative">OR</span>
        </div> 
    </div>
    

    <!-- <Form @submit="validate" v-model="valid" v-slot="{ errors, isSubmitting }" class="mt-5"> -->
    <VForm  v-model="valid" lazy-validation @submit.prevent="save"  @invalid-submit="onInvalidSubmit"  v-slot="{ errors, isSubmitting }">
        <VRow  class="mt-5 mb-3">
            
            <VCol cols="12" md="12">
                <v-label class="font-weight-medium pb-2">Account Type</v-label>
                <VSelect
                    v-model="fields.accountType"
                    :items="getAccountTypes"
                    :rules="fieldRules.accountType"
                    label="Select"
                    item-title="description"
                    item-value="name"
                    single-line
                    variant="outlined"
                    class="text-capitalize"
                ></VSelect>
            </VCol>
            
            <VCol cols="12" md="6">
                <v-label class="text-subtitle-1 font-weight-medium pb-2">First Name</v-label>
                <VTextField v-model="fields.firstName" :rules="fieldRules.firstName" required variant="outlined"   label="First Name"></VTextField>
            </VCol>
            <VCol cols="12" md="6">
                <v-label class="text-subtitle-1 font-weight-medium pb-2">Last Name</v-label>
                <VTextField v-model="fields.lastName" :rules="fieldRules.lastName" required variant="outlined"   label="Last Name"></VTextField>
            </VCol>
            <VCol cols="12" md="6">
                <v-label class="text-subtitle-1 font-weight-medium pb-2">Email Address</v-label>
                <VTextField v-model="fields.emailAddress" :rules="fieldRules.emailAddress" required variant="outlined"   label="Email Address"></VTextField>
            </VCol>
            <VCol cols="12" md="6">
                <v-label class="text-subtitle-1 font-weight-medium pb-2">Phone Number</v-label>
                <VTextField type="number" v-model="fields.phoneNumber" :rules="fieldRules.phoneNumber" required variant="outlined"   label="Phone Number"></VTextField>
            </VCol>
                
        </VRow>
        <VRow  class="mb-3" v-if="fields.accountType == showSecondFormValue">
            <VCol cols="12" md="12">
                <v-label class="text-subtitle-1 font-weight-medium pb-2">Organization Name</v-label>
                <VTextField v-model="orgFields.orgName" :rules="orgFieldRules.orgName" required variant="outlined"   label="Organization Name"></VTextField>
            </VCol>

            <VCol cols="12" md="6">
                <v-label class="font-weight-medium pd-2">Industry</v-label>
                <VSelect
                    v-model="orgFields.industry"
                    :items="getIndustries"
                    :rules="orgFieldRules.industry"
                    label="Select"
                    item-title="name"
                    item-value="id"
                    single-line
                    variant="outlined"
                ></VSelect>
            </VCol>
            <VCol cols="12" md="6">
                <v-label class="font-weight-medium pd-2">Country</v-label>
                <VSelect
                    v-model="orgFields.country"
                    :items="getCountries"
                    :rules="orgFieldRules.country"
                    label="Select"
                    item-title="name"
                    item-value="id"
                    single-line
                    variant="outlined"
                ></VSelect>
            </VCol>

            <VCol cols="12" md="6">
                <v-label class="text-subtitle-1 font-weight-medium pb-2">Organization Address</v-label>
                <VTextField v-model="orgFields.orgAddress" :rules="orgFieldRules.orgAddress" required variant="outlined"   label="Address"></VTextField>
            </VCol>
            <VCol cols="12" md="6">
                <v-label class="text-subtitle-1 font-weight-medium pb-2">Organization Bio</v-label>
                <VTextField v-model="orgFields.orgBio" :rules="orgFieldRules.orgBio" required variant="outlined"   label="Bio"></VTextField>
            </VCol>
        </VRow>
             
        <VRow  class="mb-3">
        
            <VCol cols="12" md="6">
                <v-label class="text-subtitle-1 font-weight-medium pb-2">Password</v-label>
                <VTextField :type="showPassword ? 'text' : 'password'" v-model="fields.password" :rules="fieldRules.password" required variant="outlined"   label="Password" :append-inner-icon="showPassword ? 'mdi-eye' : 'mdi-eye-off'" @click:append-inner="showPassword = !showPassword"></VTextField>
            </VCol>
            <VCol cols="12" md="6">
                <v-label class="text-subtitle-1 font-weight-medium pb-2">Confirm Password</v-label>
                <VTextField :type="showPassword ? 'text' : 'password'" v-model="fields.confirmPassword" :rules="fieldRules.confirmPassword" required variant="outlined"   label="Password" :append-inner-icon="showPassword ? 'mdi-eye' : 'mdi-eye-off'" @click:append-inner="showPassword = !showPassword"></VTextField>
            </VCol>
            <VCol cols="12">
                <div class=" d-sm-flex align-center mb-4">
                    <div class="ml-auto"><a href="javascript:void(0)" class="text-primary text-decoration-none">Forgot
                        password?</a></div>
                </div>
                <v-btn size="large" class="mt-2 submit-btn" color="primary" :disabled="!valid" @click="save" :loading="isSubmitting" block submit flat type="submit">Register</v-btn>
            </VCol>
            
        </VRow>
    </VForm>
        
    <!-- </Form> -->
</template>
