<template>
    <div class="p-3">
        <BreadCrumbComponent tab_title="Product History"></BreadCrumbComponent>
        <div class="d-flex justify-content-center">
            <img src="images\under_maintenance.png" alt="Under Maintenance" style="text-align: center;"> 
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
    methods: {}
}

</script>
