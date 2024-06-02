import { defineStore } from 'pinia';
// project imports
import axios from '@/utils/axios';
import { uniqueId } from 'lodash';
import { sub } from 'date-fns';
import { fetchWrapper } from '@/utils/helpers/fetch-wrapper';
import { toastWrapper } from '@/utils/helpers/toast-wrapper';

import user1 from '@/assets/images/profile/user-2.jpg';
import user2 from '@/assets/images/profile/user-3.jpg';
import user3 from '@/assets/images/profile/user-4.jpg';
import user4 from '@/assets/images/profile/user-5.jpg';
import user5 from '@/assets/images/profile/user-6.jpg';


import background1 from '@/assets/images/blog/blog-img5.jpg';
import adobe from '@/assets/images/chat/icon-adobe.svg';
import chrome from '@/assets/images/chat/icon-chrome.svg';
import figma from '@/assets/images/chat/icon-figma.svg';
import java from '@/assets/images/chat/icon-javascript.svg';
import zip from '@/assets/images/chat/icon-zip-folder.svg';

import { Chance } from 'chance';

interface sChatType {
    chats: any;
    chatContent: any;
    conversations: any;
    activeConversation: any;
}

type attachType = {
    icon?: string;
    file?: string;
    fileSize?: string;
};

type chatHistoryType = {
    createdAt?: any;
    msg: string;
    senderId: number | string;
    type: string;
    attachment: attachType[];
    id: string;
};

export type ChatType = {
    id: number;
    name: string;
    status?: string;
    thumb: string;
    recent: boolean;
    chatHistory: chatHistoryType[];
};

const chance = new Chance();

export const useChatStore = defineStore({
    id: 'chat',
    state: (): sChatType => ({
        chats: [],
        chatContent: 0,
        conversations: null as null || [],
        activeConversation: null as null || [],
    }),
    getters: {
        // Get Chats from Getters
        // getChats(state) {
        //     return state.chats;
        // }
    },
    actions: {

        async getConversations() {
            const data = await fetchWrapper
                .get(`chat/index`)
                .then((response: any) => {
                    return response.data
                }).catch((error: any) => {
                    console.log(error)
                });
            this.conversations = data;
        },
        async initiateConversation(values: any) {
            try {
                
                const data = await fetchWrapper.post(`chat/initiate`, values)
                    .catch((error: any) => {
                        throw error;
                    }).then((response: any) => {
                        return response
                    })

                return toastWrapper.success(data?.message, data)
            } catch (error: any) {
                return toastWrapper.error(error, error)
            }

        },
        async sendMessage(values: any) {
            try {
                const data = await fetchWrapper.post(`chat/send`, values)
                    .catch((error: any) => {
                        throw error;
                    }).then((response: any) => {
                        return response
                    })

                return toastWrapper.success(data?.message, data)
            } catch (error: any) {
                return toastWrapper.error(error, error)
            }

        },



        SelectChat(itemID: number) {
            let activeConversation = this.conversations.filter((item: any) => item.id == itemID)
            this.activeConversation = activeConversation?.length > 0 ? activeConversation[0] : null;
            this.chatContent = activeConversation?.length > 0 ? itemID : 0;
        },
        sendMsg(itemID: number, item: string, sender_id: number) {

            const updateMessage = {
                id: itemID,
                message: item,
                created_at: sub(new Date(), { seconds: 1 }),
                sender_id: sender_id
            };
            this.conversations = this.conversations.filter((chat: any) => {
                return chat.id === itemID
                    ? {
                        ...chat,
                        ...chat.messages.push(updateMessage)
                    }
                    : chat;
            });
         
        }
    }
});
