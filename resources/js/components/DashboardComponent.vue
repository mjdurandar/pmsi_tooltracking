<template>
    <div class="p-3">
        <BreadCrumbComponent tab_title="Dashboard"></BreadCrumbComponent>
        <div class="row">
            <div class="col-6">
                <div class="card" style="background-color: #f18f4e; color: #fff;">
                    <div class="card-body dash-title">
                        Sales per Day
                    </div>
                </div>
                <div class="card">
                    <div class="card-body">
                        <canvas id="salesChart"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-6">
                <div class="card" style="background-color: #f18f4e; color: #fff;">
                    <div class="card-body dash-title">
                        Master Data Count
                   </div>
                </div>
                <div class="card">
                    <div class="card-body">
                        <canvas id="myChart"></canvas>
                   </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-6">
                <div class="card" style="background-color: #f18f4e; color: #fff;">
                    <div class="card-body dash-title" >
                        Buying / Borrowing
                   </div>
                </div>
                <div class="card">
                    <div class="card-body">
                        <canvas id="pieChart"></canvas>
                   </div>
                </div>
            </div>
            <div class="col-6">
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
                data : []
        }
    },
    components: {
        BreadCrumbComponent
    },
    methods: {
        chartSales(){
                axios.get('/dashboardCount/counts')
                .then(response => {
                    const ctx = document.getElementById('salesChart');
                    const totalPerDay = response.data.totalPerDay;
                    const labels = totalPerDay.map(item => item.date);
                    const data = totalPerDay.map(item => item.total);
                    new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: labels,
                            datasets: [{
                                label: 'Sales',
                                data: data,
                                borderWidth: 1,
                                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                                borderColor: 'rgba(75, 192, 192, 1)',
                            }]
                        },
                        options: {
                            scales: {
                                y: {
                                    beginAtZero: true
                                }
                            }
                        }
                    });
                })
                .catch(error => {
                    console.error('Error fetching sales data', error);
                });
            },
            dashboard(){
                axios.get('/dashboardCount/counts')
                .then(response => {
                const ctx = document.getElementById('myChart');
                new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['Project Site', 'Units', 'Category', 'Supplier'],
                    datasets: [{
                    label: 'Master Data',
                    data: [response.data.projectCount, response.data.unitCount, response.data.categoryCount, response.data.supplierCount],
                    borderWidth: 1
                    }]
                },
                        options: {
                            scales: {
                            y: {
                                beginAtZero: true
                            }
                            }
                        }
                    });
                })
                .catch(error => {
                    console.error('Error fetching project data', error);
                });
            },
            piechart(){
                axios.get('/dashboardCount/counts')
                .then(response => {
                const ctx = document.getElementById('pieChart');
                new Chart(ctx, {
                type: 'pie',
                data: {
                labels: ['Borrowed', 'Buying'],
                    datasets: [{
                    label: 'Count',
                    data: [response.data.borrowedCount, response.data.buyingCount],
                    borderWidth: 1
                    }]
                },
                        options: {
                            scales: {
                            y: {
                                beginAtZero: true
                            }
                            }
                        }
                    });
                })
                .catch(error => {
                    console.error('Error fetching project data', error);
                });
            },
            piecharttools(){
                axios.get('/dashboardCount/counts')
                .then(response => {
                const ctx = document.getElementById('pieChartTools');
                new Chart(ctx, {
                type: 'pie',
                data: {
                labels: ['Scaffolding', 'Power Tools'],
                    datasets: [{
                    label: 'Count',
                    data: [response.data.scaffoldingCounts, response.data.powertoolsCounts],
                    borderWidth: 1
                    }]
                },
                        options: {
                            scales: {
                            y: {
                                beginAtZero: true
                            }
                            }
                        }
                    });
                })
                .catch(error => {
                    console.error('Error fetching project data', error);
                });
            },
            
        },
        mounted() {
            this.chartSales();
            this.dashboard();
            this.piechart();
            this.piecharttools();
        },
}

</script>
