<script setup lang="ts">
import { ref, onMounted, computed, watch } from 'vue';
import { router } from '@/router';

import BaseBreadcrumb from '@/components/shared/BaseBreadcrumb.vue';
import UiParentCard from '@/components/shared/UiParentCard.vue';
import { useOrganizationStore } from '@/stores/organizationStore';
import { useReportStore } from '@/stores/reportStore';
import { useAuthStore } from '@/stores/auth';
import moment from 'moment'

const authStore = useAuthStore();
const organizationStore = useOrganizationStore();
const reportStore = useReportStore();


onMounted(() => {
    reportStore.getReports()
});

const getActiveOrg = computed(() => (organizationStore.getActiveOrg()));
const getReports = computed(() => (reportStore.reports));
const getAuthUser = computed(() => (authStore.loggedUser));
const isLoggedInUserOwnsActionOrg = computed(() => (getAuthUser.value?.id == getActiveOrg.value?.user_id));

const page = ref({ title: 'Create Statistics' });
const breadcrumbs = ref([
    {
        text: 'Dashboard',
        disabled: false,
        href: 'dashboard'
    },
    {
        text: 'HSE Statistics',
        disabled: false,
        href: '#'
    },
    {
        text: 'Create',
        disabled: true,
        href: '#'
    }
]);






const valid = ref(true);;
const formContainer = ref()
const loading = ref(false);
const setLoading = (value: boolean) => {
    loading.value = value;
}

const fields = ref({
    start_date: new Date(),
    end_date: new Date(),
    workers: "",
    workingHoursPerDay: "",
    sickWorkers: "",
    offWorkingDays: "",
    workersOnLeave: "",
    leaveDays: "",
    overTimeHoursParDay: "",
    overTimeDays: "",
    overTimeWorkers: "",
    meetingTarget: 0,
    meetingActual: 0,
    toolboxTarget: 0,
    toolboxActual: 0,
    inspectionTarget: 0,
    inspectionActual: 0,
    drillsTarget: 0,
    drillsActual: 0,
    unSafeActTarget: 0,
    unSafeConditionTarget: 0,
    organization_id: getActiveOrg.value?.uuid,
});

const daysInterval = computed(() => (1 + moment.duration(moment(fields.value?.end_date).diff(moment(fields.value?.start_date, 'days'))).days()));
const totalManHour = computed(() => (daysInterval.value * fields.value?.workers * fields.value?.workingHoursPerDay));
const lostWorkingHours = computed(() => (fields.value?.offWorkingDays * fields.value?.sickWorkers * fields.value?.workingHoursPerDay));
const leaveHours = computed(() => (fields.value?.workersOnLeave * fields.value?.leaveDays * fields.value?.workingHoursPerDay));
const totalOverTime = computed(() => (fields.value?.overTimeHoursParDay * fields.value?.overTimeDays * fields.value?.overTimeWorkers));
const actualManHour = computed(() => (parseFloat(totalManHour.value -lostWorkingHours.value - leaveHours.value + totalOverTime.value)));


const fieldRules: any = ref({
    start_date: [
        (v: number) => !!v || 'Number Of Workers is required',
    ],
    end_date: [
        (v: number) => !!v || 'Number Of Workers is required',
    ],
    workers: [
        (v: number) => !!v || 'Number Of Workers is required',
    ],
    workingHoursPerDay: [
        (v: number) => !!v || 'Number of working hours is required',
        (v: number) => v < 21 || 'Can not have more than 20 working hours'
    ],
    sickWorkers: [
        (v: number) => !!v || 'Number of sick worker is required',
        (v: number) => (v && v <= fields.value.workers) || 'Sick worker can not be more than workers'
    ],
    offWorkingDays: [
        (v: number) => !!v || 'Number of working hours is required',
        (v: number) => v <= daysInterval.value || 'Days off worker can not be more than report days'
    ],
    workersOnLeave: [
        (v: number) => !!v || 'Number of worker on leave is required',
        (v: number) => (v && v <= fields.value.workers) || 'Sick worker can not be more than workers'
    ],
    leaveDays: [
        (v: number) => !!v || 'Number of working hours is required',
        (v: number) => v <= daysInterval.value || 'Days off worker can not be more than report days'
    ],
    overTimeHoursParDay: [
        (v: number) => !!v || 'Number of working hours is required',
        (v: number) => (v && v <= fields.value.workingHoursPerDay) || 'Over time hours per day can not be more that hours per day'
    ],
    overTimeDays: [
        (v: number) => !!v || 'Number of worker on leave is required',
        (v: number) => (v && v <= daysInterval.value) || 'Overtime days can not be more than report days'
    ],
    overTimeWorkers: [
        (v: number) => !!v || 'Number of working hours is required',
        (v: number) => (v && v <= fields.value.workers) || 'Days off worker can not be more than report days'
    ],
    meetingTarget: [],
    meetingActual: [],
    toolboxTarget: [],
    toolboxActual: [],
    inspectionTarget: [],
    inspectionActual: [],
    drillsTarget: [],
    drillsActual: [],
    unSafeActTarget: [],
    unSafeConditionTarget: [],
})

