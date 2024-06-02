<script setup lang="ts">
import user1 from '@/assets/images/profile/user-1.jpg';
import { ref, onMounted, computed, watch } from 'vue';
import { useAuthStore } from '@/stores/auth';

const authStore = useAuthStore();

const getAuthUser: any = computed(() => (authStore.loggedUser));

</script>

<template>
    <v-sheet>
        <!---Topbar Row-->
        <div class="d-flex gap-2 align-center pa-6">
            <!---User Avatar-->
            <v-avatar size="large">
                <img :src="getAuthUser?.media?.full_url" width="80" v-if="getAuthUser?.media" />
                <img :src="user1" alt="pro" width="54" v-else />
            </v-avatar>


            <v-badge class="badg-dotDetail" dot color="success"> </v-badge>
            <!---Name & Last seen-->
            <div>
                <h5 class="text-subtitle-1 font-weight-semibold text-capitalize">{{ getAuthUser.full_name }}</h5>
                <!-- <h6 class="text-subtitle-2"></h6> -->
            </div>
        </div>
    </v-sheet>
</template>
