<template>
    <div class="p-3">
        <BreadCrumbComponent tab_title="Dashboard"></BreadCrumbComponent>
        <!-- <div class="d-flex justify-content-center">
            <img src="images\under_maintenance.png" alt="Under Maintenance" style="text-align: center;"> 
        </div> -->
        <div class="row">       
            <div class="col-lg-12">
                <div class="card" style="background-color: #f18f4e; color: #fff">
                    <div class="card-body dash-title">
                        Sales Chart
                    </div>
                </div>
                <div class="card">
                    <div class="card-body">
                        <canvas id="saleChart" style="height: 400px !important;"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-6">
                <div class="card" style="background-color: #f18f4e; color: #fff;">
                    <div class="card-body dash-title">
                        Master Data
                    </div>
                </div>
                <div class="card">
                    <div class="card-body">
                        <canvas id="masterDataBarChart"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card" style="background-color: #f18f4e; color: #fff;">
                    <div class="card-body dash-title">
                        Supplier
                    </div>
                </div>
                <div class="card">
                    <div class="card-body">
                        <canvas id="supplierPolarAreaChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style>
    .dash-title{
        font-size: 20px;
        font-weight: bold;
        text-align: center;
    }
</style>

<script>    
import BreadCrumbComponent from "./partials/BreadCrumbComponent.vue";   
import { Chart } from 'chart.js/auto';
import axios from 'axios';

export default{

    data(){
        return{
                data : [],
        }
    },
    components: {
        BreadCrumbComponent
    },
    methods: {
        getSupplierCounts() {
            axios.get('/dashboard/supplierCount') // Make a GET request to fetch supplier counts
                .then(response => {
                const data = response.data; // Get the response data
                this.createPolarAreaChart(data.labels, data.values); // Call method to create polar area chart with received data
                })
                .catch(error => {
                console.error('Error fetching supplier counts:', error);
                });
        },
        createPolarAreaChart(labels, values) {
            new Chart(document.getElementById('supplierPolarAreaChart'), {
                type: 'polarArea',
                data: {
                labels: labels,
                datasets: [{
                    label: 'Supplier Counts',
                    data: values,
                    backgroundColor: [
                    'rgba(255, 99, 132, 0.5)', // Example background color
                    'rgba(54, 162, 235, 0.5)', // Example background color
                    'rgba(255, 206, 86, 0.5)', // Example background color
                    // Add more colors as needed
                    ],
                }]
                },
                options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    r: {
                    suggestedMin: 0, // Set the minimum value for the radial axis
                    }
                }
                }
            });
        },
        getMasterDataCounts() {
            axios.get('/dashboard/masterdataCount') // Make a GET request to fetch master data counts
                .then(response => {
                const data = response.data; // Get the response data
                this.createBarChart(data.labels, data.values); // Call method to create bar chart with received data
                })
                .catch(error => {
                console.error('Error fetching master data counts:', error);
                });
        },
        createBarChart(labels, values) {
            new Chart(document.getElementById('masterDataBarChart'), {
                type: 'bar',
                data: {
                labels: labels,
                datasets: [{
                    label: 'Master Data Counts',
                    data: values,
                    backgroundColor: 'rgba(54, 162, 235, 0.5)', // Example background color
                    borderColor: 'rgba(54, 162, 235, 1)', // Example border color
                    borderWidth: 1,
                }]
                },
                options: {
                        scales: {
                            yAxes: [{
                                ticks: {
                                    beginAtZero: true
                                }
                            }]
                        }
                    }
            });
        },
        getSalesData() {
            axios.get('/dashboard/salesData')
                .then(response => {
                    const data = response.data;
                    this.createLineChart(data.labels, data.values);
                })
                .catch(error => {
                    console.error('Error fetching sales data:', error);
                });
        },

        createLineChart(labels, values) {
            new Chart(document.getElementById('saleChart'), {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Sales',
                        data: values,
                        borderColor: 'rgb(75, 192, 192)',
                        tension: 0.1,
                    }]
                },
                options: {
                    maintainAspectRatio: false, 
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        },
        },
        mounted() {
            this.getSupplierCounts();
            this.getMasterDataCounts();
            this.getSalesData(); 
        },
}

</script>
