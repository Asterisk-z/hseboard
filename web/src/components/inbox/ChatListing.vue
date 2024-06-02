<script setup lang="ts">
import { ref, onMounted, computed } from 'vue';
import user1 from '@/assets/images/profile/user-1.jpg';
import { useChatStore } from '@/stores/chatStore';
import { useTeamMemberStore } from '@/stores/teamMemberStore';
import { formatDistanceToNowStrict } from 'date-fns';
import { last } from 'lodash';

const chatStore = useChatStore();
const teamMemberStore = useTeamMemberStore();

onMounted(() => {
    teamMemberStore.getTeamMembersExcept();
});
// console.log(chatStore.chats)
const getChats = computed(() => {
    return chatStore.chats;
});

// console.log(getChats.value)

const chatItem = getChats;
const searchValue = ref('');
const filteredChats = computed(() => {
    return chatItem.value.filter((chat: any) => {
        return chat.name.toLowerCase().includes(searchValue.value.toLowerCase());
    });
});
// const lastActivity = (chat) => last(chat.chatHistory).createdAt;

const items = ref([{ title: 'Sort by time' }, { title: 'Sort by Unread' }, { title: 'Mark all as read' }]);


const toggleChat = ref(true);

function changeToggleTab() {
    toggleChat.value = !toggleChat.value;
}

const getConversations: any = computed(() => (chatStore.conversations));
const getTeamMembersExcept: any = computed(() => (teamMemberStore.membersExceptMe));
const initiateChat = async (e: any) => {

    try {

        let objectValues = {
            "receiver_id": e,
        }

        const resp = await chatStore.initiateConversation(objectValues)
            .catch((error: any) => {
                console.log(error)
                throw error
            })
            .then((resp: any) => {
                return resp
            });

        if (resp?.message == 'success') {
            // setLoading(false)
            // setTimeout(() => {
            //     formContainer.value.reset()
            // }, 2000)
        }

        // setLoading(false)

    } catch (error) {
        console.log(error)
        // setLoading(false)
    }

}


</script>
<template>
    <div>
        <v-sheet>
            <div class="px-6 pt-3">
                <v-text-field variant="outlined" v-model="searchValue" append-inner-icon="mdi-magnify"
                    placeholder="Search Contact" hide-details density="compact"></v-text-field>
                <v-menu>
                    <template v-slot:activator="{ props }">
                        <!-- <v-btn color="white" variant="flat" class="mt-4 text-medium-emphasis" v-bind="props">Recent
                            Chats
                            <ChevronDownIcon size="18" class="ml-2" />
                        </v-btn> -->
                        <v-btn color="primary" variant="flat" class="mt-4 text-medium-emphasis"
                            @click="changeToggleTab">{{ `${toggleChat ? 'Members' : 'Chats'}` }}</v-btn>
                    </template>
                    <v-list class="elevation-10">
                        <v-list-item v-for="(item, index) in items" :key="index" :value="index">
                            <v-list-item-title>{{ item.title }}</v-list-item-title>
                        </v-list-item>
                    </v-list>
                </v-menu>
            </div>
        </v-sheet>
        <perfect-scrollbar class="lgScroll">
            <template v-if="toggleChat">

                <v-list v-if="getConversations.length > 0">
                    <v-list-item :value="conversation.id" color="primary" class="text-no-wrap chatItem"
                        v-for="conversation in getConversations" :key="conversation.id" lines="two"
                        :active="chatStore.chatContent === conversation.id"
                        @click="chatStore.SelectChat(conversation.id)">

                        <template v-slot:prepend>
                            <v-avatar>
                                <img :src="conversation?.main_image" alt="pro" width="50"
                                    v-if="conversation?.main_image" />
                                <img :src="user1" alt="pro" width="50" v-else />
                            </v-avatar>
                        </template>

                        <v-list-item-title class="text-subtitle-1 textPrimary w-100 font-weight-semibold">{{
                    conversation.main_name
                }}</v-list-item-title>

                        <!-- <v-sheet v-if="chat.chatHistory.slice(-1)[0].type == 'img'">
                            <small class="textPrimary text-subtitle-2">Sent a Photo</small>
                        </v-sheet>
                        <div class="text-subtitle-2 textPrimary mt-1 text-truncate w-100" v-else>
                            {{ chat.chatHistory.slice(-1)[0].msg }}
                        </div> -->

                        <!-- <template v-slot:append>
                            <div class="d-flex flex-column text-right w-25">
                                <small class="textPrimary text-subtitle-2">
                                    {{
                        formatDistanceToNowStrict(new Date(lastActivity(chat)), {
                            addSuffix: false
                        })
                    }}
                                </small>
                            </div>
                        </template> -->
                    </v-list-item>
                </v-list>
            </template>
            <template v-else>

                <v-list v-if="getTeamMembersExcept?.length > 0">
                    <v-list-item :value="member.id" color="primary" class="text-no-wrap chatItem"
                        v-for="member in getTeamMembersExcept" :key="member.id" lines="two"
                        @click="() => initiateChat(member.uuid)">

                        <template v-slot:prepend>
                            <v-avatar>
                                <img :src="member?.media?.full_url" alt="pro" width="50" v-if="member?.media" />
                                <img :src="user1" alt="pro" width="50" v-else />
                            </v-avatar>
                        </template>

                        <v-list-item-title class="text-subtitle-1 textPrimary w-100 font-weight-semibold">{{
                    member.full_name
                }}</v-list-item-title>

                    </v-list-item>
                </v-list>
            </template>

            <!-- <v-list>
                <v-list-item :value="chat.id" color="primary" class="text-no-wrap chatItem"
                    v-for="chat in filteredChats" :key="chat.id" lines="two" :active="chatStore.chatContent === chat.id"
                    @click="chatStore.SelectChat(chat.id)">

                    <template v-slot:prepend>
                        <v-avatar>
                            <img :src="chat.thumb" alt="pro" width="50" />
                        </v-avatar>
                    </template>

                    <v-list-item-title class="text-subtitle-1 textPrimary w-100 font-weight-semibold">{{ chat.name
                        }}</v-list-item-title>

                    <v-sheet v-if="chat.chatHistory.slice(-1)[0].type == 'img'">
                        <small class="textPrimary text-subtitle-2">Sent a Photo</small>
                    </v-sheet>
                    <div class="text-subtitle-2 textPrimary mt-1 text-truncate w-100" v-else>
                        {{ chat.chatHistory.slice(-1)[0].msg }}
                    </div>

                    <template v-slot:append>
                        <div class="d-flex flex-column text-right w-25">
                            <small class="textPrimary text-subtitle-2">
                                {{
                                formatDistanceToNowStrict(new Date(lastActivity(chat)), {
                                addSuffix: false
                                })
                                }}
                            </small>
                        </div>
                    </template>
                </v-list-item>
            </v-list> -->
        </perfect-scrollbar>
    </div>

</template>
<style>
.chatItem {
    padding: 16px 24px !important;
    border-bottom: 1px solid rgb(var(--v-theme-inputBorder), 0.1);
}

.badg-dot {
    left: -17px;
    position: relative;
    bottom: -10px;
}

.lgScroll {
    height: 500px;
}
</style>
