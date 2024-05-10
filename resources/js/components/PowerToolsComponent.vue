<template>
    <div class="p-3">
        <BreadCrumbComponent tab_title="Tools and Equipment"></BreadCrumbComponent>
        <!-- FILTER DATA -->
        <div class="row mb-3">
            <div class="col-lg-2">
                <select class="form-control" v-model="supplier_name">
                    <option value="" disabled selected>Select Supplier</option>
                    <option v-for="supplier in suppliers" :value="supplier.id">{{ supplier.name }}</option>
                </select>
            </div>
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
                <select class="form-control" v-model="category_number">
                    <option value="" disabled selected>Select Category</option>
                    <option v-for="category in categories" :value="category.id">{{ category.name }}</option>
                </select>  
                <div class="text-danger" v-if="errors.category">{{ errors.category[0] }}</div>
            </div>
            <div>
                <button class="btn btn-primary" @click="filterData">Search</button>
                <button class="btn btn-success ml-1" @click="refresh"><i class="fas fa-sync-alt"></i></button>
            </div>
        </div>

        <!-- PRODUCT CARD -->
        <div class="row">
            <div class="col-md-4" v-for="(product, index) in data" :key="index">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div style="width: 50%;">
                                <h2 class="card-title" style="font-weight: bold;">{{ product.brand_name }} {{ product.tool_name }}</h2>
                                <!-- <h6 class="card-text">{{ product.category_name }}</h6> -->
                                <h6 class="card-text">{{ product.supplier_name }}</h6>
                                <h6 class="card-text">Stocks: {{ product.stocks }}</h6>
                            </div>
                            <div style="width: 50%;">
                                <img v-if="product.image" :src="'/images/' + product.image" alt="Product Image" class="img-fluid" style="height: 250px;">
                                <p v-else>No Stocks</p>
                            </div>
                        </div>
                        <button class="btn btn-success" @click="releaseProduct(product)">
                            Release Product
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- RELEASE PRODUCT MODAL -->
        <ModalComponent :id="modalId" :title="modalTitle" :size="modalSize" :position="modalPosition">
            <template #modalHeader>
                <div class="m-auto">
                    <h3>Release your Product</h3>
                </div>
            </template>
            <template #modalBody>
                <div class="row">
                    <div class="col-6 text-center m-auto" v-if="dataValues.image">
                        <img :src="'/images/' + dataValues.image" alt="Current Image" class="img-fluid" style="height:300px;">
                    </div>
                    <div class="col-6">
                        <p>
                            <b>{{ this.dataValues.brand_name }} {{ this.dataValues.tool_name }}</b> with the voltage of {{ this.dataValues.voltage }}, dimension of {{ this.dataValues.dimensions }}, weight of {{ this.dataValues.weight }} and powerSources of {{ this.dataValues.powerSources }}.
                        </p>
                        <p>
                            <b>Status:</b>
                            <select class="form-control" v-model="dataValues.status">
                                <option value="For Sale">For Sale</option>
                                <option value="For Borrowing">For Borrowing</option>
                            </select>  
                            <div class="text-danger" v-if="errors.status">{{ errors.status[0] }}</div>
                        </p>
                        <p v-if="dataValues.status === 'For Sale'"> 
                            Set Price for Selling:
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">₱</span>
                                </div>
                                <input type="number" class="form-control" v-model="selectedPrice" min="0">
                            </div>
                            <div class="text-danger" v-if="errors.selectedPrice">{{ errors.selectedPrice[0] }}</div>
                        </p>
                        <p v-if="dataValues.status === 'For Borrowing'"> 
                            Set Price for Borrowing:
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">₱</span>
                                </div>
                                <input type="number" class="form-control" v-model="selectedPrice" min="0">
                            </div>
                            <div class="text-danger" v-if="errors.selectedPrice">{{ errors.selectedPrice[0] }}</div>
                        </p>   
                        <div class="col-12">
                            <label for="serialNumber">Please select Serial Number(s) for Product:</label>
                            <div class="form-check" v-for="(serialNumber, index) in this.dataValues.serial_numbers" :key="index">
                                <input class="form-check-input" type="checkbox" :id="'serialNumber_' + index" :value="serialNumber" @change="updateCheckedValues($event.target.value)">
                                <label class="form-check-label" :for="'serialNumber_' + index">{{ serialNumber }}</label>
                            </div>
                            <div class="text-danger" v-if="errors.serial_numbers">{{ errors.serial_numbers[0] }}</div>
                        </div>
                    </div>
                </div>
            </template>
            <template #modalFooter>
                <button class="btn btn-primary" v-on:click="reviewProduct">Review</button>
            </template>
        </ModalComponent>

        <!-- FINAL RELEASE MODAL -->
        <ModalComponent :id="modalIdFinal" :title="modalTitle" :size="modalSize" :position="modalPosition">
            <template #modalHeader>
                <div class="m-auto">
                    <h3>Review your Product</h3>
                </div>
            </template>
            <template #modalBody>
                <div class="row">
                    <div class="col-6 text-center m-auto" v-if="dataValues.image">
                        <img :src="'/images/' + dataValues.image" alt="Current Image" class="img-fluid" style="height:300px;">
                    </div>
                    <div class="col-6">
                        <p>
                            <b>{{ this.dataValues.brand_name }} {{ this.dataValues.tool_name }}</b> with the voltage of {{ this.dataValues.voltage }}, dimension of {{ this.dataValues.dimensions }}, weight of {{ this.dataValues.weight }} and powerSources of {{ this.dataValues.powerSources }}.
                        </p>
                        <!-- <p>
                            This Product will be<div v-if="category_id === 2"> <b> For Sale </b> </div> <div v-if="category_id === 3"> <b>For Borrowing</b></div>
                        </p> -->
                        <p>
                            With the price of <b>₱{{ this.selectedPrice }} each.</b>
                        </p>
                        <p>
                            The Serial Number(s) selected are: <b>{{ this.checkedSerialNumbers.join(', ') }}</b> 
                        </p>
                    </div>
                </div>
            </template>
            <template #modalFooter>
                <button class="btn btn-success" v-on:click="sellProduct">Release Product</button>
            </template>
        </ModalComponent>


    </div>
