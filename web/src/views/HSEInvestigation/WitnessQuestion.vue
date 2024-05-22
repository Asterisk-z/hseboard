<script setup lang="ts">
import { ref, onMounted, computed, watch } from 'vue';
import { useRoute } from 'vue-router'
import { router } from '@/router';

import BaseBreadcrumb from '@/components/shared/BaseBreadcrumb.vue';
import UiParentCard from '@/components/shared/UiParentCard.vue';
import { useInvestigationStore } from '@/stores/investigationStore';
import moment from 'moment'
import Swal from 'sweetalert2'



const route = useRoute()
const investigationStore = useInvestigationStore();


onMounted(() => {
    investigationStore.getWitnessInvestigationQuestions(route.params.investigation_id as string);
});

const computedIndex = (index: any) => ++index;

const getWitnessInvestigationQuestions: any = computed(() => (investigationStore.witness_investigation_questions));


const page = ref({ title: 'Investigation' });
const breadcrumbs = ref([
    {
        text: 'Dashboard',
        disabled: false,
        href: 'dashboard'
    },
    {
        text: 'HSE Investigation Witness',
        disabled: false,
        href: '/hse-investigation-witness'
    },
    {
        text: 'Witness Questions',
        disabled: true,
        href: '#'
    }
]);

const completedData = ref([]);;

watch(
    () => getWitnessInvestigationQuestions.value,
    (value) => {
        if (getWitnessInvestigationQuestions.value) {
            completedData.value = getWitnessInvestigationQuestions.value.filter((item: any) => item?.is_completed == 'no')
        }
    }
)



const valid = ref(true);;
const formContainer = ref('')
const loading = ref(false);
const setLoading = (value: boolean) => {
    loading.value = value;
}

interface AnswerType {
    questionId: number;
    response: string;
}

// Define the interface for StepTwoFields
interface StepTwoFields {
    answers: AnswerType[];
    answer: any;
    answerSend: boolean;
}

// Use Vue's ref to create a reactive object
const stepTwoFields = ref<StepTwoFields>({
    answers: [],
    answer: null,
    answerSend: true,
});

const completeResponse = async (e: any) => {
    e.preventDefault();

    try {
        setLoading(true)

        const values = { ...stepTwoFields.value }

        let objectValues = {
            "investigation_id": route.params.investigation_id,
        }

        Swal.fire({
            title: 'Info!',
            text: 'Do you want to complete questionaire?',
            icon: 'info',
            confirmButtonText: 'Yes',
            cancelButtonText: 'No',
            showCancelButton: true,
            allowOutsideClick: false,
        }).then((result) => {
            if (result.isConfirmed) {


                investigationStore.completeWitnessResponse(objectValues)
                    .catch((error: any) => {
                        throw error
                    })
                    .then((resp: any) => {
                        //  investigationStore.getInvestigation(route.params.observation_id as string);
                        router.push(`/hse-investigation-witness`)
                        return resp
                    });


            }
        });

    } catch (error) {
        console.log(error)
        setLoading(false)
    }

}


const updateResponse = async (event: any, question: any, index: any) => {

    stepTwoFields.value.answer = event;
    if (!stepTwoFields.value.answerSend) {

        try {
            setLoading(true)

            let objectValues = {
                "investigation_question_id": question?.id,
                "investigation_id": question?.investigation_id,
                "response": event,
            }


            const resp = await investigationStore.sendWitnessResponse(objectValues)
                .catch((error: any) => {
                    console.log(error)
                    throw error
                })
                .then((resp: any) => {
                    return resp
                });

            if (resp?.message == 'success') {
                setLoading(false)
                // stepTwoFields.value.question = "";
                // investigationStore.getWitnessInvestigationQuestions(route.params.observation_id as string);
            }

            setLoading(false)
            stepTwoFields.value.answers[index] = event

        } catch (error) {
            console.log(error)
            setLoading(false)
        }

    }

}

const uploadResponse = async (event: any, question: any, index: any) => {
    console.log(event)
    stepTwoFields.value.answerSend = event;
    updateResponse(stepTwoFields.value.answer, question, index)
}


</script>

<template>
    <div>
        <BaseBreadcrumb :title="page.title" :breadcrumbs="breadcrumbs"></BaseBreadcrumb>

        <v-row v-if="getWitnessInvestigationQuestions">
            <v-col cols="12" md="9">

                <UiParentCard variant="outlined">
                    <v-card-text>
                        <!-- {{ stepTwoFields }} -->

                        <VForm v-model="valid" ref="formContainer" fast-fail lazy-validation
                            @submit.prevent="completeResponse" class="py-1">
                            <VRow class="mt-5 mb-3">

                                <VCol cols="12" md="12" v-for="(question, index) in getWitnessInvestigationQuestions"
                                    :key="question">
                                    <v-label class="text-subtitle-1 font-weight-medium pb-1">{{
            `(${computedIndex(index)}.) ${question.question.question}` }}</v-label>
                                    <!-- v-model="stepTwoFields.answers[question.id]" -->
                                    <VTextarea variant="outlined" outlined :name="question?.question?.question"
                                        label="Question" :disabled="(question.is_completed == 'yes')"
                                        :readonly="(question.is_completed == 'yes')"
                                        :model-value="stepTwoFields.answers[index] ? stepTwoFields.answers[index] : question.response"
                                        @update:modelValue="(e: any) => updateResponse(e, question, index)"
                                        @update:focused="(e: any) => uploadResponse(e, question, index)">
                                    </VTextarea>

                                </VCol>

                                <VCol cols="12" lg="12" class="text-right" v-if="completedData?.length > 0">
                                    <v-btn color="primary" type="submit" :loading="loading" @click="completeResponse">
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
                </UiParentCard>

            </v-col>

        </v-row>
    </div>
</template>
<style lang="scss">
.customTab {
    min-height: 68px;
}
</style>
