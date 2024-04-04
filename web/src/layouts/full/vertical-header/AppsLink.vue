<script setup lang="ts">
import { appsLink } from '@/_mockApis/headerData';

import img1 from '@/assets/images/svgs/icon-dd-chat.svg';
import img2 from '@/assets/images/svgs/icon-dd-cart.svg';

const appsLink2 =  [
    {
        avatar: img1,
        title: 'BigDream',
        subtext: 'New messages arrived',
        href: '/apps/chats'
    },
    {
        avatar: img2,
        title: 'BigDream2',
        subtext: 'learn more information',
        href: '/ecommerce/products'
    },
]
</script>
<template>
    <!-- ---------------------------------------------- -->
    <!-- apps link -->
    <!-- ---------------------------------------------- -->
    <v-row>
        <v-col cols="12" lg="12" v-for="(item, i) in appsLink2" :key="i">
            <router-link :to="'#'" class="text-decoration-none custom-text-primary">
                <div class="d-flex align-center">
                    <v-avatar size="45" color="grey100" rounded="md">
                        <v-img :src="item.avatar" width="24" height="24" :alt="item.avatar" />
                    </v-avatar>
                    <div class="ml-3">
                        <h6 class="text-subtitle-1 mb-1 textPrimary font-weight-semibold custom-title">{{ item.title }}
                        </h6>
                    </div>
                </div>
            </router-link>
        </v-col>
    </v-row>
</template>
