<template>
    <div class="p-3">
        <BreadCrumbComponent tab_title="Borrow Tools"></BreadCrumbComponent>
        <div class="row mb-3">
            <div class="col-lg-2">
                <select v-model="selectedStatus" class="form-control">
                    <option value="" disabled selected>Select Status</option> 
                    <option value="Sale">For Sale</option>
                    <option value="Sold">Sold</option>
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
                                <h5 class="card-title">{{ tool.brand_name }} {{ tool.tool_name }}</h5>
                                <p class="card-text">Price: {{ tool.price }}</p>
                            </div>
                            <div style="width: 50%;">
                                <img v-if="tool.product_image" :src="'/images/' + tool.product_image" alt="Tool Image" class="img-fluid" style="height: 250px;">
                                <p v-else>No Image</p>
                            </div>
                        </div>
                        <button class="btn btn-primary" v-on:click="showDetails(tool)">Borrow</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Borrow a Tool Modal -->
        <ModalComponent :id="modalId" :title="modalTitle" :size="modalSize" :position="modalPosition">
            <template #modalHeader>
                <div class="m-auto">
                    <h4>Borrow a Tool</h4>
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
                        <label for="">Return Days</label>
                        <select class="form-control" v-model="dataValues.return_days_id" @change="calculateReturnDate">
                            <option v-for="returnday in returndays" :value="returnday.number_of_days">{{ returnday.number_of_days }} Days</option>
                        </select>
                        <div class="text-danger" v-if="errors.returnday">{{ errors.returnday[0] }}</div>
                    </div> 
                    <div class="col-12 pb-2">
                        <label for="">Penalty</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">₱</span>
                            </div>
                            <input type="text" class="form-control" v-model="dataValues.penalty" disabled>
                        </div>
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
                        <label for="serialNumber">Please Return the Product on or before:</label>
                        <input type="text" class="form-control" v-model="dataValues.return_date" disabled>
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
                            You are about to purchase this tool for <b>₱{{ this.dataValues.price }}</b> and need to return it on or before <b>{{ this.dataValues.return_date }}</b>.
                        </p>
                        <p>
                            If not return to the given days, a penalty of <b>₱{{ this.dataValues.penalty }}</b> will be charged.
                        </p>
                        <p>
                            Delivery will be made within <b>3-5 working days.</b>
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
                <button class="btn btn-success" v-on:click="borrowProduct()">Borrow Product</button>
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
            numberPrice: 0,
            totalPrice: 0,
            serialNumberChecked : 0,
            returndays: [],
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
    watch: {
        'dataValues.return_days_id': function(newVal, oldVal) {
            // If return_days_id changes, update the penalty based on the selected return days
            const selectedReturnDay = this.returndays.find(day => day.number_of_days === newVal);
            if (selectedReturnDay) {
                // Update the penalty based on the selected return days
                this.dataValues.penalty = selectedReturnDay.penalty;
            }
        }
    },
    methods: {
        calculateReturnDate() {
            const selectedReturnDays = this.dataValues.return_days_id;
            const currentDate = new Date();
            // Add selected return days to the current date
            currentDate.setDate(currentDate.getDate() + selectedReturnDays);
            // Format the return date with the month as a word
            const options = { month: 'long', day: '2-digit', year: 'numeric' };
            const formattedReturnDate = currentDate.toLocaleDateString("en-US", options);
            // Set the return date value to the data model
            this.dataValues.return_date = formattedReturnDate;
        },
        showDetails(tool) {
            this.dataValues = tool;
            this.checkedSerialNumbers = [];
            $('#' + this.modalId).modal('show');
        },
        getData() {
            axios.get('/borrowtools/show').then(response => {
                this.data = response.data.data;
                this.returndays = response.data.returndays;
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
            
            this.serialNumberChecked = this.checkedSerialNumbers.length;
             // Calculate the subtotal based on the number of selected serial numbers
            const subtotal = this.dataValues.price * this.checkedSerialNumbers.length;
            // Calculate the total price including VAT
            const penalty = this.dataValues.penalty; // Assuming VAT is defined in the data or a constant
            const totalPrice = (subtotal + penalty).toFixed(2);
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
            this.dataValues.price *= this.checkedSerialNumbers.length; 
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

            axios.post('/borrowtools/filterData', searchData)
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
        borrowProduct(){
            const data = {
                serial_numbers: this.checkedSerialNumbers,
                dataValues: this.dataValues
            };

            axios.post('/borrowtools/borrowTools', data)
                .then(response => {
                    Swal.fire({
                        title: "Success!",
                        text: "Product has been successfully borrowed.",
                        icon: 'success',
                        timer: 3000
                    });
                    window.location.reload();
                    $('#' + this.modalIdFinal).modal('hide');
                    this.getData();
                })
                .catch(error => {
                    Swal.fire({
                        title: "Error!",
                        text: "Failed to borrow product.",
                        icon: 'error',
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
