<script setup lang="ts">
import { ref, onMounted, computed, watch } from 'vue';
import { useRoute } from 'vue-router'
import { router } from '@/router';

import BaseBreadcrumb from '@/components/shared/BaseBreadcrumb.vue';
import UiParentCard from '@/components/shared/UiParentCard.vue';
import { useOrganizationStore } from '@/stores/organizationStore';
import { useAccountStore } from '@/stores/accountStore';
import { useAuthStore } from '@/stores/auth';
import { useFormatter } from '@/composables/formatter';
import moment from 'moment'

import Swal from 'sweetalert2'
import {
    CheckIcon,
} from 'vue-tabler-icons';




const page = ref({ title: 'Account Setting' });
const breadcrumbs = ref([
    {
        text: 'Dashboard',
        disabled: false,
        href: '/dashboard'
    },
    {
        text: 'Account Setting',
        disabled: true,
        href: '#'
    }
]);

const { formatDate } = useFormatter();
const route = useRoute()
const authStore = useAuthStore();
const organizationStore = useOrganizationStore();
const accountStore = useAccountStore();

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
const passwordDialog = ref(false);
const setPasswordDialog = (value: boolean) => {
    passwordDialog.value = value;
}

const logoDialog = ref(false);
const setLogoDialog = (value: boolean) => {
    logoDialog.value = value;
}

const passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]).{8,}$/;
const showPassword = ref(false);

const fields = ref({
    lastName: "",
    firstName: "",
    email: "",
    phone: "",
    ispon: "",
    password: "",
    currentPassword: "",
    confirmPassword: "",
    end_date: "",
    images: [],
    start_date: ""
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

const files = ref(null as any)
const previewImage = ref(null as any)

const selectImage = (image: any) => {

    fields.value.images = image.target.files;
    files.value = image.target.files;
    previewImage.value = [];

    for (let index = 0; index < files.value.length; index++) {
        const element = files.value[index];
        previewImage.value.push(URL.createObjectURL(element) as string)
    }

}

onMounted(() => {
    accountStore.getUserDetail()
});

const getAuthUser: any = computed(() => (authStore.loggedUser));
const getUserDetail: any = computed(() => (accountStore.userDetail));
const getActiveOrg: any = computed(() => (organizationStore.getActiveOrg()));
const isLoggedInUserOwnsActionOrg: any = computed(() => (getAuthUser.value?.id == getActiveOrg.value?.user_id));


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

        setTimeout(() => {
            setLoading(false)
            setDialog(false)
        }, 3000)

    } catch (error) {
        console.log(error)
        setLoading(false)
        setDialog(false)
    }

}


