<template>
    <div class="p-3">
        <BreadCrumbComponent tab_title="Dashboard"></BreadCrumbComponent>
        <div class="mb-3">
    <div class="input-group" style="width: 15%;">
        <div class="input-group-prepend">
            <span class="input-group-text">Balance</span> <!-- Add "Balance" here -->
            <span class="input-group-text">₱</span>
        </div>
        <input type="number" class="form-control" v-model="data.balance" disabled>
    </div>
</div>

        <!-- <div class="d-flex justify-content-center">
            <img src="images\under_maintenance.png" alt="Under Maintenance" style="text-align: center;"> 
        </div> -->
        <div class="row">
            <div class="col-6">
                <div class="card" style="background-color: #f18f4e; color: #fff;">
                    <div class="card-body dash-title">
                        Product Stocks
                    </div>
                </div>
                <div class="card">
                    <div class="card-body">
                        <canvas id="stocksBarChart"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-6">
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
        </div>
        <div class="row">
            <div class="col-6">
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
            <div class="col-6">
                <div class="card" style="background-color: #f18f4e; color: #fff;">
                    <div class="card-body dash-title">
                        Selling and Borrowing
                    </div>
                </div>
                <div class="card">
                    <div class="card-body">
                        <canvas id="doughnutChart"></canvas>
                    </div>
                </div>
            </div>
            <!-- <div class="col-6">
                <div class="card" style="background-color: #f18f4e; color: #fff;">
                    <div class="card-body dash-title" >
                        Tools
                   </div>
                </div>
                <div class="card">
                    <div class="card-body">
                        <canvas id="pieChartTools"></canvas>
                   </div>
                </div>
            </div> -->
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
        // balanceLineChart() {
        //     axios.get('/dashboard/balanceData')
        //     .then(response => {
        //         const data = response.data;
        //         const balance = data.balance;
        //         const timestamp = data.timestamp;

        //         // Create a line chart using Chart.js
        //         new Chart(document.getElementById('balanceLineChart'), {
        //             type: 'line',
        //             data: {
        //                 labels: [timestamp], // Use the timestamp as the x-axis label
        //                 datasets: [{
        //                     label: 'Balance',
        //                     data: [balance],
        //                     borderColor: 'rgba(75, 192, 192, 1)',
        //                     borderWidth: 2,
        //                     fill: false
        //                 }]
        //             },
        //             options: {
        //                 responsive: true,
        //                 maintainAspectRatio: false,
        //                 scales: {
        //                     x: {
        //                         type: 'time',
        //                         time: {
        //                             parser: 'YYYY-MM-DD HH:mm:ss', // Format of the timestamp
        //                             tooltipFormat: 'll HH:mm:ss' // Format of the tooltip
        //                         },
        //                         title: {
        //                             display: true,
        //                             text: 'Time'
        //                         }
        //                     },
        //                     y: {
        //                         display: true,
        //                         title: {
        //                             display: true,
        //                             text: 'Balance'
        //                         },
        //                         ticks: {
        //                             beginAtZero: true
        //                         }
        //                     }
        //                 }
        //             }
        //         });
        //     })
        //     .catch(error => {
        //         console.error('Error fetching balance data:', error);
        //     });
        // }




        // borrowedChart(){
        //     axios.get('/dashboardCount/counts')
        //     .then(response => {
        //         const ctx = document.getElementById('borrowedChart');
        //         const totalPerDay = response.data.borrowedCount;
        //         const labels = totalPerDay.map(item => item.borrowed_at);
        //         const data = totalPerDay.map(item => item.total);
        //         new Chart(ctx, {
        //             type: 'line',
        //             data: {
        //                 labels: labels,
        //                 datasets: [{
        //                     label: 'Borrowed',
        //                     data: data,
        //                     borderWidth: 1,
        //                     backgroundColor: 'rgba(75, 192, 192, 0.2)',
        //                     borderColor: 'rgba(75, 192, 192, 1)',
        //                 }]
        //             },
        //             options: {
        //                 scales: {
        //                     y: {
        //                         beginAtZero: true
        //                     }
        //                 }
        //             }
        //         });
        //     })
        //     .catch(error => {
        //         console.error('Error fetching sales data', error);
        //     });
        // },
        // dashboard(){
        //     console.log(this.data);
        //     axios.get('/dashboardCount/counts')
        //     .then(response => {
        //     const ctx = document.getElementById('myChart');
        //     new Chart(ctx, {
        //     type: 'bar',
        //     data: {
        //         labels: ['Project Site', 'Units', 'Category', 'Supplier'],
        //         datasets: [{
        //         label: 'Master Data',
        //         data: [response.data.projectCount, response.data.unitCount, response.data.categoryCount, response.data.supplierCount],
        //         borderWidth: 1
        //         }]
        //     },
        //             options: {
        //                 scales: {
        //                 y: {
        //                     beginAtZero: true
        //                 }
        //                 }
        //             }
        //         });
        //     })
        //     .catch(error => {
        //         console.error('Error fetching project data', error);
        //     });
        // },
        // piechart(){
        //     axios.get('/dashboardCount/counts')
        //     .then(response => {
        //     const ctx = document.getElementById('pieChart');
        //     new Chart(ctx, {
        //     type: 'pie',
        //     data: {
        //     labels: ['Borrowed', 'Buying'],
        //         datasets: [{
        //         label: 'Count',
        //         data: [response.data.borrowedCount, response.data.buyingCount],
        //         borderWidth: 1
        //         }]
        //     },
        //             options: {
        //                 scales: {
        //                 y: {
        //                     beginAtZero: true 
        //                 }
        //                 }
        //             }
        //         });
        //     })
        //     .catch(error => {
        //         console.error('Error fetching project data', error);
        //     });
        // },
        // piecharttools(){
        //     axios.get('/dashboardCount/counts')
        //     .then(response => {
        //     const ctx = document.getElementById('pieChartTools');
        //     new Chart(ctx, {
        //     type: 'pie',
        //     data: {
        //     labels: ['Scaffolding', 'Power Tools'],
        //         datasets: [{
        //         label: 'Count',
        //         data: [response.data.scaffoldingCounts, response.data.powertoolsCounts],
        //         borderWidth: 1
        //         }]
        //     },
        //             options: {
        //                 scales: {
        //                 y: {
        //                     beginAtZero: true
        //                 }
        //                 }
        //             }
        //         });
        //     })
        //     .catch(error => {
        //         console.error('Error fetching project data', error);
        //     });
        // },
            
        },
        mounted() {
            this.stocksBarChart();
            this.statusCount();
            this.getBalance();
            this.getSupplierCounts();
            this.getMasterDataCounts();
            // this.chartSales();
            // this.borrowedChart();
            // this.dashboard();
            // this.piechart();
            // this.piecharttools();
        },
}

</script>
