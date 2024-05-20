<script setup lang="ts">

import { ref } from 'vue';
import { MoonIcon, SunIcon } from 'vue-tabler-icons';

import { useTheme } from 'vuetify';
import { useAuthStore } from '@/stores/auth';

const auth: any = useAuthStore();
const loading = ref(false);

const setLoading = (value: boolean) => {
    loading.value = value;
}


const refreshApp = (event: any) => {

    if (auth.user || auth.hse_tok_passer) {
        setLoading(true)
        // setTimeout(() => {
        auth.refresh();
        // }, 10000)
    }
}

</script>
<template>
    <!-- ---------------------------------------------- -->
    <!-- notifications DD -->
    <!-- ---------------------------------------------- -->
    <v-menu :close-on-content-click="false">
        <template v-slot:activator="{ props }">
            <v-btn color="primary" v-bind="props" @click="refreshApp" flat :loading="loading" :disabled="loading">


                <span v-if="!loading">Refresh</span>
                <clip-loader v-else :loading="loading" color="white" :size="'25px'"></clip-loader>


            </v-btn>
        </template>
    </v-menu>
</template>
