<script setup lang="ts">
import { ref, computed } from 'vue';
import { useChatStore } from '@/stores/chatStore';

import { useAuthStore } from '@/stores/auth';

const authStore = useAuthStore();

const getAuthUser: any = computed(() => (authStore.loggedUser));
const msg = ref('');
const chatStore = useChatStore();

const sendMessage = async (message: string) => {

    try {

        let objectValues = {
            "conversation_id": chatStore?.activeConversation?.id,
            "message": message,
        }

        const resp = await chatStore.sendMessage(objectValues)
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
function addItemAndClear(item: string) {
    if (item.length === 0) {
        return;
    }
    chatStore.sendMsg(chatStore.chatContent, msg.value, getAuthUser.value.id);
    sendMessage(msg.value)
    msg.value = '';
}
</script>

<template>
    <form class="d-flex align-center pa-4" @submit.prevent="addItemAndClear(msg)">
        <v-btn icon variant="text" class="text-medium-emphasis">
            <!-- <MoodSmileIcon size="24" /> -->
        </v-btn>

        <v-text-field variant="solo" hide-details v-model="msg" color="primary" class="shadow-none" density="compact"
            placeholder="Type a Message"></v-text-field>

        <v-btn icon variant="text" type="submit" class="text-medium-emphasis" :disabled="!msg">
            <SendIcon size="20" />
        </v-btn>

    </form>
</template>

<style>
.shadow-none .v-field--no-label {
    --v-field-padding-top: -7px;
}
</style>