const uploadLogo = async (e: any) => {
    e.preventDefault();

    try {
        setLoading(true)

        const formData = new FormData();
        formData.append('image', files.value[0])

        const resp = await accountStore.uploadLogo(formData)
            .catch((error: any) => {
                console.log(error)
                throw error
            })
            .then((resp: any) => {
                return resp
            });

        if (resp?.message == 'success') {
            setLoading(false)
            setLogoDialog(false)

            fields.value.images = [];
            files.value = []
            previewImage.value = []
        }

        setLoading(false)
        setLogoDialog(false)



    } catch (error) {
        console.log(error)
        setLoading(false)
        setLogoDialog(false)
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

</script>

<template>
    <div>
        <BaseBreadcrumb :title="page.title" :breadcrumbs="breadcrumbs"></BaseBreadcrumb>


        <v-row>
            <v-col cols="12" md="11">
                <UiParentCard variant="outlined">
                    <v-card-text class="text-right">

                        <v-sheet>
                            <v-btn color="primary" class="mr-2" @click="setLogoDialog(true)">Update Photo</v-btn>
                            <v-btn color="primary" class="mr-2" @click="setDialog(true)">Update Information</v-btn>
                            <v-btn color="primary" class="mr-2" @click="setPasswordDialog(true)">Update
                                Password</v-btn>

                            <v-dialog v-model="logoDialog" max-width="600">
                                <v-card>
                                    <v-card-text>
                                        <div class="d-flex justify-space-between">
                                            <h3 class="text-h3">Photo</h3>
                                            <v-btn icon @click="setLogoDialog(false)" size="small" flat>
                                                <XIcon size="16" />
                                            </v-btn>
                                        </div>
                                    </v-card-text>
                                    <v-divider></v-divider>

                                    <v-card-text>

                                        <VForm v-model="valid" ref="formContainer" fast-fail lazy-validation
                                            @submit.prevent="uploadLogo" class="py-1">
                                            <VRow class="mt-5 mb-3">

                                                <VCol cols="12" md="12">

                                                    <v-file-input :show-size="1000" color="deep-purple-accent-4"
                                                        label="Photo" placeholder="Select your photo"
                                                        prepend-icon="mdi-paperclip" variant="outlined" accept="image/*"
                                                        @change="selectImage">
                                                    </v-file-input>

                                                    <div v-if="previewImage">
                                                        <VRow>
                                                            <VCol cols="4" v-for="image in previewImage" :key="image">

                                                                <div>
                                                                    <img class="preview my-3" :src="image" alt=""
                                                                        style="max-width: 200px;" />
                                                                </div>
                                                            </VCol>
                                                        </VRow>
                                                    </div>
                                                </VCol>

                                                <VCol cols="12" lg="12" class="text-right">
                                                    <v-btn color="error" @click="setLogoDialog(false)"
                                                        variant="text">Cancel</v-btn>

                                                    <v-btn color="primary" type="submit" :loading="loading"
                                                        :disabled="!valid" @click="uploadLogo">
                                                        <span v-if="!loading">
                                                            Upload
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

                            <v-dialog v-model="passwordDialog" max-width="600">
                                <v-card>
                                    <v-card-text>
                                        <div class="d-flex justify-space-between">
                                            <h3 class="text-h3">Update Password</h3>
                                            <v-btn icon @click="setPasswordDialog(false)" size="small" flat>
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
                                                    <v-label class="text-subtitle-1 font-weight-medium pb-1">Current
                                                        Password</v-label>
                                                    <VTextField :type="showPassword ? 'text' : 'password'"
                                                        v-model="fields.currentPassword"
                                                        :rules="fieldRules.currentPassword" required variant="outlined"
                                                        label="Current Password"
                                                        :append-inner-icon="showPassword ? 'mdi-eye' : 'mdi-eye-off'"
                                                        @click:append-inner="showPassword = !showPassword">
                                                    </VTextField>
                                                </VCol>


                                                <VCol cols="12" md="6">
                                                    <v-label class="text-subtitle-1 font-weight-medium pb-2">New
                                                        Password</v-label>
                                                    <VTextField :type="showPassword ? 'text' : 'password'"
                                                        v-model="fields.password" :rules="fieldRules.password" required
                                                        variant="outlined" label="Password"
                                                        :append-inner-icon="showPassword ? 'mdi-eye' : 'mdi-eye-off'"
                                                        @click:append-inner="showPassword = !showPassword"></VTextField>
                                                </VCol>
                                                <VCol cols="12" md="6">
                                                    <v-label class="text-subtitle-1 font-weight-medium pb-2">Confirm
                                                        New Password</v-label>
                                                    <VTextField :type="showPassword ? 'text' : 'password'"
                                                        v-model="fields.confirmPassword"
                                                        :rules="fieldRules.confirmPassword" required variant="outlined"
                                                        label="Password"
                                                        :append-inner-icon="showPassword ? 'mdi-eye' : 'mdi-eye-off'"
                                                        @click:append-inner="showPassword = !showPassword"></VTextField>
                                                </VCol>
                                                <VCol cols="12" lg="12" class="text-right">
                                                    <v-btn color="error" @click="setPasswordDialog(false)"
                                                        variant="text">Cancel</v-btn>

                                                    <v-btn color="primary" type="submit" :loading="loading"
                                                        :disabled="!valid" @click="save">
                                                        <span v-if="!loading">
                                                            Update
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

                                                <VCol cols="12" md="6">
                                                    <v-label class="text-subtitle-1 font-weight-medium pb-1">Last
                                                        Name</v-label>
                                                    <VTextField type="text" v-model="fields.lastName"
                                                        :rules="fieldRules.lastName" required variant="outlined"
                                                        label="Last Name">
                                                    </VTextField>
                                                </VCol>
                                                <VCol cols="12" md="6">
                                                    <v-label class="text-subtitle-1 font-weight-medium pb-1">First
                                                        Name</v-label>
                                                    <VTextField type="text" v-model="fields.firstName"
                                                        :rules="fieldRules.firstName" required variant="outlined"
                                                        label="First Name">
                                                    </VTextField>
                                                </VCol>

                                                <VCol cols="12" md="6">
                                                    <v-label
                                                        class="text-subtitle-1 font-weight-medium pb-1">Email</v-label>
                                                    <VTextField type="email" v-model="fields.email"
                                                        :rules="fieldRules.email" required variant="outlined"
                                                        label="Email">
                                                    </VTextField>
                                                </VCol>

                                                <VCol cols="12" md="6">
                                                    <v-label class="text-subtitle-1 font-weight-medium pb-1">Phone
                                                        Number</v-label>
                                                    <VTextField type="number" v-model="fields.phone"
                                                        :rules="fieldRules.phone" required variant="outlined"
                                                        label="Phone Number">
                                                    </VTextField>
                                                </VCol>

                                                <VCol cols="12" md="12">
                                                    <v-label class="text-subtitle-1 font-weight-medium pb-1">ISPON
                                                        Number</v-label>
                                                    <VTextField type="text" v-model="fields.ispon"
                                                        :rules="fieldRules.ispon" required variant="outlined"
                                                        label="ISPON Number">
                                                    </VTextField>
                                                </VCol>



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
                        <VRow>
                            <VCol cols='12' md="12" class="text-center" v-if="getUserDetail?.media">

                                <a :href="getUserDetail?.media?.full_url" target="_blank">
                                    <v-avatar size="140">
                                        <img :src="getUserDetail?.media?.full_url" height="140" alt="image" />
                                    </v-avatar>
                                </a>
                            </VCol>
                            <VCol cols='12' md="4">
                                <label class="text-subtitle-1">Full Name</label>
                                <p class="text-body-1"> {{ `${getUserDetail?.full_name}` }}</p>
                                <!-- <p class="text-body-1"> {{ `${moment(getCompletedInspection?.inspection?.start_date).format('MMMM Do YYYY, h:mm a')}` }}</p> -->
                            </VCol>
                            <VCol cols='12' md="4">
                                <label class="text-subtitle-1">Account Email</label>
                                <p class="text-body-1"> {{ `${getUserDetail?.email}` }}</p>
                            </VCol>
                            <VCol cols='12' md="4">
                                <label class="text-subtitle-1">Phone Number</label>
                                <p class="text-body-1"> {{ `${getUserDetail?.phone}` }}</p>
                            </VCol>
                            <VCol cols='12' md="4">
                                <label class="text-subtitle-1">Creation Date</label>
                                <p class="text-body-1"> {{ formatDate(getUserDetail?.created_at) }}</p>
                            </VCol>
                            <VCol cols='12' md="4">
                                <label class="text-subtitle-1">Account Role</label>
                                <p class="text-body-1"> {{ `${getUserDetail?.account_role?.description}` }}</p>
                            </VCol>
                            <VCol cols='12' md="4">
                                <label class="text-subtitle-1">ISPON Number</label>
                                <p class="text-body-1"> {{ `${getUserDetail?.ispon}` }}</p>
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
