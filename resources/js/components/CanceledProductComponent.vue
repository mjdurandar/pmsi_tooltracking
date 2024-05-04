<template>
    <div class="p-3">
        <BreadCrumbComponent tab_title="Canceled Products"></BreadCrumbComponent>
    
        <!-- FILTER PRODUCT -->
        <div class="d-flex justify-content-between">
            <div>
                <div class="row mb-3">
                    <div class="col-lg-4">
                        <select v-model="selectedBrand" class="form-control">
                            <option value="" disabled selected>Brand</option>
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
                    <div class="col-lg-4">
                        <select v-model="selectedTool" class="form-control">
                            <option value="" disabled selected>Tools</option> 
                            <option value="Drill">Drill</option>
                            <option value="Screwdriver">Screwdriver</option>
                            <option value="Wrench">Wrench</option>
                            <option value="Grinder">Grinder</option>
                            <option value="Jigsaw">Jigsaw</option>
                            <option value="Saw">Saw</option>
                        </select>
                    </div>
                    <div>
                        <button class="btn btn-primary" @click="filterData">Search</button>
                        <button class="btn btn-success ml-1" @click="refresh"><i class="fas fa-sync-alt"></i></button>
                    </div>
                </div>
            </div>
            <div>
            </div>
        </div>
        <!-- PRODUCT CARD -->
        <div class="row">
            <div class="col-md-4" v-for="(product, index) in data" :key="index">
                <div class="card" :class="{ 'is_for_sale': product.is_for_sale }">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div style="width: 50%;">
                                <h4>{{ product.brand }} {{ product.tool }}</h4>
                                <div style="color: red; font-weight: 500;" >
                                    <p>Product Canceled</p>
                                </div>
                                <!-- <p class="card-text">Voltage: {{ product.voltage }}</p>
                                <p class="card-text">Power Source: {{ product.powerSources }}</p>
                                <p class="card-text">Weight: {{ product.weight }}</p>
                                <p class="card-text">Dimensions: {{ product.dimensions }}</p>
                                <p class="card-text">Material: {{ product.material }}</p> -->
                            </div>
                            <div style="width: 50%;">
                                <img v-if="product.image" :src="'/images/' + product.image" alt="Product Image" class="img-fluid" style="height: 250px;">
                                <p v-else>No Stocks</p>
                            </div>
                        </div>
                            <div>
                                <button class="btn btn-success" @click="viewProduct(product)">View</button>
                            </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- VIEW PRODUCT -->
        <ModalComponent :id="modalId" :title="modalTitle" :size="modalSize" :position="modalPosition">
            <template #modalHeader>    
            </template>
            <template #modalBody>
                <div class="row">
                    <div class="col-6 text-center m-auto" v-if="dataValues.image">
                        <img :src="'/images/' + dataValues.image" alt="Current Image" class="img-fluid" style="height:300px;">
                    </div>
                    <div class="col-6">
                        <p>
                            {{ this.dataValues.brand }} {{ this.dataValues.tool }} with the voltage of {{ this.dataValues.voltage }}, dimension of {{ this.dataValues.dimensions }}, weight of {{ this.dataValues.weight }} and powerSources of {{ this.dataValues.powerSources }}.
                        </p>
                        <p>
                            We canceled it because : {{this.dataValues.description}}
                        </p>
                        <p>
                            Consider checking your product before releasing it again.
                        </p>
                        <p>
                            Once you're confident that everything is in order, proceed to release your product by clicking the "Release" button below.
                        </p>
                    </div>
                </div>
            </template>
            <template #modalFooter>
                <div class="text-right">
                    <button class="btn btn-success" v-on:click="releasedProduct">Release</button>
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
                selectedBrand : '',
                selectedTool : '',
                selectedStatus : '',
                columns : ['name' ,'action'],
                errors: [],
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
                modalId : 'modal-product',
                modalTitle : 'Add a Product',
                modalPosition: 'modal-dialog-centered',
                modalSize : 'modal-lg',
        }
    },
    components: {
        FormComponent,
        ModalComponent,
        BreadCrumbComponent,
    },
    methods: {
        getData() {
            axios.get('/canceled-product/show').then(response => {
                this.data = response.data.data;
            })
        },
        clearInputs() {
            this.dataValues = {
                name: '',
            }
            this.errors = [];
        },
        viewProduct(product) {
            this.dataValues = product;
            $('#' + this.modalId).modal('show');
        },
        releasedProduct() {
            axios.post('/canceled-product/release', this.dataValues).then(response => {
                this.getData();
                $('#' + this.modalId).modal('hide');
                Swal.fire({
                    icon: 'success',
                    title: 'Product Released',
                    showConfirmButton: false,
                    timer: 1500
                })
            }).catch(error => {
                this.errors = error.response.data.errors;
            })
        },
        filterData(){
            const searchData = {
                brand: this.selectedBrand,
                tool: this.selectedTool,
                status: this.selectedStatus
            };

            axios.post('/canceled-product/filterData', searchData)
            .then(response => {
                this.data = response.data.data;
                if (this.data.length === 0) {
                    Swal.fire({
                        title: "No Products available!",
                        icon: 'warning',
                        timer: 3000
                    });
                }
            })
            .catch(error => {
                Swal.fire({
                    title: "Error!",
                    text: "Failed to fetch data.",
                    icon: 'error',
                    timer: 3000
                });
                console.error(error);
            });
        },
        editClicked(props) {
            this.dataValues = props.data;
            this.modalTitle= 'Edit Data';

            axios.get('/unit/edit/' + this.dataValues.id).then(response => {
                this.dataValues = response.data.data;
                $('#' + this.modalId).modal('show');
            })
            .catch(errors => {
                if(errors.response.data.message.length > 0) {
                    Swal.fire({
                        title: "Failed",
                        text: errors.response.data.message,
                        icon: 'error',
                        timer: 3000
                    });
                    this.errors = errors.response.data.errors;
                }
            })
        },
        deleteClicked(props) {
            Swal.fire({
                title: 'Are you sure?',
                text: 'You will not be able to recover this data!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel',
            }).then((result) => {
                if (result.isConfirmed) {
                    // User confirmed, proceed with deletion
                    axios.get('/unit/destroy/' + props.data.id).then(response => {
                        if(response.status === 200) {
                            Swal.fire({
                                title: "Success",
                                text: response.data.message,
                                icon: 'success',
                                timer: 3000
                            });
                        }
                        this.getData();
                    }).catch(errors => {
                        if(errors.response.data.message.length > 0) {
                            Swal.fire({
                                title: "Failed",
                                text: errors.response.data.message,
                                icon: 'error',
                                timer: 3000
                            });
                        }
                    });
                }
            });
        },
        storeData() {
                axios.post('/unit/store', this.dataValues).then(response => {
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
                        this.errors = errors.response.data.errors;
                })
            },
        },
        mounted() {
            this.getData();
        }
        


}

</script>
