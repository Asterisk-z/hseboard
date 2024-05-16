<script setup lang="ts">
import { ref, onMounted, computed } from 'vue';
import { HelpIcon, ChevronDownIcon } from 'vue-tabler-icons';
import { useOrganizationStore } from '@/stores/organizationStore';
import { useAuthStore } from '@/stores/auth';

const organizationStore = useOrganizationStore();
const authStore = useAuthStore();

const getAuthUser: any = computed(() => (authStore.loggedUser));

const getOrganizations : any  = computed(() => (organizationStore.loggedUserOrgs));
const getActiveOrganizations : any  = computed(() => (organizationStore.getActiveOrg()));
const changeActiveOrganization = (orgId: string) => {
    // organizationStore.changeActiveOrg(orgId)
    organizationStore.getActiveOrganization({ 'organization_id': orgId })

}

    // onMounted(() => {
    //     organizationStore.getActiveOrganization()
    // });



</script>

<template>
    <!-- ---------------------------------------------- -->
    <!-- mega menu DD -->
    <!-- ---------------------------------------------- -->
    <div>

        <template v-if="[null, 'null'].includes(organizationStore?.active)">
            <v-btn variant="text" color="primary">{{ `Individual Account` }}</v-btn>
        </template>
        <template v-else>
            <v-menu open-on-hover :close-on-content-click="false">
                <template v-slot:activator="{ props }">
                    <v-btn class="hidden-sm-and-down" rounded="sm" variant="text" color="primary" v-bind="props"> Change
                        Organization
                        <ChevronDownIcon size="16" class="mt-1 ml-1" />
                    </v-btn>
                </template>
                <v-sheet width="300" min-height="200" elevation="10" rounded="md" class="pa-4 pb-0">
                    <perfect-scrollbar style="height: calc(100vh - 150px); max-height: 150px">
                        <div>
                            <v-row>
                                <v-col cols="12" lg="12" class="d-flex py-0">
                                    <div class="pa-4 pb-0 pr-0">    
                                        <v-row v-if="getOrganizations.length > 0">
                                            <v-col cols="12" lg="12" v-for="(item, i) in getOrganizations" :key="i">
                                                <router-link :to="'#'" class="text-decoration-none custom-text-primary"
                                                    @click="changeActiveOrganization(item.uuid)">
                                                    <div class="d-flex align-center">
                                                        <v-avatar size="45" color="grey100" rounded="md">
                                                            <v-img :src="item.avatar" width="24" height="24"
                                                                :alt="item.avatar" />
                                                        </v-avatar>
                                                        <div class="ml-3">
                                                            <h6
                                                                class="text-subtitle-1 mb-1 textPrimary font-weight-semibold custom-title">
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
                                    </div>

                                    <v-divider vertical></v-divider>
                                </v-col>
                            </v-row>
                        </div>
                    </perfect-scrollbar>
                </v-sheet>
            </v-menu>
            <v-btn variant="text" color="primary">{{ `Active Organization : ${getActiveOrganizations?.name}` }}</v-btn>
        </template>

    </div>
</template>
