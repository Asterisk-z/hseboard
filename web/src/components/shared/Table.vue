<script setup lang="ts">
import { ref, onMounted, computed, watch } from 'vue';
import { VDataTable } from 'vuetify/labs/VDataTable'


const search = ref('');
const dialog = ref(false);
const dialogDelete = ref(false);
const headers = ref([
    {
        title: 'Dessert (100g serving)',
        align: 'start',
        sortable: false,
        key: 'name',
    },
    { title: 'Calories', key: 'calories' },
    { title: 'Fat (g)', key: 'fat' },
    { title: 'Carbs (g)', key: 'carbs' },
    { title: 'Protein (g)', key: 'protein' },
    { title: 'Actions', key: 'actions', sortable: false },
]);
const desserts = ref([
    {
        name: 'Frozen Yogurt',
        calories: 159,
        fat: 6.0,
        carbs: 24,
        protein: 4.0,
    },
    {
        name: 'Ice cream sandwich',
        calories: 237,
        fat: 9.0,
        carbs: 37,
        protein: 4.3,
    },
    {
        name: 'Eclair',
        calories: 262,
        fat: 16.0,
        carbs: 23,
        protein: 6.0,
    },
    {
        name: 'Cupcake',
        calories: 305,
        fat: 3.7,
        carbs: 67,
        protein: 4.3,
    },
    {
        name: 'Gingerbread',
        calories: 356,
        fat: 16.0,
        carbs: 49,
        protein: 3.9,
    },
    {
        name: 'Jelly bean',
        calories: 375,
        fat: 0.0,
        carbs: 94,
        protein: 0.0,
    },
    {
        name: 'Lollipop',
        calories: 392,
        fat: 0.2,
        carbs: 98,
        protein: 0,
    },
    {
        name: 'Honeycomb',
        calories: 408,
        fat: 3.2,
        carbs: 87,
        protein: 6.5,
    },
    {
        name: 'Donut',
        calories: 452,
        fat: 25.0,
        carbs: 51,
        protein: 4.9,
    },
    {
        name: 'KitKat',
        calories: 518,
        fat: 26.0,
        carbs: 65,
        protein: 7,
    },
    {
        name: 'Ice cream sandwich',
        calories: 237,
        fat: 9.0,
        carbs: 37,
        protein: 4.3,
    },
    {
        name: 'Eclair',
        calories: 262,
        fat: 16.0,
        carbs: 23,
        protein: 6.0,
    },
    {
        name: 'Cupcake',
        calories: 305,
        fat: 3.7,
        carbs: 67,
        protein: 4.3,
    },
    {
        name: 'Gingerbread',
        calories: 356,
        fat: 16.0,
        carbs: 49,
        protein: 3.9,
    },
    {
        name: 'Jelly bean',
        calories: 375,
        fat: 0.0,
        carbs: 94,
        protein: 0.0,
    },
    {
        name: 'Lollipop',
        calories: 392,
        fat: 0.2,
        carbs: 98,
        protein: 0,
    },
    {
        name: 'Honeycomb',
        calories: 408,
        fat: 3.2,
        carbs: 87,
        protein: 6.5,
    },
    {
        name: 'Donut',
        calories: 452,
        fat: 25.0,
        carbs: 51,
        protein: 4.9,
    },
    {
        name: 'KitKat',
        calories: 518,
        fat: 26.0,
        carbs: 65,
        protein: 7,
    },
]);
const editedIndex = ref(-1);
const editedItem = ref({
    name: '',
    calories: 0,
    fat: 0,
    carbs: 0,
    protein: 0,
});
const defaultItem = ref({
    name: '',
    calories: 0,
    fat: 0,
    carbs: 0,
    protein: 0,
});
const formTitle : any  = computed(() => (editedIndex.value === -1 ? 'New Item' : 'Edit Item'));

watch(dialogDelete, (val) => {
    console.log(`x is ${val}`)
    val || closeDelete();
})
watch(dialog, (val) => {
    console.log(`x is ${val}`)
    val || close();
})

const editItem = (item: any) => {
    editedIndex.value = desserts.value.indexOf(item)
    editedItem.value = Object.assign({}, item)
    dialog.value = true
};