const save = async (e: any) => {
    e.preventDefault();

    try {
        setLoading(true)

        const values = { ...fields.value }
        
        
        
        let objectValues = {
            "organization_id": values.sendToOrg == 'No' ? null : values?.organization_id,
            "start_date": moment(fields.value.start_date).format('YYYY-MM-DD'),
            "end_date": moment(fields.value.end_date).format('YYYY-MM-DD'),
            "number_of_workers": values?.workers,
            "number_of_working_hours_per_day": values?.workingHoursPerDay,
            "number_of_days_under_observation": daysInterval.value,
            "number_of_injured_or_sick_workers": values?.sickWorkers,
            "average_number_of_working_days_away_from_work": values?.offWorkingDays,
            "number_of_workers_on_leave": values?.workersOnLeave,
            "average_number_of_days_spent_on_leave": values?.leaveDays,
            "average_number_of_overtime_hours_per_day": values?.overTimeHoursParDay,
            "average_number_of_overtime_days": values?.overTimeDays,
            "number_of_workers_on_overtime": values?.overTimeWorkers,
            "hse_meetings_target": values?.meetingTarget,
            "hse_meetings_actual": values?.meetingActual,
            "toolbox_talks_target": values?.toolboxTarget,
            "toolbox_talks_actual": values?.toolboxActual,
            "hse_inspection_target": values?.inspectionTarget,
            "hse_inspection_actual": values?.inspectionActual,
            "drills_target": values?.drillsTarget,
            "drills_actual": values?.drillsActual,
            "unsafe_acts_target": values?.unSafeActTarget,
            "unsafe_conditions_target": values?.unSafeConditionTarget,
            "days_interval": daysInterval.value,
            "total_man_hours": totalManHour.value,
            "lost_working_hours": lostWorkingHours.value,
            "leave_hours": leaveHours.value,
            "total_overtime": totalOverTime.value,
            "actual_man_hour": actualManHour.value,


        }




        const resp = await reportStore.addReport(objectValues)
            .catch((error: any) => {
                console.log(error)
                throw error
            })
            .then((resp: any) => {
                return resp
            });

        if (resp?.message == 'success') {
            setLoading(false)
            // setTimeout(() => {
            //     formContainer.value.reset()
            // }, 2000)
            router.push('hse-statistics')
        }

        setLoading(false)



    } catch (error) {
        console.log(error)
        setLoading(false)
    }

}
</script>

