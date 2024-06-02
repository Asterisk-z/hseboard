<script setup lang="ts">
import { ref, onMounted, computed, watch } from 'vue';

import BaseBreadcrumb from '@/components/shared/BaseBreadcrumb.vue';
import AppBaseCard from '@/components/shared/AppBaseCard.vue';
import ChatListing from '@/components/inbox/ChatListing.vue';
import ChatDetail from '@/components/inbox/ChatDetail.vue';
import ChatProfile from '@/components/inbox/ChatProfile.vue';
import { useChatStore } from '@/stores/chatStore';

const page = ref({ title: 'Home' });
const breadcrumbs = ref([
    {
        text: 'Dashboard',
        disabled: false,
        href: 'dashboard'
    },
    {
        text: 'Chat',
        disabled: true,
        href: '#'
    }
]);

const chatStore = useChatStore();
onMounted(() => {
    chatStore.getConversations();
});

</script>

<template>
    <div>
        <BaseBreadcrumb :title="page.title" :breadcrumbs="breadcrumbs"></BaseBreadcrumb>
        <v-row>
            <v-col cols="12" md="12">
                <v-card elevation="10">
                    <AppBaseCard>
                        <template v-slot:leftpart>
                            <ChatProfile />
                            <ChatListing />
                        </template>
                        <template v-slot:rightpart>
                            <ChatDetail />
                        </template>

                        <template v-slot:mobileLeftContent>
                            <ChatProfile />
                            <ChatListing />
                        </template>
                    </AppBaseCard>
                </v-card>
            </v-col>
        </v-row>

    </div>
</template>

<style scoped lang="scss">
@media (max-width: 1279px) {
    .v-card {
        position: unset;
    }
}
</style>