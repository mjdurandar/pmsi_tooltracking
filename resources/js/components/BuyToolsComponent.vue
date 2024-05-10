<template>
    <div class="p-3">
        <BreadCrumbComponent tab_title="Buy Tools"></BreadCrumbComponent>
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
                    <option value="Ryobi">Ryobi</option> 
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
                <select v-model="selectedSpecs" class="form-control">
                    <option value="" disabled selected>Select Specifications</option> 
                    <option value="battery">Battery</option>
                    <option value="corded">Corded</option>
                </select>
            </div>
            <div>
                <button class="btn btn-primary" @click="filterData">Search</button>
                <button class="btn btn-success ml-1" @click="refresh"><i class="fas fa-sync-alt"></i></button>
            </div>
        </div>


        <!-- PRODUCT CARD -->
        <div class="row">
            <div class="col-md-4" v-for="(tool, index) in data" :key="index">
                <!-- Card component for each tool -->
                <div class="card">
                    <div class="card-body">
                        <!-- Display tool information -->
                        <div class="d-flex justify-content-between">
                            <div style="width: 50%;">
                                <h5 class="card-title"><b>{{ tool.brand_name }} {{ tool.tool_name }}</b></h5>
                                <p class="card-text">Price: {{ tool.price }} <br> Stocks: {{ tool.stocks }}</p>
                            </div>
                            <div style="width: 50%;">
                                <img v-if="tool.product_image" :src="'/images/' + tool.product_image" alt="Tool Image" class="img-fluid" style="height: 250px;">
                                <p v-else>No Image</p>
                            </div>
                        </div>
                        <button class="btn btn-success" v-on:click="showDetails(tool)">Buy</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Purchase a Tool Modal -->
        <ModalComponent :id="modalId" :title="modalTitle" :size="modalSize" :position="modalPosition">
            <template #modalHeader>
                <div class="m-auto">
                    <h4>Purchase a Tool</h4>
                </div>
            </template>
            <template #modalBody>
                <div class="row">
                    <div class="col-6 pb-2">
                        <label for="">Brand</label>
                        <input type="text" v-model="dataValues.brand_name" class="form-control" disabled> 
                    </div>  
                    <div class="col-6 pb-2">
                        <label for="">Tool</label>
                        <input type="text" v-model="dataValues.tool_name" class="form-control" disabled> 
                    </div>
                    <div class="col-12 pb-2">
                        <label for="">Power Source</label>
                        <input type="text" v-model="dataValues.powerSources" class="form-control" disabled> 
                    </div>
                    <div class="col-6 pb-2">
                        <label for="">Voltage</label>
                        <input type="text" v-model="dataValues.voltage" class="form-control" disabled> 
                    </div> 
                    <div class="col-6 pb-2">
                        <label for="">Weight</label>
                        <input type="text" v-model="dataValues.weight" class="form-control" disabled> 
                    </div>   
                    <div class="col-6 pb-2">
                        <label for="">Dimension</label>
                        <input type="text" v-model="dataValues.dimensions" class="form-control" disabled> 
                    </div> 
                    <div class="col-6 pb-2">
                        <label for="">Material</label>
                        <input type="text" v-model="dataValues.material" class="form-control" disabled> 
                    </div>   
                    <div class="col-6 pb-2">
                        <label for="">Price</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">₱</span>
                            </div>
                            <input type="number" class="form-control" v-model="dataValues.price" disabled>
                        </div>
                    </div>  
                    <div class="col-6 pb-2">
                        <label for="">12% Vat</label>
                            <input type="number" class="form-control" v-model="vat" disabled>
                    </div>  
                    <div class="col-12 pb-2">
                        <label for="serialNumber">Please select Serial Number(s):</label>
                        <div class="form-check" v-for="(serialNumber, index) in this.dataValues.serial_numbers" :key="index">
                            <input class="form-check-input" type="checkbox" :id="'serialNumber_' + index" :value="serialNumber" @change="updateCheckedValues($event.target.value)">
                            <label class="form-check-label" :for="'serialNumber_' + index">{{ serialNumber }}</label>
                        </div>
                        <div class="text-danger" v-if="errors.serial_numbers">{{ errors.serial_numbers[0] }}</div>
                    </div>
                    <div class="col-12 pb-2">
                        <label for="">Grand Total including Vat:</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">₱</span>
                            </div>
                            <input type="number" class="form-control" v-model="totalPrice" disabled>
                        </div>
                    </div>  
                </div>
            </template>
            <template #modalFooter>
                <div class="text-right">
                    <button class="btn btn-primary" v-on:click="reviewProduct">Review</button>
                </div>
            </template>
        </ModalComponent>

        <!-- REVIEW PRODUCT MODAL -->
        <ModalComponent :id="modalIdFinal" :title="modalTitle" :size="modalSizeFinal" :position="modalPosition">
            <template #modalHeader>
                <div class="m-auto">
                    <h3></h3>
                </div>
            </template>
            <template #modalBody>
                <div class="row">
                    <div class="col-6 text-center m-auto" v-if="dataValues.product_image">
                        <img :src="'/images/' + dataValues.product_image" alt="Current Image" class="img-fluid" style="height:300px;">
                    </div>
                    <div class="col-6">
                        <p>
                            <b>{{ this.dataValues.brand_name }} {{ this.dataValues.tool_name }}</b> with the voltage of {{ this.dataValues.voltage }}, dimension of {{ this.dataValues.dimensions }}, weight of {{ this.dataValues.weight }} and powerSources of {{ this.dataValues.powerSources }}.
                        </p>
                        <p>
                            The Serial Number(s) selected are: <b>{{ this.checkedSerialNumbers.join(', ') }}</b> 
                        </p>
                        <p>
                            You are about to purchase this tool for <b>₱{{ this.totalPrice }}</b> including VAT.
                        </p>
                        <p>
                            Delivery will be made within 3-5 working days.
                        </p>
                        <p>
                            This Product will be Delivered at: <b>{{ this.userLocation }}</b>
                        </p>
                        <p>
                            If all details are correct, click the "Buy Product" button to proceed.
                        </p>
                    </div>
                </div>
            </template>
            <template #modalFooter>
                <button class="btn btn-success" v-on:click="buyProduct()">Buy Product</button>
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
            userLocation: [],
            errors: [],
            selectedStatus: '',
            selectedBrand: '',
            selectedTool: '',
            selectedSpecs: '',
            totalPrice: 0,
            checkedSerialNumbers: [],
            vat: 12,
            vatPercentage: 0.12,
            dataValues: {
            },
            modalId : 'modal-buytools',
            modalIdFinal : 'modal-buytools-final',
            modalTitle : 'Buy Tools',
            modalPosition: 'modal-dialog-centered',
            modalSize : 'modal-md',
            modalSizeFinal : 'modal-lg',
        }
    },
    components: {
        FormComponent,
        ModalComponent,
        BreadCrumbComponent
    },
    methods: {
        showDetails(tool) {
            this.dataValues = tool;
            $('#' + this.modalId).modal('show');
        },
        getData() {
            axios.get('/buytools/show').then(response => {
                this.data = response.data.data;
                this.userLocation = response.data.userLocation;
            })
        },
        clearInputs() {
            this.dataValues = {
                name: '',
            }
            this.errors = [];
        },
        refresh(){
            window.location.reload();
        },
        updateCheckedValues(serialNumber) {
            if (this.checkedSerialNumbers.includes(serialNumber)) {
                this.checkedSerialNumbers = this.checkedSerialNumbers.filter(num => num !== serialNumber);
            } else {
                this.checkedSerialNumbers.push(serialNumber);
            }

             // Calculate the subtotal based on the number of selected serial numbers
                const subtotal = this.dataValues.price * this.checkedSerialNumbers.length;

            // Calculate the total price including VAT
            const vat = this.vatPercentage; // Assuming VAT is defined in the data or a constant
            const totalPrice = (subtotal * (1 + vat)).toFixed(2);
            // Update the total price
            this.totalPrice = totalPrice;
        },
        validateForm() {
            this.errors = [];

            if (this.checkedSerialNumbers.length === 0) {
                this.errors.serial_numbers = ['Please select at least one serial number'];
            }
            // Check if any errors are present
            if (Object.keys(this.errors).length > 0) {
                return false; // Validation failed
            }

            return true; // Validation passed
        },
        reviewProduct() {
            // Validate the form
            if (!this.validateForm()) {
                return;
            }

            // Show the final modal
            $('#' + this.modalIdFinal).modal('show');
            $('#' + this.modalId).modal('hide');
        },
        filterData() {
            const searchData = {
                status: this.selectedStatus,
                brand: this.selectedBrand,
                tool: this.selectedTool,
                specs: this.selectedSpecs
            };

            axios.post('/buytools/filterData', searchData)
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
        buyProduct(){
            const data = {
                serial_numbers: this.checkedSerialNumbers,
                total_price: this.totalPrice,
                dataValues: this.dataValues
            };

            axios.post('/buytools/buyTools', data)
                .then(response => {
                    Swal.fire({
                        title: "Success!",
                        text: response.data.message,
                        icon: 'success',
                        timer: 3000
                    });
                    $('#' + this.modalIdFinal).modal('hide');
                    this.getData();
                    window.location.reload();
                })
                .catch(error => {
                    Swal.fire({
                        title: "Warning!",
                        text: error.response.data.error ? error.response.data.error : "Failed to purchase product.",
                        icon: 'warning',
                        timer: 3000
                    });
                    console.error(error);
                });
        }
    },
    mounted() {
            this.getData();
        }
        


}

</script>
