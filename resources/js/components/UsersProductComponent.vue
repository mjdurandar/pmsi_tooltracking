<template>
    <div class="p-3">
        <BreadCrumbComponent tab_title="Products"></BreadCrumbComponent>
        <div class="row mb-3">
            <div class="col-lg-2">
                <select v-model="selectedBrand" class="form-control">
                    <option value="" disabled selected>Select Brand</option>
                    <option value="Bosch">Bosch</option>
                    <option value="Dewalt">Dewalt</option>
                    <option value="Makita">Makita</option>
                    <option value="Milwaukee">Milwaukee</option>
                    <option value="Black+Decker">Black+Decker</option>
                    <option value="Craftsman">Craftsman</option>
                    <option value="Hitachi">Hitachi</option>
                    <option value="Ingersoll">Ingersoll</option>
                    <option value="Porter-Cable">Porter-Cable</option>
                    <option value="Snap-on">Snap-on</option>
                    <option value="Ridgid">Ridgid</option>
                    <option value="Metabo">Metabo</option>
                </select>
            </div>
            <div class="col-lg-2">
                <select v-model="selectedTool" class="form-control">
                    <option value="" disabled selected>Select Tools</option> 
                    <option value="Drill">Drill</option>
                    <option value="Screwdriver">Screwdriver</option>
                    <option value="Wrench">Wrench</option>
                    <option value="Grinder">Grinder</option>
                    <option value="Jigsaw">Jigsaw</option>
                    <option value="Saw">Saw</option>
                </select>
            </div>
            <div class="col-lg-2">
                <button class="btn btn-primary" @click="filterData">Search</button>
                <button class="btn btn-success ml-1" @click="refresh"><i class="fas fa-sync-alt"></i></button>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4" v-for="(product, index) in data" :key="index">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div style="width: 50%;">
                                <h2 class="card-title" style="font-weight: bold;">{{ product.brand_name }} {{ product.tool_name }}</h2>
                            </div>
                            <div style="width: 50%;">
                                <img v-if="product.image" :src="'/images/' + product.image" alt="Product Image" class="img-fluid" style="height: 250px;">
                                <p v-else>No Stocks</p>
                            </div>
                        </div>
                        <button class="btn btn-warning" @click="viewProduct(product)">
                            Product Broken?
                        </button>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- SELECT SRN AND PRN CODE -->
        <ModalComponent :id="modalIdSelect" :title="modalTitle" :size="modalSize" :position="modalPosition">
            <template #modalHeader>
                <div class="m-auto">
                    <h4>Return a Product</h4>
                </div>
            </template>
            <template #modalBody>
                <div class="modal-body" style="max-height: 400px; overflow-y: auto;">
                    <div class="row">
                        <div class="col-lg-12">   
                            <label for="">Serial Number</label>
                            <input type="text" class="form-control" v-model="dataValues.serial_number">
                            <div class="text-danger" v-if="errors.serial_number">{{ errors.serial_number[0] }}</div>
                        </div>
                        <div class="col-lg-12 mt-2">   
                            <label for="">Please describe the problem</label>
                            <textarea type="text" class="form-control" v-model="dataValues.description"></textarea>
                            <div class="text-danger" v-if="errors.description">{{ errors.description[0] }}</div>
                        </div>
                    </div>
                </div>
            </template>
            <template #modalFooter>
                <div class="text-right">
                    <button class="btn btn-primary mr-2" v-on:click="proceed">Submit</button>
                </div>
            </template>
        </ModalComponent>

    </div>
</template>

<script>    
import FormComponent from "./partials/FormComponent.vue";   
import ModalComponent from "./partials/ModalComponent.vue";
import BreadCrumbComponent from "./partials/BreadCrumbComponent.vue";
import Swal from 'sweetalert2'
import axios from 'axios';

export default{

    data(){
        return{
                data : [],
                columns : ['name' ,'action'],
                errors: [],
                selectedBrand: '',
                selectedTool: '',
                options : {
                    headings : {
                        name : 'Unit',
                        action : 'Action',
                    },
                    filterable: false,
                    sortable: []
                },
                dataValues: {
                    name: '',
                },
                modalId : 'modal-unit',
                modalIdSelect : 'modal-select',
                modalTitle : 'Unit',
                modalPosition: 'modal-dialog-centered',
                modalSize : 'modal-md',
        }
    },
    components: {
        FormComponent,
        ModalComponent,
        BreadCrumbComponent,
    },
    methods: {
        addClicked(props){
            $('#' + this.modalId).modal('show');
            this.clearInputs();
        },
        getData() {
            axios.get('/users-product/show').then(response => {
                this.data = response.data.data;
            })
        },
        clearInputs() {
            this.dataValues = {
                name: '',
            }
            this.errors = [];
        },
        viewProduct(product){
            this.dataValues = product;
            $('#' + this.modalIdSelect).modal('show');
        },
        validateForm() {
            this.errors = [];

            if (!this.dataValues.serial_number) {
                this.errors.serial_number = ['Serial Number is required'];
            }

            if (!this.dataValues.description) {
                this.errors.description = ['Description is required'];
            }

            // Check if any errors are present
            if (Object.keys(this.errors).length > 0) {
                return false; // Validation failed
            }

            return true; // Validation passed
        },
        proceed()
        {   
            if(!this.validateForm())
            {
                return;
            }
            axios.post('/users-product/brokenProduct', this.dataValues).then(response => {
                    if(response.status === 200) {
                        Swal.fire({
                            title: "Success",
                            text: 'Please wait the admin will check your request and we will contact you for the return process.',
                            icon: 'success',
                            timer: 6000
                        });
                    }
                    this.getData();
                    $('#' + this.modalIdSelect).modal('hide');
                })
                .catch(errors => {
                        this.errors = errors.response.data.errors;
                })
        }
        },
        mounted() {
            this.getData();
        }
        


}

</script>
