<script setup lang="ts">
import { ref, computed, onMounted, watch } from 'vue';

import { useDashboardStore } from '@/stores/dashboardStore';
import { useCustomizerStore } from '@/stores/customizer';
import { useAccountStore } from '@/stores/accountStore';
import { useAuthStore } from '@/stores/auth';
import Swal from 'sweetalert2'

const page = ref({ title: 'Home' });
const breadcrumbs = ref([
    {
        text: 'Dashboard',
        disabled: false,
        href: '#'
    },
    {
        text: 'Home',
        disabled: true,
        href: '#'
    }
]);


const dashboardStore = useDashboardStore();
const customizer = useCustomizerStore();
const accountStore = useAccountStore();
const authStore = useAuthStore();

onMounted(() => {
    dashboardStore.getData();
});


const getAuthUser: any = computed(() => (authStore.loggedUser));
const getData: any = computed(() => (dashboardStore.data));
const textColor: any = computed(() => (customizer.textColor));

const actionChartLabel: any = computed(() => (getData.value?.actionChart?.map((item: any) => ([item.name]))));
const actionChartCount: any = computed(() => (getData.value?.actionChart?.map((item: any) => (item.count))));
const observationChartLabel: any = computed(() => (getData.value?.observationChart?.map((item: any) => ([item.name]))));
const observationChartCount: any = computed(() => (getData.value?.observationChart?.map((item: any) => (item.count))));

/* Chart */
const ActionChartOptions = ref({

    chart: {
        height: 275,
        type: 'bar',
        fontFamily: `inherit`,
        toolbar: {
            show: false
        }
    },
    // colors: [getLight100.value, getLight100.value, getPrimary.value, getLight100.value, getLight100.value, getLight100.value],
    plotOptions: {
        bar: {
            borderRadius: 4,
            columnWidth: '45%',
            distributed: true,
            endingShape: 'rounded'
        }
    },
    dataLabels: {
        enabled: false
    },
    legend: {
        show: false
    },
    grid: {
        yaxis: {
            lines: {
                show: false
            }
        }
    },
    xaxis: {

        labels: {
            show: true,
            style: {
                colors: textColor.value,
                fontSize: '12px',
            },
        },
        categories: actionChartLabel.value,
        // categories: [['Pending'], ['Unassigned'], ['Assigned'], ['Rejected'], ['Accepted'], ['completed']],
        axisBorder: {
            show: false
        }
    },
    yaxis: {
        labels: {
            show: false
        }
    },
    tooltip: {
        theme: 'dark',
        x: {
            format: 'dd/MM/yy HH:mm'
        }
    }

});
const ObservationChartOptions = ref({

    chart: {
        height: 275,
        type: 'bar',
        fontFamily: `inherit`,
        toolbar: {
            show: false
        }
    },
    // colors: [getLight100.value, getLight100.value, getPrimary.value, getLight100.value, getLight100.value, getLight100.value],
    plotOptions: {
        bar: {
            borderRadius: 4,
            columnWidth: '45%',
            distributed: true,
            endingShape: 'rounded'
        }
    },
    dataLabels: {
        enabled: false
    },
    legend: {
        show: false
    },
    grid: {
        yaxis: {
            lines: {
                show: false
            }
        }
    },
    xaxis: {
        labels: {
            show: true,
            style: {
                colors: textColor.value,
                fontSize: '12px',
            },
        },
        categories: observationChartLabel.value,
        // categories: [['Unsafe Act'], ['Unsafe Condition'], ['Near Miss'], ['Medical Treatment Case'], ['Fatality'], ['Dangerous Occurrence']],
        axisBorder: {
            show: false
        }
    },
    yaxis: {
        labels: {
            show: false
        }
    },
    tooltip: {
        theme: 'dark',
        x: {
            format: 'dd/MM/yy HH:mm'
        }
    }

});
const ActionChart = ref({
    series: [
        {
            name: '',
            data: actionChartCount.value,
            // data: [10, 20, 20, 30, 50, 70]
        }
    ]
});
const ObservationChart = ref({
    series: [
        {
            name: '',
            // data: [60, 40, 30, 70, 10, 90]
            data: observationChartCount.value
        }
    ]
});

watch(() => getData.value, (value) => {
    ActionChartOptions.value.xaxis.categories = actionChartLabel.value
    ObservationChartOptions.value.xaxis.categories = observationChartLabel.value
    ActionChart.value.series[0].data = actionChartCount.value
    ObservationChart.value.series[0].data = observationChartCount.value
});


const loading = ref(false);
const setLoading = (value: boolean) => {
    loading.value = value;
}

