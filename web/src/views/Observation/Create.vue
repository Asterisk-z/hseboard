<script setup lang="ts">
import { ref, onMounted, computed, watch } from 'vue';
import { router } from '@/router';

import BaseBreadcrumb from '@/components/shared/BaseBreadcrumb.vue';
import UiParentCard from '@/components/shared/UiParentCard.vue';
// import { VStepperVertical } from 'vuetify/labs/VStepperVertical'

const page = ref({ title: 'Create Observation' });
const breadcrumbs = ref([
    {
        text: 'Dashboard',
        disabled: false,
        href: 'dashboard'
    },
    {
        text: 'Observation',
        disabled: false,
        href: '#'
    },
    {
        text: 'Create',
        disabled: true,
        href: '#'
    }
]);

const shipping = ref(0)
const step = ref(1)
const items = ref(['Review Order','Select Shipping','Submit',])
const products = ref([
{
    name: 'Product 1',
    price: 10,
    quantity: 2,
},
{
    name: 'Product 2',
    price: 15,
    quantity: 10,
},
])

const subtotal = computed(() => (products.value.reduce((acc, product) => acc + product.quantity * product.price, 0)));
const total = computed(() => (subtotal.value + Number(shipping.value ?? 0)));


</script>

<template>
    <div>
        <BaseBreadcrumb :title="page.title" :breadcrumbs="breadcrumbs"></BaseBreadcrumb>
        <v-row>
            <v-col cols="12" md="12">
                <UiParentCard title="Simple Title"> Welcome Team</UiParentCard>

            </v-col>

        </v-row>
    </div>
</template>
