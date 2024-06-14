<script setup lang="ts">
import { ref, computed } from 'vue';

// import img1 from '@/assets/images/svgs/icon-dd-chat.svg';
// import img2 from '@/assets/images/svgs/icon-dd-cart.svg';


import { useOrganizationStore } from '@/stores/organizationStore';
import { useAuthStore } from '@/stores/auth';


const organizationStore = useOrganizationStore();
const authStore = useAuthStore();

const getAuthUser: any = computed(() => (authStore.loggedUser));

const getOrganizations: any = computed(() => (organizationStore.loggedUserOrgs));
const getActiveOrganizations: any = computed(() => (organizationStore.getActiveOrg()));
const changeActiveOrganization = (orgId: string) => {

    organizationStore.getActiveOrganization({ 'organization_id': orgId })

}

</script>
<template>
    <!-- ---------------------------------------------- -->
    <!-- apps link -->
    <!-- ---------------------------------------------- -->
    <div>
        <template v-if="[null, 'null'].includes(organizationStore?.active)">
            <v-btn variant="text" color="primary">{{ `Individual Account` }}</v-btn>
        </template>
        <template v-else>
            <!-- <v-row>
                <v-col cols="12" lg="12" v-for="(item, i) in appsLink2" :key="i">
                    <router-link :to="'#'" class="text-decoration-none custom-text-primary">
                        <div class="d-flex align-center">
                            <v-avatar size="45" color="grey100" rounded="md">
                                <v-img :src="item.avatar" width="24" height="24" :alt="item.avatar" />
                            </v-avatar>
                            <div class="ml-3">
                                <h6 class="text-subtitle-1 mb-1 textPrimary font-weight-semibold custom-title">{{
                                    item.title
        }}
                                </h6>
                            </div>
                        </div>
                    </router-link>
                </v-col>
            </v-row> -->

            <v-row v-if="getOrganizations.length > 0">
                <v-col cols="12" lg="12" v-for="(item, i) in getOrganizations" :key="i">
                    <router-link :to="'#'" class="text-decoration-none custom-text-primary"
                        @click="changeActiveOrganization(item.uuid)">
                        <div class="d-flex align-center">
                            <v-avatar size="45" color="grey100" rounded="md">
                                <v-img :src="item.avatar" width="24" height="24" :alt="item.avatar" />
                            </v-avatar>
                            <div class="ml-3">
                                <h6 class="text-subtitle-1 mb-1 textPrimary font-weight-semibold custom-title">
                                    {{ item.name }}
                                </h6>
                                <p class="text-subtitle-2 textSecondary">{{
            item.industry?.name
                                    }}</p>
                            </div>
                        </div>
                    </router-link>
                </v-col>
            </v-row>
        </template>
    </div>

</template>
