<script setup lang="ts">
import { ref, onMounted, computed, nextTick, watch } from 'vue';
import BaseBreadcrumb from '@/components/shared/BaseBreadcrumb.vue';
import { VDataTable } from 'vuetify/labs/VDataTable'
import { useOrganizationStore } from '@/stores/organizationStore';
import { useInvestigationStore } from '@/stores/investigationStore';
import { useAuthStore } from '@/stores/auth';
import { useAuditTemplateStore } from '@/stores/auditTemplateStore'
import { useInspectionStore } from '@/stores/inspectionStore'
import moment from 'moment'
import { router } from '@/router';
import Swal from 'sweetalert2'
import readXlsxFile from 'read-excel-file'


const page = ref({ title: 'Inspection Template' });
const breadcrumbs = ref([
    {
        text: 'Dashboard',
        disabled: false,
        href: '/dashboard'
    },
    {
        text: 'Inspection',
        disabled: false,
        href: '/inspections'
    },
    {
        text: 'Template',
        disabled: true,
        href: '#'
    }
]);


const authStore = useAuthStore();
const organizationStore = useOrganizationStore();
const auditTemplateStore = useAuditTemplateStore()
const inspectionStore = useInspectionStore()

onMounted(() => {
    inspectionStore.getInvestigationTemplateTypes()
    inspectionStore.getInspectionTemplateForTypeId()
});

const computedIndex = (index: any) => ++index;

const getActiveOrg: any = computed(() => (organizationStore.getActiveOrg()));
const getAuthUser: any = computed(() => (authStore.loggedUser));
const getAuditTypes: any = computed(() => (auditTemplateStore.auditTypes));
const getAuditTemplates: any = computed(() => (auditTemplateStore.auditTemplates));
const getInvestigationTemplateTypes: any = computed(() => (inspectionStore.inspectionTemplateTypes));
const getInspectionTemplateForTypeId: any = computed(() => (inspectionStore.inspectionTypeTemplates));
const isLoggedInUserOwnsActionOrg: any = computed(() => (getAuthUser.value?.id == getActiveOrg.value?.user_id));



const audit_type = ref('');
watch(getInvestigationTemplateTypes, () => {
    if (getInvestigationTemplateTypes.value) {
        audit_type.value = getInvestigationTemplateTypes.value?.sample
    }
})


const valid = ref(true);
const editValid = ref(true);
const formContainer = ref()
const loading = ref(false);
const setLoading = (value: boolean) => {
    loading.value = value;
}
const dialog = ref(false);
const setDialog = (value: boolean) => {
    dialog.value = value;
}
const editDialog = ref(false);
const setEditDialog = (value: boolean) => {
    editDialog.value = value;
    if (value == false) selectItem({})
}
const viewDialog = ref(false);
const setViewDialog = (value: boolean) => {
    viewDialog.value = value;
    if (value == false) selectItem({})
}
const deleteDialog = ref(false);
const setDeleteDialog = (value: boolean) => {
    deleteDialog.value = value;
    if (value == false) selectItem({})
}
const selectedItem = ref({} as any);
const selectItem = (item: any, action: string = '') => {
    selectedItem.value = Object.assign({}, item.raw);

    switch (action) {
        case 'view':
            setViewDialog(true)
            break;
        case 'edit':
            setEditDialog(true)
            break;
        case 'viewQuestion':
            // setViewQuestion(true)
            questions.value = selectedItem.value.questions?.map((question: any) => (question?.question))
            setQuestionDialog(true)
            break;
        case 'delete':
            setDeleteDialog(true)
            break;
        default:
            break;
    }

}


const search = ref('');