const deleteItem = (item: any) => {
    editedIndex.value = desserts.value.indexOf(item)
    editedItem.value = Object.assign({}, item)
    dialogDelete.value = true
};

const $nextTick = () => {
    // editedItem.value = Object.assign({}, defaultItem)
    editedIndex.value = -1
}

const closeDelete = () => {
    dialogDelete.value = false
    $nextTick();
};

const close = () => {
    dialog.value = false
    $nextTick();
};

const deleteItemConfirm = () => {
    desserts.value.splice(editedIndex.value, 1)
    closeDelete()
};

const save = () => {
    if (editedIndex.value > -1) {
        // Object.assign(desserts[editedIndex.value], editedItem.value)
    } else {
        desserts.value.push(editedItem.value)
    }
    close()
};

</script>

<template>
    <div>

        <v-card title="Table" flat>
            <template v-slot:text>
                <v-text-field v-model="search" label="Search" prepend-inner-icon="mdi-magnify" variant="outlined"
                    hide-details single-line></v-text-field>
            </template>
            <VDataTable :headers="headers" :items="desserts" :sort-by="[{ key: 'calories', order: 'asc' }]">
                <template v-slot:top>
                    <VToolbar flat>
                        <VToolbarTitle>List</VToolbarTitle>
                        <v-divider class="mx-4" inset vertical></v-divider>
                        <v-spacer></v-spacer>
                        <v-dialog v-model="dialog" max-width="500px">
                            <template v-slot:activator="{ props }">
                                <v-btn class="mb-2" color="primary" v-bind="props">
                                    New Item
                                </v-btn>
                            </template>
                            <v-card>
                                <v-card-title>
                                    <span class="text-h5">{{ formTitle }}</span>
                                </v-card-title>

                                <v-card-text>
                                    <v-container>
                                        <v-row>
                                            <v-col cols="12" md="4" sm="6">
                                                <v-text-field v-model="editedItem.name"
                                                    label="Dessert name"></v-text-field>
                                            </v-col>
                                            <v-col cols="12" md="4" sm="6">
                                                <v-text-field v-model="editedItem.calories"
                                                    label="Calories"></v-text-field>
                                            </v-col>
                                            <v-col cols="12" md="4" sm="6">
                                                <v-text-field v-model="editedItem.fat"
                                                    label="Fat (g)"></v-text-field>
                                            </v-col>
                                            <v-col cols="12" md="4" sm="6">
                                                <v-text-field v-model="editedItem.carbs"
                                                    label="Carbs (g)"></v-text-field>
                                            </v-col>
                                            <v-col cols="12" md="4" sm="6">
                                                <v-text-field v-model="editedItem.protein"
                                                    label="Protein (g)"></v-text-field>
                                            </v-col>
                                        </v-row>
                                    </v-container>
                                </v-card-text>

                                <v-card-actions>
                                    <v-spacer></v-spacer>
                                    <v-btn color="blue-darken-1" variant="text" @click="close">
                                        Cancel
                                    </v-btn>
                                    <v-btn color="blue-darken-1" variant="text" @click="save">
                                        Save
                                    </v-btn>
                                </v-card-actions>
                            </v-card>
                        </v-dialog>
                        <v-dialog v-model="dialogDelete" max-width="500px">
                            <v-card>
                                <v-card-title class="text-h5">Are you sure you want to delete this
                                    item?</v-card-title>
                                <v-card-actions>
                                    <v-spacer></v-spacer>
                                    <v-btn color="blue-darken-1" variant="text" @click="closeDelete">Cancel</v-btn>
                                    <v-btn color="blue-darken-1" variant="text"
                                        @click="deleteItemConfirm">OK</v-btn>
                                    <v-spacer></v-spacer>
                                </v-card-actions>
                            </v-card>
                        </v-dialog>
                    </VToolbar>
                </template>
                <template v-slot:item.actions="{ item }">
                    <v-icon class="me-2" size="small" @click="editItem(item)">
                        mdi-pencil
                    </v-icon>
                    <v-icon size="small" @click="deleteItem(item)">
                        mdi-delete
                    </v-icon>
                </template>
                <template v-slot:no-data>
                    <!-- <v-btn color="primary" @click="initialize">
                        Reset
                    </v-btn> -->
                </template>
            </VDataTable>

        </v-card>

    </div>
</template>
