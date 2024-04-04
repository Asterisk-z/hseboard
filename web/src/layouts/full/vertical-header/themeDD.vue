<script setup lang="ts">

import { ref } from 'vue';
import { MoonIcon, SunIcon } from 'vue-tabler-icons';

import { useTheme } from 'vuetify';
import { useCustomizerStore } from '@/stores/customizer';

const customizer = useCustomizerStore();
const theme = useTheme();
// themes color options
const themeColors = ref([
    {
        name: 'DARK_AQUA_THEME',
        bg: 'themeDarkAqua'
    },
    {
        name: 'AQUA_THEME',
        bg: 'themeAqua'
    },
]);

const toggleTheme = (event : any) => {
    customizer.CHANGE_THEME()
}

</script>
<template>
    <!-- ---------------------------------------------- -->
    <!-- notifications DD -->
    <!-- ---------------------------------------------- -->
    <v-menu :close-on-content-click="false">
        <template v-slot:activator="{ props }">
            <v-btn icon variant="text" color="primary" class="custom-hover-primary" v-bind="props" @click="toggleTheme">

                <MoonIcon stroke-width="1.5" size="22" v-if="customizer.actTheme != 'DARK_AQUA_THEME'" />
                <SunIcon stroke-width="1.5" size="22" v-else />

            </v-btn>
        </template>
    </v-menu>
</template>
