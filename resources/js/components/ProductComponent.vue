<style>
    .is_for_sale {
        filter: brightness(80%);
    }
</style>

<template>
    <div class="p-3">
        <BreadCrumbComponent tab_title="Products"></BreadCrumbComponent>
        
        <!-- FILTER PRODUCT -->
        <div class="d-flex justify-content-between">
            <div>
                <div class="row mb-3">
                    <div class="col-lg-3">
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
                    <div class="col-lg-2">
                        <select v-model="selectedTool" class="form-control">
                            <option value="" disabled selected>Tools</option> 
                            <option value="Drill">Drill</option>
                            <option value="Screwdriver">Screwdriver</option>
                            <option value="Wrench">Wrench</option>
                            <option value="Grinder">Grinder</option>
                            <option value="Saw">Saw</option>
                        </select>
                    </div>
                    <div class="col-lg-3">
                        <select class="form-control" v-model="selectedStatus">
                            <option value="" disabled selected>Status</option>
                            <option value="1">For Sale</option>
                            <option value="0">Unreleased</option>
                        </select> 
                    </div>
                    <div>
                        <button class="btn btn-primary" @click="filterData">Search</button>
                        <button class="btn btn-success ml-1" @click="refresh"><i class="fas fa-sync-alt"></i></button>
                        <button class="btn btn-success ml-1" @click="exportToCSV"><i class="fa-solid fa-file-csv"></i></button>
                    </div>
                </div>
            </div>
            <div>
                <FormComponent 
                    :data="data"
                    :columns="columns"
                    :options="options"
                    btnName="Add Product"
                    otherBtnName="Canceled Products"
                    buttonOptionColor="btn btn-danger"
                    :option3Switch=true
                    :tableSwitch=false
                    :otherButton=true
                    option3Name="View"
                    option3Icon="fa fa-eye"
                    @deleteClicked="isRemoved"
                    @editClicked="editClicked"
                    @addClicked="addClicked"
                    @optionalClicked="viewClicked"
                    @otherClicked="canceledProduct"
                    >
                </FormComponent>
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
                                <p class="card-text" :class="{ 'text-danger': product.stocks < 5 }">Stocks: {{ product.stocks }}</p>
                                <p class="card-text">Price: ₱{{ product.price }}</p>
                                <div style="color: green; font-weight: 500;" v-if="product.is_for_sale">
                                    <p>Product is For Sale</p>
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
                        <div class="d-flex justify-content-between mt-3" v-if="!product.is_for_sale">
                            <div>               
                                <button class="btn btn-danger mr-1" @click="isRemoved(product)">Remove</button>
                                <button class="btn btn-primary mr-1" @click="editClicked(product)">Edit</button>
                            </div>
                            <div>
                                <button class="btn btn-success" @click="releaseProduct(product)">Release</button>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between mt-3" v-if="product.is_for_sale">
                            <div>
                                <button class="btn btn-success" @click="viewClicked(product)">View</button>
                                <button class="btn btn-success ml-2" @click="reStocked(product)">Restock</button>
                            </div>
                            <div>
                                <button class="btn btn-danger" @click="cancelProduct(product)">Cancel</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- RESTOCK MODAL -->
        <ModalComponent :id="modalIdRestock" :title="modalTitle" :size="modalSizeRestock" :position="modalPosition">
            <template #modalHeader>
                <div class="m-auto">
                    <h4>Restock Product</h4>
                </div>
            </template>
            <template #modalBody>
                <div class="row">
                    <div class="col-12">
                        <label for="">How many Products?</label>
                        <input type="number" class="form-control" v-model="dataValues.reStocked" min="0">
                        <div class="text-danger" v-if="errors.reStocked">{{ errors.reStocked[0] }}</div>
                    </div>  
                </div>
            </template>
            <template #modalFooter>
                <div class="text-right">
                    <button class="btn btn-success" v-on:click="addProduct">Confirm</button>
                </div>
            </template>
        </ModalComponent>

        <!-- ADD/EDIT PRODUCT MODAL -->
        <ModalComponent :id="modalId" :title="modalTitle" :size="modalSize" :position="modalPosition">
            <template #modalHeader>
                <div class="m-auto">
                    <h4>Product</h4>
                </div>
            </template>
            <template #modalBody>
                <div class="row">
                    <div class="col-6">
                        <label for="">Brand</label>
                        <select v-model="dataValues.brand" class="form-control" :disabled="viewMode">
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
                            <option value="Ryobi">Ryobi</option>
                            <!-- <option value="Other">Other</option> -->
                        </select>
                        <!-- <input type="text" v-if="dataValues.brand === 'Other'" v-model="dataValues.other_brand" class="form-control mt-2" placeholder="Enter other brand"> -->
                        <div class="text-danger" v-if="errors.brand">{{ errors.brand[0] }}</div>
                    </div>  
                    <div class="col-lg-6">
                        <label for="">Tools</label>
                        <select v-model="dataValues.tool" class="form-control" :disabled="viewMode">
                            <option value="Drill">Drill</option>
                            <option value="Screwdriver">Screwdriver</option>
                            <option value="Wrench">Wrench</option>
                            <option value="Grinder">Grinder</option>
                            <option value="Saw">Saw</option>
                            <!-- <option value="Other">Other</option> -->
                        </select>
                        <input type="text" v-if="dataValues.tool === 'Other'" v-model="dataValues.other_tool" class="form-control mt-2" placeholder="Enter other tool">
                        <!-- <div class="text-danger" v-if="errors.tool">{{ errors.tool[0] }}</div> -->
                    </div>
                    <div class="col-12">
                        <label for="image" v-if="!viewMode">Image</label>
                        <input type="file" id="image" class="form-control" @change="onFileChange" v-if="!viewMode">
                        <!-- <div class="text-danger" v-if="errors.image">{{ errors.image[0] }}</div> -->
                    </div>
                    <div class="col-12" v-if="dataValues.image">
                        <label for="currentImage">Current Image</label>
                        <img :src="'/images/' + dataValues.image" alt="Current Image" class="img-fluid">
                    </div>
                    <div class="col-lg-12 mt-3 mb-3">
                        <label for="">Power Source</label>
                        <div class="d-flex justify-content-around">
                            <div class="form-check">
                                <input type="radio" class="form-check-input" id="cordedRadio" value="Corded" v-model="dataValues.powerSources" :disabled="viewMode">
                                <label class="form-check-label" for="cordedRadio">Corded</label>
                            </div>
                            <div class="form-check">
                                <input type="radio" class="form-check-input" id="batteryRadio" value="Battery" v-model="dataValues.powerSources" :disabled="viewMode">
                                <label class="form-check-label" for="batteryRadio">Battery</label>
                            </div>
                        </div>
                        <div class="text-danger" v-if="errors.powerSources">{{ errors.powerSources[0] }}</div>
                    </div>
                    <div class="col-lg-6">
                        <label for="">Voltage</label>
                        <select v-model="dataValues.voltage" class="form-control" :disabled="viewMode">
                            <option value="12 Volts">12 Volts</option>
                            <option value="18 Volts">18 Volts</option>
                            <option value="20 Volts">20 Volts</option>
                            <option value="110 Volts">110 Volts</option>
                            <option value="120 Volts">120 Volts</option>
                            <option value="210 Volts">210 Volts</option>
                            <!-- <option value="Other">Other</option> -->
                        </select>
                        <!-- <input type="text" v-if="dataValues.voltage === 'Other'" v-model="dataValues.other_voltage" class="form-control mt-2" placeholder="Enter Voltage"> -->
                        <div class="text-danger" v-if="errors.voltage">{{ errors.voltage[0] }}</div>
                    </div>
                    <div class="col-lg-6">
                        <label for="">Weight</label>
                        <select v-model="dataValues.weight" class="form-control" :disabled="viewMode">
                            <option value="2kg">2kg</option>
                            <option value="4kg">4kg</option>
                            <option value="6kg">6kg</option>
                            <option value="8kg">8kg</option>
                            <option value="10kg">10kg</option>
                            <option value="12kg">12kg</option>
                            <!-- <option value="Other">Other</option> -->
                        </select>
                        <!-- <input type="text" v-if="dataValues.weight === 'Other'" v-model="dataValues.other_weight" class="form-control mt-2" placeholder="Enter Weight"> -->
                        <div class="text-danger" v-if="errors.weight">{{ errors.weight[0] }}</div>
                    </div>
                    <div class="col-lg-6">
                        <label for="">Dimensions</label>
                        <select v-model="dataValues.dimensions" class="form-control" :disabled="viewMode">
                            <option value="12 x 12 x 8">12 x 12 x 8</option>
                            <option value="20 x 25 x 18">20 x 25 x 18</option>
                            <option value="30 x 40 x 36">30 x 40 x 36</option>
                            <option value="15 x 20 x 12">15 x 20 x 12</option>
                            <option value="24 x 70 x 20">24 x 70 x 20</option>
                            <!-- <option value="Other">Other</option> -->
                        </select>
                        <!-- <input type="text" v-if="dataValues.dimensions === 'Other'" v-model="dataValues.other_dimensions" class="form-control mt-2" placeholder="Enter Dimensions"> -->
                        <div class="text-danger" v-if="errors.dimensions">{{ errors.dimensions[0] }}</div>
                    </div>
                    <div class="col-lg-6">
                        <label for="">Material</label>
                        <select v-model="dataValues.material" class="form-control" :disabled="viewMode">
                            <option value="Steel">Steel</option>
                            <option value="Aluminum">Aluminum</option>
                            <option value="Wood">Wood</option>
                            <option value="Plastic">Plastic</option>
                            <option value="Rubber">Rubber</option>
                            <option value="Carbon Fiber">Carbon Fiber</option>
                            <option value="Ceramic">Ceramic</option>
                            <option value="Composite Materials">Composite Materials</option>
                            <!-- <option value="Other">Other</option> -->
                        </select>
                        <input type="text" v-if="dataValues.material === 'Other'" v-model="dataValues.other_material" class="form-control mt-2" placeholder="Enter Material">
                        <!-- <div class="text-danger" v-if="errors.dimensions">{{ errors.dimensions[0] }}</div> -->
                    </div>
                    <div class="col-12 mt-2">
                        <label for="">Stocks</label>
                        <input type="number" class="form-control" v-model="dataValues.stocks" :disabled="viewMode" min="0">
                        <div class="text-danger" v-if="errors.stocks">{{ errors.stocks[0] }}</div>
                    </div>
                    <div class="col-12">
                        <label for="">Price per pc</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">₱</span>
                            </div>
                            <input type="number" class="form-control" v-model="dataValues.price" :disabled="viewMode" min="0">
                        </div>
                        <div class="text-danger" v-if="errors.price">{{ errors.price[0] }}</div>
                    </div>  
                </div>
            </template>
            <template #modalFooter>
                <div class="text-right">
                    <!-- Button for adding product -->
                    <button v-if="!viewMode" class="btn btn-success" v-on:click="addProduct()">Add</button>

                    <!-- Button for closing view -->
                    <button v-if="viewMode" class="btn btn-danger" v-on:click="closeView()">Close</button>
                </div>
            </template>
        </ModalComponent>

        <!-- REVIEW PRODUCT BEFORE RELEASE -->
        <ModalComponent :id="modalIdReview" :title="modalTitle" :size="modalSizeView" :position="modalPosition">
            <template #modalHeader>
                <div class="m-auto">
                    <h4>Review your Product before Releasing It</h4>
                </div>
            </template>
            <template #modalBody>
                <div class="row">
                    <div class="col-12 text-center" v-if="dataValues.image">
                        <img :src="'/images/' + dataValues.image" alt="Current Image" class="img-fluid" style="height:300px;">
                    </div>
                    <div class="col-6">
                        <label for="">Brand</label>
                        <input type="text" v-model="dataValues.brand" class="form-control mt-2" placeholder="Brand" disabled>
                        <input type="text" v-if="dataValues.brand === 'Other'" v-model="dataValues.other_brand" class="form-control mt-2">
                    </div>  
                    <div class="col-lg-6">
                        <label for="">Tools</label>
                        <input type="text" v-model="dataValues.tool" class="form-control mt-2" placeholder="Tool" disabled>
                        <input type="text" v-if="dataValues.tool === 'Other'" v-model="dataValues.other_tool" class="form-control mt-2">
                    </div>
                    <div class="col-lg-12 mt-3 mb-3">
                        <label for="">Power Source</label>
                        <div class="d-flex justify-content-around">
                            <div class="form-check">
                                <input type="radio" class="form-check-input" id="cordedRadio" value="Corded" v-model="dataValues.powerSources" disabled>
                                <label class="form-check-label" for="cordedRadio">Corded</label>
                            </div>
                            <div class="form-check">
                                <input type="radio" class="form-check-input" id="batteryRadio" value="Battery" v-model="dataValues.powerSources" disabled>
                                <label class="form-check-label" for="batteryRadio">Battery</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <label for="">Voltage</label>
                        <input type="text" v-model="dataValues.voltage" class="form-control mt-2" placeholder="Voltage" disabled>
                        <input type="text" v-if="dataValues.voltage === 'Other'" v-model="dataValues.other_voltage" class="form-control mt-2">
                    </div>
                    <div class="col-lg-6">
                        <label for="">Weight</label>
                        <input type="text" v-model="dataValues.weight" class="form-control mt-2" placeholder="Weight" disabled>
                        <input type="text" v-if="dataValues.weight === 'Other'" v-model="dataValues.other_weight" class="form-control mt-2">
                    </div>
                    <div class="col-lg-6">
                        <label for="">Dimensions</label>
                        <input type="text" v-model="dataValues.dimensions" class="form-control mt-2" placeholder="Dimensions" disabled>
                        <input type="text" v-if="dataValues.dimensions === 'Other'" v-model="dataValues.other_dimensions" class="form-control mt-2">
                    </div>
                    <div class="col-lg-6">
                        <label for="">Material</label>
                        <input type="text" v-model="dataValues.material" class="form-control mt-2" placeholder="Material" disabled>
                        <input type="text" v-if="dataValues.material === 'Other'" v-model="dataValues.other_material" class="form-control mt-2">
                    </div>
                    <div class="col-12 mt-2">
                        <label for="">Stocks</label>
                        <input type="number" class="form-control" v-model="dataValues.stocks" disabled>
                    </div>
                    <div class="col-12">
                        <label for="">Price per pc</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">₱</span>
                            </div>
                            <input type="text" class="form-control" v-model="dataValues.price" disabled>
                        </div>
                    </div>  
                </div>
            </template>
            <template #modalFooter>
                <div class="text-right">
                    <button class="btn btn-success" v-on:click="viewReleaseProduct">Release</button>
                </div>
            </template>
        </ModalComponent>

         <!-- RELEASE PRODUCT -->
         <ModalComponent :id="modalIdRelease" :title="modalTitle" :size="modalSizeView" :position="modalPosition">
            <template #modalHeader>
                <div class="m-auto">
                    <h4>Release your Product</h4>
                </div>
            </template>
            <template #modalBody>
                <div class="row">
                    <div class="col-6 text-center m-auto" v-if="dataValues.image">
                        <img :src="'/images/' + dataValues.image" alt="Current Image" class="img-fluid" style="height:300px;">
                    </div>
                    <div class="col-6">
                        <p>
                            Before releasing your product for sale, make sure you have thoroughly reviewed and finalized all product details, including brand, tool type, power source, voltage, weight, dimensions, material, stocks, and price per piece.
                        </p>
                        <p>
                            Double-check your product images to ensure they accurately represent your product and meet your quality standards.
                        </p>
                        <p>
                            Consider providing additional information or specifications that may be helpful for potential buyers.
                        </p>
                        <p>
                            Once you're confident that everything is in order, proceed to release your product for sale by clicking the "Release" button below.
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

        <!-- INPUT SERIAL NUMBER -->
        <ModalComponent :id="modalIdCode" :title="modalTitle" :size="modalSizeCode" :position="modalPosition">
            <template #modalHeader>
                <div class="m-auto">
                    <h4>Serial Number</h4>
                </div>
            </template>
            <template #modalBody>
                <!-- Use v-for to loop through the specified number of input fields -->
                <div class="modal-body" style="max-height: 400px; overflow-y: auto;">
                    <!-- Use v-for to loop through the specified number of input fields -->
                    <div class="row" v-for="(index, key) in numberOfFields" :key="index">
                        <div class="col-lg-12">
                            <label for="">Serial Number {{ index }}</label>
                            <!-- Use v-model with dynamic dataValues key -->
                            <input type="text" v-model="dataValues[`serial_numbers${index}`]" class="form-control" :placeholder="'SRN-' + ('0000' + (index)).slice(-4)">
                        </div>  
                    </div>
                </div>
            </template>
            <template #modalFooter>
                <div class="text-right">
                    <button class="btn btn-success" v-on:click="storeData">Save</button>
                </div>
            </template>
        </ModalComponent>

        <!-- CANCEL PRODUCT MODAL -->
        <ModalComponent :id="modalIdCancel" :title="modalTitle" :size="modalSizeView" :position="modalPosition">
            <template #modalHeader>
                <div class="m-auto">
                    <h4>Why are we canceling this Product?</h4>
                </div>
            </template>
            <template #modalBody>
                <div class="row">
                    <div class="col-12">
                        <label for="">Explain: </label>
                        <textarea type="text" v-model="dataValues.description" class="form-control mt-2" placeholder="Why are we canceling this Product?"></textarea>
                        <div class="text-danger" v-if="errors.description">{{ errors.description[0] }}</div>
                    </div> 
                </div>
            </template>
            <template #modalFooter>
                <div class="text-right">
                    <button class="btn btn-success" v-on:click="cancelProductExplanation">Submit</button>
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
                powerSources : '',
                imageData: null,
                brand: '',
                numberOfFields: 0,
                viewMode: false,
                canceledId: '',
                selectedBrand : '',
                selectedTool : '',
                selectedStatus : '',
                serialNumberErrors: [],
                productCodeErrors: [],
                columns : ['brand', 'tool', 'powerSources', 'voltage', 'weight', 'dimensions', 'material', 'stocks', 'price' ,'action'],
                errors: [],
                options : {
                    headings : {
                        brand: 'Brand',
                        tool: 'Tool',
                        powerSources: 'Power Source',
                        voltage: 'Voltage',
                        weight: 'Weight',
                        dimensions: 'Dimensions',
                        material: 'Material',
                        stocks: 'Stocks',
                        price: 'Price',
                        action: 'Action'
                    },
                    filterable: false,
                    sortable: []
                },
                dataValues: {
                    name: '',
                    id: '',
                    voltage: '',
                    weight: '',
                    dimensions: '',
                },
                modalId : 'modal-product',
                modalIdReview : 'modal-product-review',
                modalIdCode : 'modal-product-code',
                modalIdRelease : 'modal-product-release',
                modalIdCancel : 'modal-product-cancel',
                modalIdRestock : 'modal-product-restock',
                modalSizeRestock : 'modal-sm',
                modalSizeCode : 'modal-md',
                modalSizeView : 'modal-lg',
                modalTitle : 'Add a Product',
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
        refresh(){
            window.location.reload();
        },
        reStocked(props){
            this.dataValues = props;
            console.log(this.dataValues);
            this.modalTitle = 'Restock Product';
            $('#' + this.modalIdRestock).modal('show');
        },
        onFileChange(event) {
            const file = event.target.files[0];
            if (file) {
                this.imageData = file;
            }
        },
        clearInputs() {
            this.dataValues = {
                name: '',
            }
            this.imageData = null;
            this.errors = [];
        },
        addClicked(){
            console.log(this.imageData);
            this.viewMode = false;
            this.clearInputs();
            $('#' + this.modalId).modal('show');
        },
        canceledProduct(){
            window.location.href = '/canceled-product';
        },
        cancelProduct(props){
            this.clearInputs(); 
            this.canceledId = props.id;
            $('#' + this.modalIdCancel).modal('show');
        },
        filterData(){
            const searchData = {
                brand: this.selectedBrand,
                tool: this.selectedTool,
                status: this.selectedStatus
            };

            axios.post('/products/filterData', searchData)
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
        validateFormCancel() {
            this.errors = [];

            if (!this.dataValues.description) {
                this.errors.description = ['Description is required'];
            }

            // Check if any errors are present
            if (Object.keys(this.errors).length > 0) {
                return false; // Validation failed
            }

            return true; // Validation passed
        },
        cancelProductExplanation(){
            if (this.validateFormCancel()) {
                axios.post('/products/canceledProduct',  { id: this.canceledId, dataValues: this.dataValues }).then(response => {
                    if(response.status === 200) {
                        Swal.fire({
                            title: "Success",
                            text: response.data.message,
                            icon: 'success',
                            timer: 3000
                        });
                    }
                    this.getData();
                    $('#' + this.modalIdCancel).modal('hide');
                })
                .catch(errors => {
                        if (errors.response.status === 422) {
                    Swal.fire({
                        title: "Warning",
                        text: errors.response.data.errors,
                        icon: 'warning',
                        timer: 3000
                    });
                    } else {
                        // Handle other errors here
                        console.error(error);
                    }
                });
            }
        },
        getData() {
            axios.get('/products/show').then(response => {
                this.data = response.data.data;
            })
        },
        clearInputs() {
            this.dataValues = {
                name: '',
            }
            this.imageData = '';
            this.errors = [];
        },
        viewClicked(props){
            this.dataValues = props;
            this.viewMode = true;
            $('#' + this.modalId).modal('show');
        },
        closeView() {
            $('#' + this.modalIdView).modal('hide');
            $('#' + this.modalId).modal('hide');
        },
        validateForm() {
            this.errors = [];

            if (!this.dataValues.brand) {
                this.errors.brand = ['Brand is required'];
            }

            if (!this.dataValues.tool) {
                this.errors.tool = ['Tool is required'];
            }

            // if (!this.imageData) {
            //     this.errors.image = ['Image is required'];
            // }

            if (!this.dataValues.powerSources) {
                this.errors.powerSources = ['Power Source is required'];
            }

            if (!this.dataValues.voltage) {
                this.errors.voltage = ['Voltage is required'];
            }

            if (!this.dataValues.weight) {
                this.errors.weight = ['Weight is required'];
            }

            if (!this.dataValues.dimensions) {
                this.errors.dimensions = ['Dimensions is required'];
            }

            if (!this.dataValues.material) {
                this.errors.material = ['Material is required'];
            }

            if (!this.dataValues.stocks) {
                this.errors.stocks = ['Stocks is required'];
            }

            if (!this.dataValues.price) {
                this.errors.price = ['Price is required'];
            }

            // Check if any errors are present
            if (Object.keys(this.errors).length > 0) {
                return false; // Validation failed
            }

            return true; // Validation passed
        },
        releasedProduct()
        {
            axios.post('/products/releasedProduct', this.dataValues).then(response => {
                if(response.status === 200) {
                    Swal.fire({
                        title: "Your Product is now for Sale!",
                        text: response.data.message,
                        icon: 'success',
                        timer: 3000
                    });
                }
                this.getData();
                $('#' + this.modalIdRelease).modal('hide');
            })
            .catch(errors => {
                    this.errors = errors.response.data.errors;
            });
        },
        editClicked(props) {
            this.modalTitle = 'Edit Data';
            this.viewMode = false;
            axios.get('/products/edit/' + props.id).then(response => {
                this.dataValues = props;

                for (let i = 0; i < response.data.serial_numbers.length; i++) {
                    this.dataValues[`serial_numbers${i + 1}`] = response.data.serial_numbers[i];
                }
                
                $('#' + this.modalId).modal('show');
            }).catch(errors => {
                // Handle errors
                if (errors.response.data.message.length > 0) {
                    Swal.fire({
                        title: "Failed",
                        text: errors.response.data.message,
                        icon: 'error',
                        timer: 3000
                    });
                    this.errors = errors.response.data.errors;
                }
            });
        },
        isRemoved(props) {
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
                    axios.post('/products/isRemoved/', props).then(response => {
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
        viewReleaseProduct() {
            $('#' + this.modalIdReview).modal('hide');
            $('#' + this.modalIdRelease).modal('show');
        },
        releaseProduct(props) {
            this.dataValues = props;
            $('#' + this.modalIdReview).modal('show');
        },
        addProduct() {
            if (this.validateForm()) {
                if(this.dataValues.reStocked)
                {   
                    this.numberOfFields = parseInt(this.dataValues.reStocked);
                    $('#' + this.modalIdCode).modal('show');
                    $('#' + this.modalIdRestock).modal('hide');
                }
                else{
                    // Proceed with adding the product
                    this.numberOfFields = parseInt(this.dataValues.stocks);
                    $('#' + this.modalIdCode).modal('show');
                    $('#' + this.modalId).modal('hide');
                }
            }
        },
        reports(){
            window.location.href = '/reports';
        },
        async exportToCSV() {
            try {
                // Make HTTP request to trigger CSV export
                window.open('/export-csv/', '_blank');
            } catch (error) {
                console.error('Error exporting CSV:', error);
                // Handle error
            }
        },
        storeData(){
            const formData = new FormData();
            // Check if id is present and not null before appending it to the form data
            if (this.dataValues.id !== null && this.dataValues.id !== undefined) {
                formData.append('id', this.dataValues.id);
            }
            formData.append('brand', this.dataValues.brand);
            formData.append('tool', this.dataValues.tool);
            formData.append('other_brand', this.dataValues.other_brand);
            formData.append('other_tool', this.dataValues.other_tool);
            formData.append('other_voltage', this.dataValues.other_voltage);
            formData.append('other_weight', this.dataValues.other_weight);
            formData.append('other_dimensions', this.dataValues.other_dimensions);
            formData.append('other_material', this.dataValues.other_material);
            formData.append('image', this.dataValues.image);
            formData.append('powerSources', this.dataValues.powerSources);
            formData.append('voltage', this.dataValues.voltage);
            formData.append('weight', this.dataValues.weight);
            formData.append('dimensions', this.dataValues.dimensions);
            formData.append('material', this.dataValues.material);
            formData.append('stocks', this.dataValues.stocks);
            formData.append('price', this.dataValues.price);
            formData.append('reStocked', this.dataValues.reStocked);

            const serialNumbers = [];
            for (let index = 1; index <= this.numberOfFields; index++) {    
                serialNumbers.push(this.dataValues[`serial_numbers${index}`]);
            }
            formData.append('serial_numbers', JSON.stringify(serialNumbers));

            if (this.imageData) {
                formData.append('image', this.imageData);
            }
            axios.post('/products/store', formData, {
                headers: {
                    'Content-Type': 'multipart/form-data',
                },
            }).then(response => {
                if (response.status === 200) {
                    Swal.fire({
                        title: 'Success',
                        text: response.data.message,
                        icon: 'success',
                        timer: 3000
                    });
                }
                this.getData();
                $('#' + this.modalId).modal('hide');
                $('#' + this.modalIdCode).modal('hide');
                $('#' + this.modalIdRestock).modal('hide');
            }).catch(error => {
                if (error.response) {
                    if (error.response.status === 422 || error.response.data.error === 'Duplicate codes are not allowed.') {
                        Swal.fire({
                            title: 'Warning',
                            html: `You entered duplicate serial numbers. Please check your input.`,
                            icon: 'warning',
                        });
                    } else {
                        // Handle other errors
                        Swal.fire({
                            title: 'Warning',
                            text: response.data.message,
                            icon: 'warning',
                        });
                    }
                } else {
                    // Handle network errors
                    Swal.fire({
                        title: 'Error',
                        text: 'Failed to connect to the server. Please check your internet connection.',
                        icon: 'error',
                    });
                }
            });
            },
        },
        mounted() {
            this.getData();
        }
        
}

</script>
