<style>
    .sold-out .card-body {
        background-color: #f8f9fa; /* Change background color for sold-out cards */
        opacity: 0.5; /* Set opacity to 80% */
    }

    .btn-sold-out {
        color: #000 !important; /* Change button text color to black */
    }
</style>

<template>
    <div class="p-3">
        <BreadCrumbComponent tab_title="Buy Tools"></BreadCrumbComponent>
        <div class="row">
            <div class="col-md-4" v-for="(tool, index) in data" :key="index">
                <!-- Card component for each tool -->
                <div class="card" :class="{ 'sold-out': tool.is_sold_out }">
                    <div class="card-body">
                        <!-- Display tool information -->
                        <div class="d-flex justify-content-between">
                            <div style="width: 50%;">
                                <h5 class="card-title">{{ tool.name }}</h5>
                                <p class="card-text">Price: {{ tool.price }}</p>
                                <p class="card-text">Product Code: {{ tool.product_code }}</p>
                            </div>
                            <div style="width: 50%;">
                                <img v-if="tool.image" :src="'/images/' + tool.image" alt="Tool Image" class="img-fluid">
                                <p v-else>No Image</p>
                            </div>
                        </div>
                        <button :class="tool.is_sold_out ? 'btn btn-dark' : 'btn btn-success'"
                                v-on:click="showDetails(tool)"
                                :disabled="tool.is_sold_out">
                            {{ tool.is_sold_out ? 'Sold Out' : 'Buy Tool' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <ModalComponent :id="modalId" :title="modalTitle" :size="modalSize" :position="modalPosition">
            <template #modalHeader>
                <div class="m-auto">
                    <h4>Purchase a Tool</h4>
                </div>
            </template>
            <template #modalBody>
                <div class="row">
                    <div class="col-12 pb-2">
                        <input type="hidden" v-model="dataValues.power_tools_id">
                        <label for="">PowerTools</label>
                        <input type="text" v-model="selectedTool.name" class="form-control" disabled> 
                    </div>  
                    <div class="col-12 pb-2">
                        <input type="hidden" v-model="dataValues.product_code">
                        <label for="">Product Code</label>
                        <input type="text" v-model="selectedTool.product_code" class="form-control" disabled> 
                    </div>
                    <div class="col-12 pb-2">
                        <label for="">Price</label>
                        <input type="number" v-model="selectedTool.price" class="form-control" disabled> 
                    </div>  
                </div>
            </template>
            <template #modalFooter>
                <div class="text-right">
                    <button class="btn btn-success" v-on:click="storeData">Buy</button>
                </div>
            </template>
        </ModalComponent>

    </div>
</template>

<script>    
import BreadCrumbComponent from "./partials/BreadCrumbComponent.vue";   
import FormComponent from "./partials/FormComponent.vue";   
import ModalComponent from "./partials/ModalComponent.vue";
import Swal from 'sweetalert2'
import axios from 'axios';

export default{

    data(){
        return{
            data : [],
            errors: [],
            selectedTool: [],
            dataValues: {},
            modalId : 'modal-buytools',
            modalTitle : 'Buy Tools',
            modalPosition: 'modal-dialog-centered',
            modalSize : 'modal-md',
        }
    },
    components: {
        FormComponent,
        ModalComponent,
        BreadCrumbComponent
    },
    methods: {
        showDetails(tool) {
            this.selectedTool = tool;
            this.dataValues = {
                power_tools_id: tool.id,
            };
            $('#' + this.modalId).modal('show');
        },
        getData() {
            axios.get('/powertools/show').then(response => {
                this.data = response.data.data;
            })
        },
        clearInputs() {
            this.dataValues = {
                name: '',
            }
            this.errors = [];
        },
        storeData() {
            axios.post('/buytools/store', this.dataValues).then(response => {
                if(response.status === 200) {
                    Swal.fire({
                        title: "Success",
                        text: response.data.message,
                        icon: 'success',
                        timer: 3000
                    });
                }
                this.getData();
                $('#' + this.modalId).modal('hide');
            })
            .catch(errors => {
                if(errors.response.data.message.length > 0) {
                    Swal.fire({
                        title: "Warning",
                        text: errors.response.data.message,
                        icon: 'warning',
                        timer: 3000
                    });

                    this.errors = errors.response.data.errors;
                }
            })
        },
    },
    mounted() {
            this.getData();
        }
        


}

</script>
