<script setup lang="ts">
import { ref, onMounted, computed } from 'vue';

import { VDataTable } from 'vuetify/labs/VDataTable'
import BaseBreadcrumb from '@/components/shared/BaseBreadcrumb.vue';
import UiParentCard from '@/components/shared/UiParentCard.vue';
import { useOpenLinksStore } from '@/stores/openLinks';
import moment from 'moment'

const page = ref({ title: 'Home' });
const breadcrumbs = ref([
    {
        text: 'Dashboard',
        disabled: false,
        href: 'dashboard'
    },
    {
        text: 'Notification',
        disabled: true,
        href: '#'
    }
]);
const openLinks = useOpenLinksStore();


onMounted(() => {
    openLinks.getNotifications();
});

const computedIndex = (index: any) => ++index;
const getNotifications: any = computed(() => (openLinks.notifications));

const search = ref('');


const headers = ref([
    {
        key: 'sn',
        title: 'SN',
        sortable: false,
    },
    {
        key: 'actionPerformed',
        title: 'Action Performed',
        value: (item: any): string => `${item.actionPerformed} `,
    },
    // {
    //     key: 'actionPerformed',
    //     title: 'Inspection Type',
    //     value: (item: any): string => `${item?.actionPerformed}`
    // },
    {
        key: 'description',
        title: 'Description',
        value: (item: any): string => `${item.description}`
    },
    {
        key: 'created_at',
        title: 'Date Created',
        value: (item: any): string => `${moment(item.createdAt).format('MMMM Do YYYY, h:mm a')}`
    },
    {
        key: 'action',
        sortable: false,
        // align: 'end',
        title: 'Action',
    },
]);

</script>

<template>
    <div>
        <BaseBreadcrumb :title="page.title" :breadcrumbs="breadcrumbs"></BaseBreadcrumb>
        <v-row>
            <v-col cols="12" md="12">
                <v-card :title="``" flat>




                    <template v-slot:text>
                        <v-text-field v-model="search" label="Search" prepend-inner-icon="mdi-magnify"
                            variant="outlined" hide-details single-line></v-text-field>
                    </template>

                    <VDataTable :headers="headers" :items="getNotifications" :search="search" item-key="name"
                        items-per-page="5" item-value="fat" show-select>

                        <!-- <template v-slot:item.action="{ item }">

                            <v-menu>
                                <template v-slot:activator="{ props }">
                                    <v-btn color="primary" dark v-bind="props" flat> Action </v-btn>
                                </template>

                                <v-list>
                                    <v-list-item @click="selectItem(item, 'view')">
                                        <v-list-item-title>
                                            <v-icon class="mr-2" size="small">
                                                mdi-eye
                                            </v-icon>
                                            View Inspection
                                        </v-list-item-title>
                                    </v-list-item>
                                </v-list>
                            </v-menu>
                        </template> -->

                        <!-- <template v-slot:item.action="{ item }">

                            <v-menu>
                                <template v-slot:activator="{ props }">
                                    <v-btn color="primary" dark v-bind="props" flat> Action </v-btn>
                                </template>

                                <v-list>
                                    <template>
                                        <v-list-item @click="selectItem(item, 'view')">
                                            <v-list-item-title>
                                                <v-icon class="mr-2" size="small">
                                                    mdi-pencil
                                                </v-icon>
                                                Edit Action
                                            </v-list-item-title>
                                        </v-list-item>
                                    </template>
                                </v-list>
                            </v-menu>
                        </template> -->

                        <template v-slot:item.sn="{ index }">
                            {{ computedIndex(index) }}
                        </template>




                    </VDataTable>

                </v-card>
            </v-col>
        </v-row>
    </div>
</template>
