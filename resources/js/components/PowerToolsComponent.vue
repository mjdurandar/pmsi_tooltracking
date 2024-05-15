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
                <button class="btn btn-primary" @click="filterData">Search</button>
                <button class="btn btn-success ml-1" @click="refresh"><i class="fas fa-sync-alt"></i></button>
                <button class="btn btn-success ml-1" @click="exportToCSVAdmin"><i class="fa-solid fa-file-csv"></i></button>
            </div>
            <div class="col-lg-4 d-flex justify-content-end">
                <button class="btn btn-success p-3" @click="checkout">Release</button>
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
                        <button class="btn btn-warning ml-1" @click="maintenance(product)"><i class="fa-solid fa-wrench"></i></button>
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
                                <input type="number" class="form-control" v-model="dataValues.price" min="0">
                            </div>
                            <div class="text-danger" v-if="errors.price">{{ errors.price[0] }}</div>
                        </p>
                        <p v-if="dataValues.status === 'For Borrowing'"> 
                            Set Price for Borrowing:
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">₱</span>
                                </div>
                                <input type="number" class="form-control" v-model="dataValues.price" min="0">
                            </div>
                            <div class="text-danger" v-if="errors.price">{{ errors.price[0] }}</div>
                        </p>
                    </div>
                </div>
            </template>
            <template #modalFooter>
                <button class="btn btn-success mr-2" v-on:click="nextModal">Select Serial Number(s)</button>
            </template>
        </ModalComponent>

        <!-- SELECT SRN AND PRN CODE -->
        <ModalComponent :id="modalIdSelect" :title="modalTitle" :size="modalSizeRelease" :position="modalPosition">
            <template #modalHeader>
                <div class="m-auto">
                    <h4>Select Serial Number</h4>
                </div>
            </template>
            <template #modalBody>
                <div class="modal-body" style="max-height: 400px; overflow-y: auto;">
                    <div class="row" v-for="(key, index) in Object.keys(serialNumbers)" :key="index">
                        <div class="col-lg-12 d-flex justify-content-around mt-2">
                            <div class="m-auto">
                                <input type="checkbox" :checked="selectedIndexes.includes(Number(key))" v-model="selectedIndexes" :value="Number(key)">
                            </div>
                            <div class="m-auto">
                                <div>Serial Number {{ index + 1 }}</div>
                            </div>
                            <div>
                                <input type="text" :value="serialNumbers[key]" class="form-control" disabled>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
            <template #modalFooter>
                <div class="text-right">
                    <button class="btn btn-primary mr-2" v-on:click="selectAll">Select All</button>
                    <button class="btn btn-primary mr-2" v-on:click="clearSelected">Clear</button>
                    <button class="btn btn-success" v-on:click="addToReleaseCart">Add to Release</button>
                </div>
            </template>
        </ModalComponent>

        <!-- RELEASE MULTIPLE PRODUCTS MODAL -->
        <ModalComponent :id="modalIdReview" :title="modalTitle" :size="modalSizeReleaseFinal" :position="modalPosition">
            <template #modalHeader>
                <div class="m-auto">
                    <h4>Release your Product</h4>
                </div>
            </template>
            <template #modalBody>
                <div class="modal-body" style="max-height: 400px; overflow-y: auto;">
                    <div v-for="(product, index) in selectedProducts" :key="index">
                        <div class="row">
                            <div class="col-6 text-center m-auto" v-if="product.dataValues.image">
                                <img :src="'/images/' + product.dataValues.image" alt="Product Image" class="img-fluid" style="height:300px;">
                            </div>
                            <div class="col-6">
                                <p>
                                    You are about to release this product <b>{{ product.dataValues.brand_name }} {{ product.dataValues.tool_name }}</b>
                                    with the voltage of <b>{{ product.dataValues.voltage }}</b>, dimension of <b>{{ product.dataValues.dimensions }}</b>, weight of <b>{{ product.dataValues.weight }}</b> and powerSources of <b>{{ product.dataValues.powerSources }}</b>.
                                </p>
                                <p>
                                    You selected the Serial Number(s) of:
                                    <ul>
                                        <li v-for="(serialNumber, index) in product.selectedSerialNumbers" :key="index">
                                            <b>{{ serialNumber }}{{ index !== product.selectedSerialNumbers.length - 1 ? '' : '' }}</b>
                                        </li>
                                    </ul>
                                </p>
                                <p>
                                    Status: <b>{{ product.dataValues.status }}</b>
                                </p>
                                <p>
                                    Once you're confident that everything is in order, proceed by clicking <b>"Release"</b> button below.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
            <template #modalFooter>
                <div class="text-right">
                    <button class="btn btn-success" v-on:click="releasedProductFinal">Release</button>
                </div>
            </template>
        </ModalComponent>


        <!-- SELECT SRN AND PRN CODE FOR MAINTENANCE -->
        <ModalComponent :id="modalIdSelectMaintenance" :title="modalTitle" :size="modalSizeRelease" :position="modalPosition">
            <template #modalHeader>
                <div class="m-auto">
                    <h4>Select Serial Number</h4>
                </div>
            </template>
            <template #modalBody>
                <div class="modal-body" style="max-height: 400px; overflow-y: auto;">
                    <div class="row" v-for="(key, index) in Object.keys(serialNumbers)" :key="index">
                        <div class="col-lg-12 d-flex justify-content-around mt-2">
                            <div class="m-auto">
                                <input type="checkbox" :checked="selectedIndexes.includes(Number(key))" v-model="selectedIndexes" :value="Number(key)">
                            </div>
                            <div class="m-auto">
                                <div>Serial Number {{ index + 1 }}</div>
                            </div>
                            <div>
                                <input type="text" :value="serialNumbers[key]" class="form-control" disabled>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
            <template #modalFooter>
                <div class="text-right">
                    <button class="btn btn-primary mr-2" v-on:click="forMaintenance">Proceed</button>
                </div>
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
                errors: '',
                searchData: '', 
                statuses: [],
                selectedBrand : '',
                selectedTool : '',
                selectedProducts: [],
                suppliers: [],
                category_number: '',
                supplier_name: '',
                serialNumbers: [],
                price : 0,
                selectedIndexes: [],    
                checkedSerialNumbers: [],
                dataValues: {
                    name: '',
                    category_id: '',
                    supplier_id: '',
                },
                modalId : 'modal-release-product',
                modalIdFinal : 'modal-final-release-product',
                modalIdSelect : 'modal-select-serial-number',
                modalIdReview : 'modal-review-product',
                modalIdSelectMaintenance : 'modal-select-serial-number-maintenance',
                modalTitle : 'Power Tools',
                modalPosition: 'modal-dialog-centered',
                modalSize : 'modal-lg',
                modalSizeRelease : 'modal-md',
                modalSizeReleaseFinal : 'modal-lg',
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
                this.suppliers = response.data.suppliers;
            })
        },
        selectAll() {
            // Get the keys of the serialNumbers object and convert them to an array of indexes
            const keys = Object.keys(this.serialNumbers);
            this.selectedIndexes = keys.map(Number); // Convert keys to numbers to ensure proper indexing
        },
        nextModal(){
            if (!this.validateForm()) {
                return;
            }
            this.serialNumbers = { ...this.dataValues.serial_numbers };
            $('#' + this.modalId).modal('hide'); 
            $('#' + this.modalIdSelect).modal('show'); 
        },
        maintenance(product){
            if(product.stocks === 0)
            {
                Swal.fire({
                    title: "No Serial Numbers available!",
                    icon: 'warning',
                    timer: 3000
                });
                return;
            }
            this.dataValues = product;
            this.serialNumbers = { ...product.serial_numbers };
            $('#' + this.modalIdSelectMaintenance).modal('show'); 
        },
        forMaintenance()
        {   
             // Create an array to store selected serial numbers for maintenance
            const selectedForMaintenance = [];
            // Iterate over the selected indexes
            this.selectedIndexes.forEach(index => {
                // Push the serial number corresponding to the index to the selectedForMaintenance array
                selectedForMaintenance.push(this.serialNumbers[index]);
                // Remove the serial number from the release list
                delete this.serialNumbers[index];
            });

            axios.post('/powertools/forMaintenance', {
                serialNumbers: selectedForMaintenance,
                dataValues: this.dataValues
            })
                .then(response => {
                    Swal.fire({
                        title: "Product Up for Maintenance!",
                        icon: 'success',
                        timer: 3000
                    });
                    this.getData();
                    $('#' + this.modalIdSelectMaintenance).modal('hide');
                })
                .catch(errors => {
                    Swal.fire({
                            title: 'Something went wrong!',
                            text: errors.response.data.error,
                            icon: 'error',
                            timer: 3000
                        });
                });
        },
        async exportToCSVAdmin() {
            try {
                // Make HTTP request to trigger CSV export
                window.open('/export-csv-admin/', '_blank');
            } catch (error) {
                console.error('Error exporting CSV:', error);
                // Handle error
            }
        },
        refresh(){
            window.location.reload();
        },
        clearSelected()
        {
            this.selectedIndexes.length = 0;
        },
        checkout()
        {
            if(this.selectedProducts.length === 0){
                Swal.fire({
                    title: "No Products selected!",
                    icon: 'warning',
                    timer: 3000
                });
                return;
            }
            $('#' + this.modalIdReview).modal('show');
        },
        validateForm() {
            this.errors = [];

            // if (this.checkedSerialNumbers.length === 0) {
            //     this.errors.serial_numbers = ['Please select at least one serial number'];
            // }
 
            if (!this.dataValues.price) {
                this.errors.price = ['Price is required'];
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
            });
        },
        releaseProduct(props){
            this.checkedSerialNumbers = [];
            this.dataValues = props;
            this.dataValues.status = '';
            this.selectedIndexes.length = 0;
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
        addToReleaseCart() {
            if (this.selectedIndexes.length === 0) {
                Swal.fire({
                    title: "No Serial Number selected!",
                    icon: 'warning',
                    timer: 3000
                });
                return;
            }

            const selectedSerials = this.selectedIndexes.map(index => this.serialNumbers[index]);
            
            // Check if any of the selected serial numbers are already in the list of selected products for the same product_id
            const existingProduct = this.selectedProducts.find(product => product.dataValues.id === this.dataValues.id);
            if (existingProduct) {
                if (existingProduct.dataValues.status !== this.dataValues.status) {
                    this.selectedProducts.push({
                        dataValues: this.dataValues,
                        status: this.dataValues.status,
                        selectedSerialNumbers: selectedSerials,
                    });
                    Swal.fire({
                        title: 'Added to Release Product!',
                        text: '',
                        icon: 'success',
                        timer: 3000
                    });
                } else {
                    // Add only the unique serial numbers if the status is the same
                    const newSerialNumbers = selectedSerials.filter(serial => !existingProduct.selectedSerialNumbers.includes(serial));
                    if (newSerialNumbers.length > 0) {
                        existingProduct.selectedSerialNumbers.push(...newSerialNumbers);
                        Swal.fire({
                            title: 'Serial Number Added to Existing Product!',
                            text: '',
                            icon: 'success',
                            timer: 3000
                        });
                    } else {
                        Swal.fire({
                            title: 'Serial Number Already Selected for This Product!',
                            text: 'Please select a different one!',
                            icon: 'warning',
                            timer: 3000
                        });
                        return;
                    }
                }
            } else {
                // Add new data entry with selected serial numbers
                this.selectedProducts.push({
                    dataValues: this.dataValues,
                    selectedSerialNumbers: selectedSerials,
                });
                Swal.fire({
                    title: 'Added to Release Product!',
                    text: '',
                    icon: 'success',
                    timer: 3000
                });
            }

            $('#' + this.modalIdSelect).modal('hide');
        },
        releasedProductFinal()
        {
            axios.post('/powertools/releasedProduct', this.selectedProducts)
                .then(response => {
                    Swal.fire({
                        title: "Product Release Successfully!",
                        icon: 'success',
                        timer: 3000
                    });
                    this.getData();
                    $('#' + this.modalIdReview).modal('hide');
                    this.selectedProducts = [];
                })
                .catch(errors => {
                    Swal.fire({
                            title: 'Something went wrong!',
                            text: errors.response.data.error,
                            icon: 'error',
                            timer: 3000
                        });
                });
        }
        },
        mounted() {
            this.getData();
        }
        


}

</script>