</template>

<style>
    .borrowed-card {
        filter: brightness(80%); /* Set the background color for approved products */
    }
</style>

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
                categories : [],
                errors: '',
                searchData: '', 
                statuses: [],
                selectedBrand : '',
                selectedTool : '',
                suppliers: [],
                category_number: '',
                category_id: '',
                supplier_name: '',
                selectedPrice : 0,
                checkedSerialNumbers: [],
                dataValues: {
                    name: '',
                    category_id: '',
                    supplier_id: '',
                },
                modalId : 'modal-release-product',
                modalIdFinal : 'modal-final-release-product',
                modalTitle : 'Power Tools',
                modalSizeFinal : "modal-md",
                modalPosition: 'modal-dialog-centered',
                modalSize : 'modal-lg',
        }
    },
    components: {
        FormComponent,
        ModalComponent,
        BreadCrumbComponent,
    },
    // computed: {
    //     filteredCategories() {
    //         // Filter out the 'Unreleased' category if product's category is already 'Unreleased'
    //         return this.categories.filter(category => {
    //             return !(this.dataValues.category_name === 'Unreleased' && category.name === 'Unreleased');
    //         });
    //     }
    // },
    methods: {
        getData() {
            axios.get('/powertools/show').then(response => {
                this.data = response.data.data;
                this.categories = response.data.categories;
                this.suppliers = response.data.suppliers;
            })
        },
        refresh(){
            window.location.reload();
        },
        validateForm() {
            this.errors = [];

            if (this.checkedSerialNumbers.length === 0) {
                this.errors.serial_numbers = ['Please select at least one serial number'];
            }
 
            if (!this.selectedPrice) {
                this.errors.selectedPrice = ['Price is required'];
            }

            if (!this.dataValues.status) {
                this.errors.status = ['Status is required'];
            }

            // Check if any errors are present
            if (Object.keys(this.errors).length > 0) {
                return false; // Validation failed
            }

            return true; // Validation passed
        },
        updateCheckedValues(serialNumber) {
            if (this.checkedSerialNumbers.includes(serialNumber)) {
                this.checkedSerialNumbers = this.checkedSerialNumbers.filter(num => num !== serialNumber);
            } else {
                this.checkedSerialNumbers.push(serialNumber);
            }
        },
        filterData(){
            const searchData = {
                supplier_name: this.supplier_name,
                category_number: this.category_number,
                brand: this.selectedBrand,
                tool: this.selectedTool,
                specs: this.selectedSpecs
            };

            axios.post('/powertools/filterData', searchData)
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
        releaseProduct(props){
            this.checkedSerialNumbers = [];
            this.dataValues = props;
            if(this.dataValues.stocks === 0){
                Swal.fire({
                    title: "No Stocks Available!",
                    icon: 'warning',
                    timer: 3000
                });
                return;
            }
            $('#' + this.modalId).modal('show'); 
        },
        reviewProduct(){
            if (!this.validateForm()) {
                    return;
                }
            $('#' + this.modalId).modal('hide'); 
            $('#' + this.modalIdFinal).modal('show'); 
        },
        sellProduct()
        {   
            const data = {
                category_id: this.category_id,
                price: this.selectedPrice,
                serial_numbers: this.checkedSerialNumbers,
                dataValues: this.dataValues
            };

            Swal.fire({
                title: 'Are you sure you want to Release the Product?',
                text: 'This Product will be For Sale/For Borrowing!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, Release it!',
                cancelButtonText: 'Cancel',
            }).then((result) => {
                if (result.isConfirmed) {
                    axios.post('/powertools/releasedProduct', data).then(response => {
                        if(response.status === 200) {
                            Swal.fire({
                                title: "Success",
                                text: response.data.message,
                                icon: 'success',
                                timer: 3000
                            });
                        }
                        $('#' + this.modalIdFinal).modal('hide');
                        window.location.reload();
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
        },
        mounted() {
            this.getData();
            console.log(this.categories);
        }
        


}

</script>