<template>
    <div>
        <BaseBreadcrumb :title="page.title" :breadcrumbs="breadcrumbs"></BaseBreadcrumb>
        <v-row>
            <v-col cols="12" md="8">
                <UiParentCard title="">
                    <!-- <v-card> -->
                    <v-card-text>
                        <div class="d-flex justify-space-between">
                            <h3 class="text-h3">Create HSE Report</h3>
                        </div>
                    </v-card-text>
                    <v-divider></v-divider>

                    <v-card-text>

                        <VForm v-model="valid" ref="formContainer" fast-fail lazy-validation @submit.prevent="save"
                            class="py-1">
                            <VRow class="mt-5 mb-3">

                                <VCol cols="12" md="6">
                                    <v-label class="text-subtitle-1 font-weight-medium pb-1">Report Start
                                        Date</v-label>
                                    <VueDatePicker input-class-name="dp-custom-input" v-model="fields.start_date"
                                        @date-update="fields.end_date = new Date()" :max-date="new Date()"
                                        :enable-time-picker="false" required>
                                    </VueDatePicker>
                                </VCol>
                                <VCol cols="12" md="6">
                                    <v-label class="text-subtitle-1 font-weight-medium pb-1">Report End
                                        Date</v-label>
                                    <VueDatePicker input-class-name="dp-custom-input" :disabled='!fields.start_date'
                                        v-model="fields.end_date" :enable-time-picker="false" :max-date="new Date()"
                                        :min-date="fields.start_date ? new Date(fields.start_date) : new Date() "
                                        required>
                                    </VueDatePicker>
                                </VCol>

                                <template v-if="fields.end_date && fields.start_date">
                                    <VCol cols="12" md="6">
                                        <v-label class="text-subtitle-1 font-weight-medium pb-1">Number of
                                            worker(s)</v-label>
                                        <VTextField type="number" v-model="fields.workers" :rules="fieldRules.workers"
                                            required variant="outlined" label="Number of Workers">
                                        </VTextField>
                                    </VCol>
                                    <VCol cols="12" md="6">
                                        <v-label class="text-subtitle-1 font-weight-medium pb-1">Number of working hours
                                            per day</v-label>
                                        <VTextField type="number" v-model="fields.workingHoursPerDay"
                                            :rules="fieldRules.workingHoursPerDay" required variant="outlined"
                                            label="Number of Working Hours">
                                        </VTextField>
                                    </VCol>

                                    <template v-if="fields.workers && fields.workingHoursPerDay">
                                        <VCol cols="12" md="6">
                                            <v-label class="text-subtitle-1 font-weight-medium pb-1">Number of injured
                                                or sick worker(s)</v-label>
                                            <VTextField type="number" v-model="fields.sickWorkers"
                                                :rules="fieldRules.sickWorkers" required variant="outlined"
                                                label="Number of Sick Worker">
                                            </VTextField>
                                        </VCol>
                                        <VCol cols="12" md="6">
                                            <v-label class="text-subtitle-1 font-weight-medium pb-1">Average number of
                                                days away from work</v-label>
                                            <VTextField type="number" v-model="fields.offWorkingDays"
                                                :rules="fieldRules.offWorkingDays" required variant="outlined"
                                                label="Number of Working Hours">
                                            </VTextField>
                                        </VCol>


                                        <template v-if="fields.sickWorkers && fields.offWorkingDays">
                                            <VCol cols="12" md="6">
                                                <v-label class="text-subtitle-1 font-weight-medium pb-1">Number of
                                                    workers on leave</v-label>
                                                <VTextField type="number" v-model="fields.workersOnLeave"
                                                    :rules="fieldRules.workersOnLeave" required variant="outlined"
                                                    label="Number of Sick Worker">
                                                </VTextField>
                                            </VCol>
                                            <VCol cols="12" md="6">
                                                <v-label class="text-subtitle-1 font-weight-medium pb-1">Average number
                                                    of days spent on leave</v-label>
                                                <VTextField type="number" v-model="fields.leaveDays"
                                                    :rules="fieldRules.leaveDays" required variant="outlined"
                                                    label="Number of Working Hours">
                                                </VTextField>
                                            </VCol>


                                            <template v-if="fields.workersOnLeave && fields.leaveDays">
                                                <VCol cols="12" md="4">
                                                    <v-label class="text-subtitle-1 font-weight-medium pb-1">Average
                                                        number of overtime hours per day</v-label>
                                                    <VTextField type="number" v-model="fields.overTimeHoursParDay"
                                                        :rules="fieldRules.overTimeHoursParDay" required
                                                        variant="outlined" label="Number of Sick Worker">
                                                    </VTextField>
                                                </VCol>
                                                <VCol cols="12" md="4">
                                                    <v-label class="text-subtitle-1 font-weight-medium pb-1">Average
                                                        number of overtime days</v-label>
                                                    <VTextField type="number" v-model="fields.overTimeDays"
                                                        :rules="fieldRules.overTimeDays" required variant="outlined"
                                                        label="Number of Working Hours">
                                                    </VTextField>
                                                </VCol>
                                                <VCol cols="12" md="4">
                                                    <v-label class="text-subtitle-1 font-weight-medium pb-1">Number of
                                                        workers on overtime</v-label>
                                                    <VTextField type="number" v-model="fields.overTimeWorkers"
                                                        :rules="fieldRules.overTimeWorkers" required variant="outlined"
                                                        label="Number of Working Hours">
                                                    </VTextField>
                                                </VCol>

                                                <template v-if="actualManHour">
                                                    <VCol cols="12" md="6">
                                                        <v-label class="text-subtitle-1 font-weight-medium pb-1">HSE
                                                            Meetings Target</v-label>
                                                        <VTextField type="number" v-model="fields.meetingTarget"
                                                            :rules="fieldRules.meetingTarget" required
                                                            variant="outlined" label="Number of Sick Worker">
                                                        </VTextField>
                                                    </VCol>
                                                    <VCol cols="12" md="6">
                                                        <v-label class="text-subtitle-1 font-weight-medium pb-1">HSE
                                                            Meetings Actual</v-label>
                                                        <VTextField type="number" v-model="fields.meetingActual"
                                                            :rules="fieldRules.meetingActual" required
                                                            variant="outlined" label="Number of Working Hours">
                                                        </VTextField>
                                                    </VCol>
                                                    <VCol cols="12" md="6">
                                                        <v-label class="text-subtitle-1 font-weight-medium pb-1">Toolbox
                                                            Talks Target</v-label>
                                                        <VTextField type="number" v-model="fields.toolboxTarget"
                                                            :rules="fieldRules.toolboxTarget" required
                                                            variant="outlined" label="Number of Sick Worker">
                                                        </VTextField>
                                                    </VCol>
                                                    <VCol cols="12" md="6">
                                                        <v-label class="text-subtitle-1 font-weight-medium pb-1">Toolbox
                                                            Talks Actual</v-label>
                                                        <VTextField type="number" v-model="fields.toolboxActual"
                                                            :rules="fieldRules.toolboxActual" required
                                                            variant="outlined" label="Number of Working Hours">
                                                        </VTextField>
                                                    </VCol>
                                                    <VCol cols="12" md="6">
                                                        <v-label class="text-subtitle-1 font-weight-medium pb-1">HSE
                                                            Audit/Inspections Target</v-label>
                                                        <VTextField type="number" v-model="fields.inspectionTarget"
                                                            :rules="fieldRules.inspectionTarget" required
                                                            variant="outlined" label="Number of Sick Worker">
                                                        </VTextField>
                                                    </VCol>
                                                    <VCol cols="12" md="6">
                                                        <v-label class="text-subtitle-1 font-weight-medium pb-1">HSE
                                                            Audit/Inspections Actual</v-label>
                                                        <VTextField type="number" v-model="fields.inspectionActual"
                                                            :rules="fieldRules.inspectionActual" required
                                                            variant="outlined" label="Number of Working Hours">
                                                        </VTextField>
                                                    </VCol>
                                                    <VCol cols="12" md="6">
                                                        <v-label class="text-subtitle-1 font-weight-medium pb-1">Drills
                                                            Target</v-label>
                                                        <VTextField type="number" v-model="fields.drillsTarget"
                                                            :rules="fieldRules.drillsTarget" required variant="outlined"
                                                            label="Number of Sick Worker">
                                                        </VTextField>
                                                    </VCol>
                                                    <VCol cols="12" md="6">
                                                        <v-label class="text-subtitle-1 font-weight-medium pb-1">Drills
                                                            Actual</v-label>
                                                        <VTextField type="number" v-model="fields.drillsActual"
                                                            :rules="fieldRules.drillsActual" required variant="outlined"
                                                            label="Number of Working Hours">
                                                        </VTextField>
                                                    </VCol>
                                                    <VCol cols="12" md="12">
                                                        <v-label class="text-subtitle-1 font-weight-medium pb-1">Unsafe
                                                            Acts (UA) Target</v-label>
                                                        <VTextField type="number" v-model="fields.unSafeActTarget"
                                                            :rules="fieldRules.unSafeActTarget" required
                                                            variant="outlined" label="Number of Sick Worker">
                                                        </VTextField>
                                                    </VCol>
                                                    <VCol cols="12" md="6">
                                                        <v-label class="text-subtitle-1 font-weight-medium pb-1">Unsafe
                                                            Conditions (UC) Target</v-label>
                                                        <VTextField type="number" v-model="fields.unSafeConditionTarget"
                                                            :rules="fieldRules.unSafeConditionTarget" required
                                                            variant="outlined" label="Number of Sick Worker">
                                                        </VTextField>
                                                    </VCol>


                                                </template>

                                            </template>

                                        </template>
                                    </template>

                                </template>



                                <VCol cols="12" lg="12" class="text-right">

                                    <v-btn color="primary" type="submit" :loading="loading" :disabled="!valid"
                                        @click="save">
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
                    <!-- </v-card> -->
                </UiParentCard>

            </v-col>

            <v-col cols="12" md="4">
                <UiParentCard title="">
                    <v-card-text>
                        <div class="d-flex justify-space-between">
                            <h3 class="text-h3">Report Overview</h3>
                        </div>
                    </v-card-text>
                    <v-divider></v-divider>

                    <v-card-text>
                        <v-table>
                            <thead>
                                <tr>
                                    <th class="text-left">

                                    </th>
                                    <th class="text-left">

                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Working Days</td>
                                    <td>{{ daysInterval }}</td>
                                </tr>
                                <tr>
                                    <td>Target Man Hours</td>
                                    <td>{{ totalManHour }}</td>
                                </tr>
                                <tr>
                                    <td>Lost Working Hours</td>
                                    <td>{{ lostWorkingHours }}</td>
                                </tr>
                                <tr>
                                    <td>Total Leave Hours</td>
                                    <td>{{ leaveHours }}</td>
                                </tr>
                                <tr>
                                    <td>Total Overtime</td>
                                    <td>{{ totalOverTime }}</td>
                                </tr>
                                <tr>
                                    <td>Actual Man Hours</td>
                                    <td>{{ actualManHour }}</td>
                                </tr>
                            </tbody>
                        </v-table>
                    </v-card-text>
                </UiParentCard>


            </v-col>
        </v-row>
    </div>
</template>
