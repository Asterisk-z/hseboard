<script setup lang="ts">
import { ref, onMounted, computed, watch } from 'vue';
import { useRoute } from 'vue-router'
import { router } from '@/router';

import BaseBreadcrumb from '@/components/shared/BaseBreadcrumb.vue';
import UpdateAccountDetail from '@/views/Settings/Components/UpdateAccountDetail.vue';
import UpdateAccountLogo from '@/views/Settings/Components/UpdateAccountLogo.vue';
import UpdateAccountPassword from '@/views/Settings/Components/UpdateAccountPassword.vue';
import UiParentCard from '@/components/shared/UiParentCard.vue';
import { useAccountStore } from '@/stores/accountStore';
import { useFormatter } from '@/composables/formatter';


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
const accountStore = useAccountStore();

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


onMounted(() => {
    accountStore.getUserDetail()
});

const getUserDetail: any = computed(() => (accountStore.userDetail));






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

                                        <UpdateAccountLogo :closeDialog="setLogoDialog" />

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

                                        <UpdateAccountPassword :closeDialog="setPasswordDialog" />

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

                                        <UpdateAccountDetail :closeDialog="setDialog" />

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
