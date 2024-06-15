<script setup lang="ts">
import { ref, onMounted, computed, watch } from 'vue';

import { useAccountStore } from '@/stores/accountStore';

const accountStore = useAccountStore();

const valid = ref(true);
const formContainer = ref('')

const loading = ref(false);
const setLoading = (value: boolean) => {
    loading.value = value;
}

const props = defineProps({
    closeDialog: {
        type: Function,
        required: true,
        default(rawProps: boolean) {
            return rawProps;
        }
    },
});

const fields = ref({
    images: []
});

const files = ref(null as any)
const previewImage = ref(null as any)

const selectImage = (image: any) => {

    fields.value.images = image.target.files;
    files.value = image.target.files;
    previewImage.value = [];

    for (let index = 0; index < files.value.length; index++) {
        const element = files.value[index];
        previewImage.value.push(URL.createObjectURL(element) as string)
    }

}

const uploadLogo = async (e: any) => {
    e.preventDefault();

    try {
        setLoading(true)

        const formData = new FormData();
        formData.append('image', files.value[0])

        const resp = await accountStore.uploadLogo(formData)
            .catch((error: any) => {
                console.log(error)
                throw error
            })
            .then((resp: any) => {
                return resp
            });

        if (resp?.message == 'success') {
            setLoading(false)
            props.closeDialog(false)

            fields.value.images = [];
            files.value = []
            previewImage.value = []
        }

        setLoading(false)
        props.closeDialog(false)



    } catch (error) {
        console.log(error)
        setLoading(false)
        props.closeDialog(false)
    }

}


</script>

<template>

    <VForm v-model="valid" ref="formContainer" fast-fail lazy-validation @submit.prevent="uploadLogo" class="py-1">
        <VRow class="mt-5 mb-3">

            <VCol cols="12" md="12">

                <v-file-input :show-size="1000" color="deep-purple-accent-4" label="Photo"
                    placeholder="Select your photo" prepend-icon="mdi-paperclip" variant="outlined" accept="image/*"
                    @change="selectImage">
                </v-file-input>

                <div v-if="previewImage">
                    <VRow>
                        <VCol cols="4" v-for="image in previewImage" :key="image">

                            <div>
                                <img class="preview my-3" :src="image" alt="" style="max-width: 200px;" />
                            </div>
                        </VCol>
                    </VRow>
                </div>
            </VCol>

            <VCol cols="12" lg="12" class="text-right">
                <v-btn color="error" @click="props.closeDialog(false)" variant="text">Cancel</v-btn>

                <v-btn color="primary" type="submit" :loading="loading" :disabled="!valid" @click="uploadLogo">
                    <span v-if="!loading">
                        Upload
                    </span>
                    <clip-loader v-else :loading="loading" color="white" :size="'25px'"></clip-loader>
                </v-btn>

            </VCol>
        </VRow>
    </VForm>


</template>
<style lang="scss">
.customTab {
    min-height: 68px;
}
</style>
