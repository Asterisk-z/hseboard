<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';

import { useDashboardStore } from '@/stores/dashboardStore';

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

onMounted(() => {
    dashboardStore.getData();
});

const getData: any = computed(() => (dashboardStore.data));

/* Chart */
const ActionChartOptions = computed(() => {
    return {
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

            categories: [['Pending'], ['Unassigned'], ['Assigned'], ['Rejected'], ['Accepted'], ['Completed']],
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
    };
});
const ObservationChartOptions = computed(() => {
    return {
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
            // labels: ['', '', 'Near Miss', 'Medical Treatment Case', 'Fatality', 'Dangerous Occurrence'],
            categories: [['Unsafe Act'], ['Unsafe Condition'], ['Near Miss'], ['Medical Treatment Case'], ['Fatality'], ['Dangerous Occurrence']],
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
    };
});
const ActionChart = {
    series: [
        {
            name: '',
            data: [0, 0, 0, 0, 0, 0]
        }
    ]
};
const ObservationChart = {
    series: [
        {
            name: '',
            data: [0, 0, 0, 0, 0, 0]
        }
    ]
};

</script>

<template>
    <div>
        <!-- <BaseBreadcrumb :title="page.title" :breadcrumbs="breadcrumbs"></BaseBreadcrumb> -->
        <!-- <v-row> -->
        <!-- <v-col cols="12" md="12">
                <UiParentCard title="Simple Title"> Welcome </UiParentCard>
            </v-col> -->
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

                <v-card elevation="10">
                    <v-card-item>
                        <v-card-title class="text-h5">Action</v-card-title>
                        <v-card-subtitle class="text-subtitle-1 textSecondary">All</v-card-subtitle>
                        <apexchart type="bar" height="275" :options="ActionChartOptions" :series="ActionChart.series">
                        </apexchart>
                    </v-card-item>
                </v-card>
            </v-col>
            <v-col cols="12" md="8">

                <v-card elevation="10">
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
