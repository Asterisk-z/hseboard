<script setup lang="ts">
import { ref, computed } from 'vue';
import { AppsIcon, CalendarIcon, MailIcon, MessagesIcon } from 'vue-tabler-icons';
import AppsLink from './AppsLink.vue';
import { useOrganizationStore } from '@/stores/organizationStore';
import { useAuthStore } from '@/stores/auth';

const organizationStore = useOrganizationStore();
const authStore = useAuthStore();


const open = ref(['Apps']);
</script>

<template>
    <div class="pt-6">
        <v-list v-model:opened="open" class="right-sidebar">
            <v-list-group value="Organization">
                <template v-slot:activator="{ props }">
                    <v-list-item v-bind="props">
                        <template v-slot:prepend>
                            <AppsIcon width="21" stroke-width="1.5" />
                        </template>
                        <h5 class="text-subtitle-1 ml-2">Organization</h5>
                    </v-list-item>
                </template>
                <v-list-item class="pl-6 pb-6">
                    <div>
                        <AppsLink />
                    </div>
                </v-list-item>
            </v-list-group>
        </v-list>
    </div>
</template>

<style scoped>
.right-sidebar .v-list-group__items .v-list-item {
    padding-inline-start: 26px !important;
}
</style>
