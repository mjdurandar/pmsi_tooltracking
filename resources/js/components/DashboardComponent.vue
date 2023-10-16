<template>
    <div class="p-3">
        <BreadCrumbComponent tab_title="Dashboard"></BreadCrumbComponent>
        <div class="row">
            <div class="col-6">
                <div class="card" style="background-color: #F3A068; color: #fff;">
                    <div class="card-body dash-title">
                        Data Count
                   </div>
                </div>
                <div class="card">
                    <div class="card-body">
                        <canvas id="myChart"></canvas>
                   </div>
                </div>
            </div>
            <div class="col-6">
                <div class="card" style="background-color: #F3A068; color: #fff;">
                    <div class="card-body dash-title" >
                        Data Count
                   </div>
                </div>
                <div class="card">
                    <div class="card-body">
                        <canvas id="pieChart"></canvas>
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
            }
            
        },
        mounted() {
            this.dashboard();
            this.piechart();
        },
}

</script>