const headers = ref([
    {
        key: 'sn',
        title: 'SN',
        sortable: false,
    },
    {
        key: 'title',
        title: 'Title',
        value: (item: any): string => `${item.title.substring(0, 15)} ${item.title.length > 15 ? '...' : ''}`
    },
    {
        key: 'description',
        title: 'Description',
        value: (item: any): string => `${item.description.substring(0, 15)} ${item.description.length > 15 ? '...' : ''}`
    },
    {
        key: 'created_at',
        title: 'Date Created',
        value: (item: any): string => `${moment(item.created_at).format('MMMM Do YYYY, h:mm a')}`
    },
    {
        key: 'action',
        sortable: false,
        // align: 'end',
        title: 'Action',
    },
]);


const fields = ref({
    audit_type_id: "",
    title: "",
    description: "",
    file: "",
    organization_id: getActiveOrg.value?.uuid,

});


const fieldRules: any = ref({
    audit_type_id: [
        (v: string) => !!v || 'Date Time  is required',
    ],
    title: [
        (v: string) => !!v || 'Title is required',
    ],
    file: [
        (v: string) => !!v || 'File  is required',
    ],
    description: [
        (v: string) => !!v || 'Description is required',
        (v: string) => v.length > 10 || 'More than ten letters required'
    ],
})


const newRow: any = ref([]);
const save = async (e: any) => {
    e.preventDefault();

    try {
        setLoading(true)

        const values = { ...fields.value }


        // let formData = new FormData()
        // formData.append('title', values?.title)
        // formData.append('audit_type_id', values?.audit_type_id)
        // formData.append('questions', newRow.value)
        // formData.append('description', values?.description)
        // formData.append('file', values?.file[0])
        // formData.append('organization_id', getActiveOrg.value?.uuid)

        let objectValues = {
            'title': values?.title,
            'questions': newRow.value,
            'description': values?.description,
            'file': values?.file[0],
            'organization_id': getActiveOrg.value?.uuid,
        }

        const resp = await inspectionStore.uploadTemplateQuestion(objectValues)
            .catch((error: any) => {
                console.log(error)
                throw error
            })
            .then((resp: any) => {
                return resp
            });

        if (resp?.message == 'success') {
            setLoading(false)
            setDialog(false)
            setTimeout(() => {
                // formContainer.value.reset()
            }, 2000)
            inspectionStore.getInspectionTemplateForTypeId();
        }

        setLoading(false)
        setDialog(false)



    } catch (error) {
        console.log(error)
        setLoading(false)
        setDialog(false)
    }

}


const removeDocument = async (document: any) => {

    try {
        setLoading(true)

        let objectValues = {
            "document_id": document?.id,
        }

        const resp = await inspectionStore.deleteTemplateQuestion(objectValues)
            .catch((error: any) => {
                throw error
            })
            .then((resp: any) => {
                return resp
            });

        if (resp?.message == 'success') {
            setLoading(false)
            setDeleteDialog(false)

            inspectionStore.getInspectionTemplateForTypeId();
        }

        setLoading(false)
        setDeleteDialog(false)



    } catch (error) {
        console.log(error)
        setLoading(false)
        setDeleteDialog(false)
    }

}


const datetime = ref('')


const files = ref([])
const questions = ref([])
const questionDialog = ref(false)
const setQuestionDialog = (value: boolean) => {
    questionDialog.value = value;
}
const images = ref([])
const previewImage = ref([] as any)

const selectImage = (image: any) => {
    fields.value.file = image.target.files;
    images.value = image.target.files;
    previewImage.value = [];

    readXlsxFile(image.target.files[0]).then((rows: any) => {
        questions.value = rows
        questionDialog.value = true
        rows.forEach((row: any) => {
            newRow.value.push(row[0])
        })

    })


    for (let index = 0; index < images.value.length; index++) {
        const element = images.value[index];
        previewImage.value.push(URL.createObjectURL(element) as string)
    }

}

</script>

