<script setup lang="ts">
import { ref, shallowRef } from 'vue';
import { useCustomizerStore } from '@/stores/customizer';
import { useAuthStore } from '@/stores/auth';
import { useOrganizationStore } from '@/stores/organizationStore';
import sidebarItems from './sidebarItem';
import userSidebarItem from './userSidebarItem';

import NavGroup from './NavGroup/index.vue';
import NavItem from './NavItem/index.vue';
import NavCollapse from './NavCollapse/NavCollapse.vue';
import Profile from './profile/Profile.vue';
import Logo from '../logo/Logo.vue';

const customizer = useCustomizerStore();
const organizationStore = useOrganizationStore();

const sidebarMenu = shallowRef(sidebarItems);
const userSidebarMenu = shallowRef(userSidebarItem);
</script>

<template>
    <v-navigation-drawer left v-model="customizer.Sidebar_drawer" elevation="0" rail-width="75" mobile-breakpoint="960"
        app class="leftSidebar v-theme--DARK_AQUA_THEME" :rail="customizer.mini_sidebar" expand-on-hover width="270">
        <!---Logo part -->

        <div class="pa-5">
            <Logo />
        </div>
        <!-- ---------------------------------------------- -->
        <!---Navigation -->
        <!-- ---------------------------------------------- -->
        <perfect-scrollbar class="scrollnavbar">
            <v-list class="pa-6 v-theme--DARK_AQUA_THEME">
                <!---Menu Loop -->
                <!-- {{ 'frfrfr' }}
                {{ organizationStore?.active }} -->
                <template v-if="[null, 'null'].includes(organizationStore?.active)">
                    <template v-for="(item, i) in userSidebarMenu" :key="i">
                        <!---Item Sub Header -->
                        <NavGroup :item="item" v-if="item.header" />
                        <!---If Has Child -->
                        <NavCollapse class="leftPadding" :item="item" :level="0" v-else-if="item.children" />
                        <!---Single Item-->
                        <NavItem :item="item" v-else class="leftPadding" />
                        <!---End Single Item-->
                    </template>
                </template>
                <template v-else>
                    <template v-for="(item, i) in sidebarMenu" :key="i">
                        <!---Item Sub Header -->
                        <NavGroup :item="item" v-if="item.header" />
                        <!---If Has Child -->
                        <NavCollapse class="leftPadding" :item="item" :level="0" v-else-if="item.children" />
                        <!---Single Item-->
                        <NavItem :item="item" v-else class="leftPadding" />
                        <!---End Single Item-->
                    </template>
                </template>
            </v-list>
            <!-- <div class="pa-6 userbottom">
                <Profile />
            </div> -->
        </perfect-scrollbar>
    </v-navigation-drawer>
</template>
