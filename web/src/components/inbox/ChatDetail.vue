<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import user1 from '@/assets/images/profile/user-1.jpg';
import { useChatStore } from '@/stores/chatStore';
import { formatDistanceToNowStrict } from 'date-fns';
import ChatSendMsg from '@/components/inbox/ChatSendMsg.vue';
import ChatInfo from '@/components/inbox/ChatInfo.vue';

import { useAuthStore } from '@/stores/auth';

const authStore = useAuthStore();

const getAuthUser: any = computed(() => (authStore.loggedUser));

const chatStore = useChatStore();


const chatDetail: any = computed(() => {
    return chatStore.chats[chatStore.chatContent - 1];
});


const activeConversation: any = computed(() => chatStore.activeConversation);

</script>
<template>

    <div v-if="Object.keys(activeConversation).length > 0" class="customHeight">
        <div>
            <div class="d-flex align-center gap-3 pa-4">
                <!---Topbar Row-->

                <div class="d-flex gap-2 align-center">
                    <!---User Avatar-->

                    <v-avatar>
                        <img :src="activeConversation?.main_image" alt="pro" width="50"
                            v-if="activeConversation?.main_image" />
                        <img :src="user1" alt="pro" width="50" v-else />
                    </v-avatar>
                    <!---Name & Last seen-->
                    <div>
                        <h5 class="text-h5 mb-n1">{{ activeConversation.main_name }}</h5>
                    </div>
                </div>

                <!---Topbar Icons-->
            </div>
            <v-divider />
            <!---Chat History-->
            <perfect-scrollbar class="rightpartHeight">
                <div class="d-flex" v-if="activeConversation?.messages?.length > 0">
                    <div class="w-100">
                        <div v-for="message in activeConversation.messages" :key="message.id" class="pa-5">
                            <div v-if="getAuthUser.id === message.sender_id" class="justify-end d-flex text-end mb-1">
                                <div>
                                    <small class="text-medium-emphasis text-subtitle-2" v-if="message.created_at">
                                        {{
                                        formatDistanceToNowStrict(new Date(message.created_at), {
                                        addSuffix: false
                                        })
                                        }}
                                        ago</small>

                                    <v-sheet class="bg-grey100 rounded-md px-3 py-2 mb-1">
                                        <p class="text-body-1">{{ message.message }}</p>
                                    </v-sheet>
                                    <!-- <v-sheet v-if="message.type == 'img'" class="mb-1">
                                        <img :src="message.msg" class="rounded-md" alt="pro" width="250" />
                                    </v-sheet>sender -->
                                </div>
                            </div>
                            <div v-else class="d-flex align-items-start gap-3 mb-1">
                                <!---User Avatar-->

                                <v-avatar>
                                    <img :src="message?.sender?.media?.full_url" alt="pro" width="40"
                                        v-if="message?.sender?.media" />
                                    <img :src="user1" alt="pro" width="40" v-else />
                                </v-avatar>
                                <div>
                                    <small class="text-medium-emphasis text-subtitle-2" v-if="message.created_at">
                                        {{ message.sender?.full_name }},
                                        {{
                                        formatDistanceToNowStrict(new Date(message.created_at), {
                                        addSuffix: false
                                        })
                                        }}
                                        ago
                                    </small>

                                    <v-sheet class="bg-grey100 rounded-md px-3 py-2 mb-1">
                                        <p class="text-body-1">{{ message.message }}</p>
                                    </v-sheet>
                                    <!-- <v-sheet v-if="chat.type == 'img'" class="mb-1">
                                        <img :src="chat.msg" class="rounded-md" alt="pro" width="250" />
                                    </v-sheet> -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </perfect-scrollbar>
        </div>
        <v-divider />
        <!---Chat send-->
        <ChatSendMsg />
    </div>
    <div v-else class="customHeight">
        <div>

        </div>
    </div>
</template>
<style lang="scss">
.rightpartHeight {
    height: 530px;
}

.badg-dotDetail {
    left: -9px;
    position: relative;
    bottom: -10px;
}

.toggleLeft {
    position: absolute;
    right: 15px;
    top: 15px;
}

.right-sidebar {
    width: 320px;
    border-left: 1px solid rgb(var(--v-theme-borderColor));
    transition: 0.1s ease-in;
    flex-shrink: 0;
}

.HideLeftPart {
    display: none;
}

@media (max-width: 960px) {
    .right-sidebar {
        position: absolute;
        right: -320px;

        &.showLeftPart {
            right: 0;
            z-index: 2;
            box-shadow: 2px 1px 20px rgba(0, 0, 0, 0.1);
        }
    }

    .boxoverlay {
        position: absolute;
        height: 100%;
        width: 100%;
        z-index: 1;
        background: rgba(0, 0, 0, 0.2);
    }
}
</style>
