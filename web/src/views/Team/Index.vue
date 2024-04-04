<script setup lang="ts">
import { ref, onMounted } from 'vue';


import BaseBreadcrumb from '@/components/shared/BaseBreadcrumb.vue';
import UiParentCard from '@/components/shared/UiParentCard.vue';

import EditableTable from '@/components/shared/Table.vue';
import { VDataTable } from 'vuetify/labs/VDataTable'


const page = ref({ title: 'Home' });
const breadcrumbs = ref([
    {
        text: 'Dashboard',
        disabled: false,
        href: 'dashboard'
    },
    {
        text: 'Team Members',
        disabled: true,
        href: '#'
    }
]);





const search = ref('');
const headers = ref([
    {
        align: 'start',
        key: 'name',
        title: 'Dessert (100g serving)',
    },
    {
        key: 'calories',
        title: 'Calories',
        value: (item: any) => `${item.calories} ${"Peace"}`,
        align: 'end',
        sortable: false
    },
    { key: 'fat', title: 'Fat (g)' },
    { key: 'carbs', title: 'Carbs (g)' },
    { key: 'protein', title: 'Protein (g)' },
    { key: 'iron', title: 'Iron (%)' },
]);
const desserts = ref([
    {
        name: 'Frozen Yogurt',
        calories: 159,
        fat: 6.0,
        carbs: 24,
        protein: 4.0,
        iron: 1,
    },
    {
        name: 'Ice cream sandwich',
        calories: 237,
        fat: 9.0,
        carbs: 37,
        protein: 4.3,
        iron: 1,
    },
    {
        name: 'Eclair',
        calories: 262,
        fat: 16.0,
        carbs: 23,
        protein: 6.0,
        iron: 7,
    },
    {
        name: 'Cupcake',
        calories: 305,
        fat: 3.7,
        carbs: 67,
        protein: 4.3,
        iron: 8,
    },
    {
        name: 'Gingerbread',
        calories: 356,
        fat: 16.0,
        carbs: 49,
        protein: 3.9,
        iron: 16,
    },
    {
        name: 'Jelly bean',
        calories: 375,
        fat: 0.0,
        carbs: 94,
        protein: 0.0,
        iron: 0,
    },
    {
        name: 'Lollipop',
        calories: 392,
        fat: 0.2,
        carbs: 98,
        protein: 0,
        iron: 2,
    },
    {
        name: 'Honeycomb',
        calories: 408,
        fat: 3.2,
        carbs: 87,
        protein: 6.5,
        iron: 45,
    },
    {
        name: 'Donut',
        calories: 452,
        fat: 25.0,
        carbs: 51,
        protein: 4.9,
        iron: 22,
    },
    {
        name: 'KitKat',
        calories: 518,
        fat: 26.0,
        carbs: 65,
        protein: 7,
        iron: 6,
    },
    {
        name: 'Gingerbread',
        calories: 356,
        fat: 16.0,
        carbs: 49,
        protein: 3.9,
        iron: 16,
    },
    {
        name: 'Jelly bean',
        calories: 375,
        fat: 0.0,
        carbs: 94,
        protein: 0.0,
        iron: 0,
    },
    {
        name: 'Lollipop',
        calories: 392,
        fat: 0.2,
        carbs: 98,
        protein: 0,
        iron: 2,
    },
    {
        name: 'Honeycomb',
        calories: 408,
        fat: 3.2,
        carbs: 87,
        protein: 6.5,
        iron: 45,
    },
    {
        name: 'Donut',
        calories: 452,
        fat: 25.0,
        carbs: 51,
        protein: 4.9,
        iron: 22,
    },
    {
        name: 'KitKat',
        calories: 518,
        fat: 26.0,
        carbs: 65,
        protein: 7,
        iron: 6,
    },
    {
        name: 'Gingerbread',
        calories: 356,
        fat: 16.0,
        carbs: 49,
        protein: 3.9,
        iron: 16,
    },
    {
        name: 'Jelly bean',
        calories: 375,
        fat: 0.0,
        carbs: 94,
        protein: 0.0,
        iron: 0,
    },
    {
        name: 'Lollipop',
        calories: 392,
        fat: 0.2,
        carbs: 98,
        protein: 0,
        iron: 2,
    },
    {
        name: 'Honeycomb',
        calories: 408,
        fat: 3.2,
        carbs: 87,
        protein: 6.5,
        iron: 45,
    },
    {
        name: 'Donut',
        calories: 452,
        fat: 25.0,
        carbs: 51,
        protein: 4.9,
        iron: 22,
    },
    {
        name: 'KitKat',
        calories: 518,
        fat: 26.0,
        carbs: 65,
        protein: 7,
        iron: 6,
    },
    {
        name: 'Gingerbread',
        calories: 356,
        fat: 16.0,
        carbs: 49,
        protein: 3.9,
        iron: 16,
    },
    {
        name: 'Jelly bean',
        calories: 375,
        fat: 0.0,
        carbs: 94,
        protein: 0.0,
        iron: 0,
    },
    {
        name: 'Lollipop',
        calories: 392,
        fat: 0.2,
        carbs: 98,
        protein: 0,
        iron: 2,
    },
    {
        name: 'Honeycomb',
        calories: 408,
        fat: 3.2,
        carbs: 87,
        protein: 6.5,
        iron: 45,
    },
    {
        name: 'Donut',
        calories: 452,
        fat: 25.0,
        carbs: 51,
        protein: 4.9,
        iron: 22,
    },
    {
        name: 'KitKat',
        calories: 518,
        fat: 26.0,
        carbs: 65,
        protein: 7,
        iron: 6,
    },
]);

const remove = (key : any) => {
    headers.value = headers.value.filter(header => header.key !== key)
      }

</script>

<template>
    <div>
        <BaseBreadcrumb :title="page.title" :breadcrumbs="breadcrumbs"></BaseBreadcrumb>
        <v-row>
            <v-col cols="12" md="12">
                <!-- <UiParentCard title="Simple Title"> Welcome Team</UiParentCard> -->

                <v-card title="Team Members" flat>
                    <template v-slot:text>
                        <v-text-field v-model="search" label="Search" prepend-inner-icon="mdi-magnify"
                            variant="outlined" hide-details single-line></v-text-field>
                    </template>

                    <VDataTable :headers="headers" :items="desserts" :search="search" item-key="name" items-per-page="5"
                        item-value="fat" show-select>
                        <template v-slot:header.name="{ column }">
                            {{ column.name.toUpperCase() }}
                        </template>


                        <!-- <template v-slot:headers="{ columns, isSorted, getSortIcon, toggleSort }">
                            <tr>
                                <td v-for="column in columns" :key="column.key">
                                    <span class="mr-2 cursor-pointer" @click="() => toggleSort(column)">{{
                                        column.title }}</span>
                                    <template v-if="isSorted(column)">
                                        <v-icon :icon="getSortIcon(column)"></v-icon>
                                    </template>
                                    <v-icon v-if="column.removable" icon="$close"
                                        @click="() => remove(column.key)"></v-icon>
                                </td>
                            </tr>
                        </template> -->

                        <template v-slot:items="{ column }">
                            {{ column.toUpperCase() }}
                        </template>
                    </VDataTable>

                </v-card>

            </v-col>
        </v-row>
    </div>
</template>