<template>
    <div>
        <BaseBreadcrumb :title="page.title" :breadcrumbs="breadcrumbs"></BaseBreadcrumb>
        <v-row>
            <v-col cols="12" md="12">

                <v-card :title="`Inspection Templates`" flat>


                    <template v-slot:append>
                        <v-sheet>
                            <v-btn color="primary" class="mr-2" @click="setDialog(true)">Add Template</v-btn>

                            <v-dialog v-model="dialog" max-width="800">
                                <v-card>
                                    <v-card-text>
                                        <div class="d-flex justify-space-between">
                                            <h3 class="text-h3">Add Template</h3>
                                            <v-btn icon @click="setDialog(false)" size="small" flat>
                                                <XIcon size="16" />
                                            </v-btn>
                                        </div>
                                    </v-card-text>
                                    <v-divider></v-divider>

                                    <v-card-text>

                                        <VForm v-model="valid" ref="formContainer" fast-fail lazy-validation
                                            @submit.prevent="save" class="py-1" enctype="multipart/form-data">
                                            <VRow class="mt-5 mb-3">


                                                <VCol cols="12" md="12" v-if="audit_type">
                                                    <a class="mr-2" :href="audit_type" target="_blank">
                                                        <v-btn color="primary"> Download Sample Template</v-btn>
                                                    </a>
                                                </VCol>

                                                <VCol cols="12" md="12">
                                                    <v-label
                                                        class="text-subtitle-1 font-weight-medium pb-1">Title</v-label>
                                                    <VTextField type="text" v-model="fields.title"
                                                        :rules="fieldRules.title" required variant="outlined"
                                                        label="Title"
                                                        :color="fields.title.length > 2 ? 'success' : 'primary'">
                                                    </VTextField>
                                                </VCol>

                                                <VCol cols="12" md="12">
                                                    <v-label
                                                        class="text-subtitle-1 font-weight-medium pb-1">Description</v-label>
                                                    <VTextarea variant="outlined" outlined name="Description"
                                                        label="Description" v-model="fields.description"
                                                        :rules="fieldRules.description" required
                                                        :color="fields.description.length > 10 ? 'success' : 'primary'">
                                                    </VTextarea>
                                                </VCol>

                                                <VCol cols="12" md="12">

                                                    <v-label
                                                        class="text-subtitle-1 font-weight-medium pb-1">File</v-label>
                                                    <v-file-input v-model="files" :show-size="1000"
                                                        color="deep-purple-accent-4" label="File input"
                                                        :rules="fieldRules.file" placeholder="Select your files"
                                                        prepend-icon="mdi-paperclip" variant="outlined" counter
                                                        accept=".xlx, .xlsx" @change="selectImage">
                                                        <template v-slot:selection="{ fileNames }">
                                                            <template v-for="(fileName, index) in fileNames"
                                                                :key="fileName">
                                                                <v-chip v-if="index < 2" class="me-2"
                                                                    color="deep-purple-accent-4" size="small" label>
                                                                    {{ fileName }}
                                                                </v-chip>

                                                                <span v-else-if="index === 2"
                                                                    class="text-overline text-grey-darken-3 mx-2">
                                                                    +{{ files.length - 2 }} File(s)
                                                                </span>
                                                            </template>
                                                        </template>
                                                    </v-file-input>

                                                    <div v-if="previewImage">
                                                        <VRow>
                                                            <VCol cols="4" v-for="image in previewImage" :key="image">

                                                                <div>
                                                                    <img class="preview my-3" :src="image" alt=""
                                                                        style="max-width: 200px;" />
                                                                </div>
                                                            </VCol>
                                                        </VRow>
                                                    </div>
                                                    <!-- <VTextField type="hidden" v-model="fields.file"
                                                        :rules="fieldRules.file" required>
                                                    </VTextField> -->
                                                </VCol>
                                                <VCol cols="12" lg="12" class="text-right">
                                                    <v-btn color="error" @click="setDialog(false)"
                                                        variant="text">Cancel</v-btn>

                                                    <v-btn color="primary" type="submit" :loading="loading"
                                                        :disabled="!valid" @click="save">
                                                        <span v-if="!loading">
                                                            Submit
                                                        </span>
                                                        <clip-loader v-else :loading="loading" color="white"
                                                            :size="'25px'"></clip-loader>
                                                    </v-btn>

                                                </VCol>
                                            </VRow>
                                        </VForm>
                                    </v-card-text>
                                </v-card>
                            </v-dialog>

                            <v-dialog v-model="questionDialog" max-width="800">
                                <v-card>
                                    <v-card-text>
                                        <div class="d-flex justify-space-between">
                                            <h3 class="text-h3">Questions</h3>
                                            <v-btn icon @click="setQuestionDialog(false)" size="small" flat>
                                                <XIcon size="16" />
                                            </v-btn>
                                        </div>
                                    </v-card-text>
                                    <v-divider></v-divider>

                                    <v-card-text>

                                        <v-table>
                                            <thead>
                                                <tr>
                                                    <th class="text-left">
                                                        #
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr v-for="(item) in questions" :key="item">
                                                    <td>{{ `${item}` }}</td>
                                                </tr>
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <th class="text-left">

                                                        <v-btn color="primary" class="mr-2"
                                                            @click="setQuestionDialog(false)">Close
                                                            Questions</v-btn>
                                                    </th>
                                                </tr>
                                            </tfoot>
                                        </v-table>
                                    </v-card-text>
                                </v-card>
                            </v-dialog>
                        </v-sheet>
                    </template>

                    <template v-slot:prepend>

                        <v-sheet>
                            <v-dialog v-model="deleteDialog" max-width="500">
                                <v-card>
                                    <v-card-text>
                                        <div class="d-flex justify-space-between">
                                            <h3 class="text-h3">Delete Template</h3>
                                            <v-btn icon @click="setDeleteDialog(false)" size="small" flat>
                                                <XIcon size="16" />
                                            </v-btn>
                                        </div>
                                    </v-card-text>
                                    <v-divider></v-divider>

                                    <v-card-text>

                                        <VRow class="mt-1 mb-3">


                                            <VCol cols="12" lg="12">
                                                <v-card text="You can't revert this action"
                                                    title="Do you want to delete Template?"></v-card>
                                            </VCol>

                                            <VCol cols="12" lg="12" class="text-right">

                                                <v-btn color="primary" class="mr-3" :loading="loading"
                                                    :disabled="loading" @click="removeDocument(selectedItem)">
                                                    <span v-if="!loading">
                                                        Yes
                                                    </span>
                                                    <clip-loader v-else :loading="loading" color="white"
                                                        :size="'25px'"></clip-loader>
                                                </v-btn>

                                                <v-btn color="error" class="mr-3" :disabled="loading"
                                                    @click="setDeleteDialog(false)">No</v-btn>

                                            </VCol>
                                        </VRow>

                                    </v-card-text>
                                </v-card>
                            </v-dialog>

                        </v-sheet>
                    </template>



                    <template v-slot:text>

                        <v-text-field v-model="search" label="Search" prepend-inner-icon="mdi-magnify"
                            variant="outlined" hide-details single-line></v-text-field>
                    </template>

                    <VDataTable :headers="headers" :items="getInspectionTemplateForTypeId" :search="search"
                        item-key="name" items-per-page="5" item-value="fat" show-select>
                        <template v-slot:item.action="{ item }">
                            <div v-if="isLoggedInUserOwnsActionOrg">
                                <v-btn color="error" @click="selectItem(item, 'delete')"> Delete </v-btn>
                                <v-btn color="primary" @click="selectItem(item, 'viewQuestion')"> View Questions
                                </v-btn>
                            </div>
                        </template>

                        <template v-slot:item.sn="{ index }">
                            {{ computedIndex(index) }}
                        </template>


                        <!-- <template v-slot:items="{ column }">
                            {{ column.toUpperCase() }}
                        </template> -->
                    </VDataTable>

                </v-card>

            </v-col>
        </v-row>
    </div>
</template>
