<template>
    <div class="p-3">
        <BreadCrumbComponent tab_title="Dashboard"></BreadCrumbComponent>

        <!-- <div class="d-flex justify-content-center">
            <img src="images\under_maintenance.png" alt="Under Maintenance" style="text-align: center;"> 
        </div> -->
        <div class="row">       
            <div class="col-lg-12">
                <div class="card" style="background-color: #f18f4e; color: #fff;">
                    <div class="card-body dash-title">
                        Sales
                    </div>
                </div>
                <div class="card">
                    <div class="card-body">
                        <canvas id=""></canvas>
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
        getBalance(){
            axios.get('/dashboardCount/counts')
            .then(response => {
                this.data = response.data;
            })
            .catch(error => {
                console.error('Error fetching balance data', error);
            });
        },
        stocksBarChart() {
            axios.get('/dashboard/productStocks')
            .then(response => {
                const data = response.data;
                const labels = data.labels;
                const values = data.values;

                // Create a bar chart using Chart.js
                new Chart(document.getElementById('stocksBarChart'), {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Product Stocks',
                            backgroundColor: 'rgba(54, 162, 235, 0.5)',
                            borderColor: 'rgba(54, 162, 235, 1)',
                            borderWidth: 1,
                            data: values
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
            })
            .catch(error => {
                console.error('Error fetching stock data', error);
            });
        },
        statusCount() {
            axios.get('/dashboard/statusCount')
            .then(response => {
                const data = response.data;
                const borrowedCount = data.borrowedCount;
                const sellingCount = data.sellingCount;
                const bothCount = data.bothCount;

                // Create a doughnut chart using Chart.js
                new Chart(document.getElementById('doughnutChart'), {
                    type: 'doughnut',
                    data: {
                        labels: ['Borrowing', 'Selling'],
                        datasets: [{
                            label: 'Status Counts',
                            data: [borrowedCount, sellingCount],
                            backgroundColor: [
                                'rgba(255, 99, 132, 0.5)', 
                                'rgba(54, 162, 235, 0.5)', 
                            ],
                            borderColor: [
                                'rgba(255, 99, 132, 1)',
                                'rgba(54, 162, 235, 1)',
                            ],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        // You can add more options here as needed
                    }
                });
            })
            .catch(error => {
                console.error('Error fetching status counts:', error);
            });
        },
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
        }
      
        },
        mounted() {
            this.stocksBarChart();
            this.statusCount();
            this.getBalance();
            this.getSupplierCounts();
            this.getMasterDataCounts();
        },
}

</script>