const sendVerificationMail = async () => {

    try {
        setLoading(true)

        Swal.fire({
            title: 'Info!',
            text: 'Are you sure?',
            icon: 'info',
            confirmButtonText: 'Yes',
            cancelButtonText: 'No',
            showCancelButton: true,
            allowOutsideClick: false,
        }).then((result) => {
            if (result.isConfirmed) {


                accountStore.sendVerificationMail()
                    .catch((error: any) => {
                        throw error
                    })
                    .then((resp: any) => {

                        return resp
                    });


            }
        });

        setLoading(false)

    } catch (error) {

        setLoading(false)
    }

}


</script>

<template>
    <div>

        <v-card elevation="0" rounded="md" class="bg-lightwarning mt-6" v-if="getAuthUser?.is_email_verified == false">
            <v-card-item class="py-0">
                <v-row class="d-flex align-center">
                    <v-col cols="12" sm="7" class="pa-6">
                        <h5 class="text-h5 py-4">Email Not Verified</h5>
                        <v-btn flat color="secondary" @click="sendVerificationMail">Verify Account</v-btn>
                    </v-col>

                </v-row>
            </v-card-item>
        </v-card>


        <v-row class="mt-1">
            <v-col cols="12" lg="3" md="6" sm="6">
                <v-card elevation="10">
                    <v-card-item>
                        <div class="d-flex align-center mt-md-6 mt-1">
                            <v-avatar class="rounded-md bg-lightprimary text-primary">
                                <GridDotsIcon size="22" />
                            </v-avatar>
                            <div class="pl-4">
                                <h6 class="text-subtitle-1 textSecondary">Total Actions</h6>
                                <h3 class="text-h6">{{ getData?.action ? getData?.action : '0' }}</h3>
                            </div>
                        </div>
                    </v-card-item>
                </v-card>
            </v-col>
            <v-col cols="12" lg="3" md="6" sm="6" class="d-flex justify-lg-start justify-end">
                <v-card elevation="10">
                    <v-card-item>
                        <div class="d-flex align-center mt-md-6 mt-1">
                            <v-avatar class="rounded-md bg-grey100 textSecondary">
                                <GridDotsIcon size="22" class="text-medium-emphasis" />
                            </v-avatar>
                            <div class="pl-4">
                                <h6 class="text-subtitle-1 textSecondary">Total Inspections</h6>
                                <h3 class="text-h6">{{ getData?.inspection ? getData?.inspection : '0' }}</h3>
                            </div>
                        </div>
                    </v-card-item>
                </v-card>
            </v-col>
            <v-col cols="12" lg="3" md="6" sm="6">
                <v-card elevation="10">
                    <v-card-item>
                        <div class="d-flex align-center mt-md-6 mt-1">
                            <v-avatar class="rounded-md bg-lightprimary text-primary">
                                <GridDotsIcon size="22" />
                            </v-avatar>
                            <div class="pl-4">
                                <h6 class="text-subtitle-1 textSecondary">Investigation</h6>
                                <h3 class="text-h6">{{ getData?.investigation ? getData?.investigation : '0' }}</h3>
                            </div>
                        </div>
                    </v-card-item>
                </v-card>
            </v-col>
            <v-col cols="12" lg="3" md="6" sm="6" class="d-flex justify-lg-start justify-end">
                <v-card elevation="10">
                    <v-card-item>
                        <div class="d-flex align-center mt-md-6 mt-1">
                            <v-avatar class="rounded-md bg-grey100 textSecondary">
                                <GridDotsIcon size="22" class="text-medium-emphasis" />
                            </v-avatar>
                            <div class="pl-4">
                                <h6 class="text-subtitle-1 textSecondary">Total Report</h6>
                                <h3 class="text-h6">{{ getData?.report ? getData?.report : '0' }}</h3>
                            </div>
                        </div>
                    </v-card-item>
                </v-card>
            </v-col>
        </v-row>

        <v-row>
            <v-col cols="12" md="8">

                <v-card elevation="10" v-if="ActionChartOptions.xaxis.categories">
                    <v-card-item>
                        <v-card-title class="text-h5">Action</v-card-title>
                        <v-card-subtitle class="text-subtitle-1 textSecondary">All</v-card-subtitle>
                        <apexchart type="bar" height="275" :options="ActionChartOptions" :series="ActionChart.series">
                        </apexchart>
                    </v-card-item>
                </v-card>
            </v-col>
            <v-col cols="12" md="8">

                <v-card elevation="10" v-if="ObservationChartOptions.xaxis.categories">
                    <v-card-item>
                        <v-card-title class="text-h5">Observation</v-card-title>
                        <v-card-subtitle class="text-subtitle-1 textSecondary">All</v-card-subtitle>
                        <apexchart type="bar" height="275" :options="ObservationChartOptions"
                            :series="ObservationChart.series">
                        </apexchart>
                    </v-card-item>
                </v-card>
            </v-col>
        </v-row>

        <!-- </v-row> -->
    </div>
</template>
