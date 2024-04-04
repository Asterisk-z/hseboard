<script setup lang="ts">
import { RouterView } from 'vue-router';
import VerticalSidebarVue from './vertical-sidebar/VerticalSidebar.vue';
import VerticalHeaderVue from './vertical-header/VerticalHeader.vue';
import Customizer from './customizer/Customizer.vue';
import { useCustomizerStore } from '../../stores/customizer';
import { pl, zhHans } from 'vuetify/locale'
const customizer = useCustomizerStore();

</script>

<template>
    <v-locale-provider >
        <v-app
            :theme="customizer.actTheme"
            :class="[
                customizer.actTheme,
                true ? 'verticalLayout' : ''
            ]"
        >
            <!-- <Customizer /> -->
            <VerticalSidebarVue />
            <VerticalHeaderVue />

            <v-main>
                <v-container fluid class="page-wrapper pb-sm-15 pb-10">
                    <div>
                        <RouterView />
                        <!-- <v-btn
                            class="customizer-btn"
                            size="large"
                            icon
                            variant="flat"
                            color="primary"
                            @click.stop="customizer.SET_CUSTOMIZER_DRAWER(!customizer.Customizer_drawer)"
                        >
                            <SettingsIcon />
                        </v-btn> -->
                    </div>
                </v-container>
            </v-main>
        </v-app>
    </v-locale-provider>
</template>
