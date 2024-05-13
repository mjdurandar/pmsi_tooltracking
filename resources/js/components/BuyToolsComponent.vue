<template>
    <div class="p-3">
        <BreadCrumbComponent tab_title="Buy Tools"></BreadCrumbComponent>
        <!-- FILTER DATA -->
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
            <div class="col-lg-2">
                <button class="btn btn-primary" @click="filterData">Search</button>
                <button class="btn btn-success ml-1" @click="refresh"><i class="fas fa-sync-alt"></i></button>
            </div>
            <div class="col-lg-4 d-flex justify-content-end">
                <button class="btn btn-success p-3" @click="checkout">Checkout</button>
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
                        <button class="btn btn-success" v-on:click="showDetails(tool)">Purchase Product</button>
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
                        <label for="">Price each</label>
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
                    <!-- <div class="col-12 pb-2">
                        <label for="serialNumber">Please select Serial Number(s):</label>
                        <div class="form-check" v-for="(serialNumber, index) in this.dataValues.serial_numbers" :key="index">
                            <input class="form-check-input" type="checkbox" :id="'serialNumber_' + index" :value="serialNumber" @change="updateCheckedValues($event.target.value)">
                            <label class="form-check-label" :for="'serialNumber_' + index">{{ serialNumber }}</label>
                        </div>
                        <div class="text-danger" v-if="errors.serial_numbers">{{ errors.serial_numbers[0] }}</div>
                    </div> -->
                    <div class="col-12 pb-2">
                        <label for="">Total including Vat:</label>
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
                    <button class="btn btn-primary" v-on:click="reviewProduct">Proceed</button>
                </div>
            </template>
        </ModalComponent>

        <!-- SELECT SRN AND PRN CODE -->
        <ModalComponent :id="modalIdSelect" :title="modalTitle" :size="modalSize" :position="modalPosition">
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
                    <button class="btn btn-success" v-on:click="addToReleaseCart">Add to Cart</button>
                </div>
            </template>
        </ModalComponent>


        <!-- REVIEW PURCHASED MODAL -->
        <ModalComponent :id="modalIdReview" :title="modalTitle" :size="modalSizeFinal" :position="modalPosition">
            <template #modalHeader>
                <div class="m-auto">
                    <h4>Review your Purchased</h4>
                </div>
            </template>
            <template #modalBody>
                <div class="modal-body" style="max-height: 400px; overflow-y: auto;">
                    <div v-for="(product, index) in selectedProducts" :key="index">
                        <div class="row">
                            <div class="col-6 text-center m-auto" v-if="product.dataValues.product_image">
                                <img :src="'/images/' + product.dataValues.product_image" alt="Product Image" class="img-fluid" style="height:300px;">
                            </div>
                            <div class="col-6">
                                <p>
                                    You are about to purchase <b>{{ product.dataValues.brand }} {{ product.dataValues.tool }}</b>
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
                                    The Product will be delivered in this location
                                    <b>{{ userLocation }}</b>
                                </p>
                                <p>
                                    Once you're confident that everything is in order, proceed by clicking <b>"Confirm"</b> button below.
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6">
                        </div>
                        <div class="col-6">
                            <div>
                                <b>Total:</b> <input type="text" class="form-control" v-model="reviewTotal" :style="{borderColor: 'green', color: 'darkgreen'}" disabled>
                            </div>
                            <div>
                                <b>VAT Total (12%):</b> <input type="text" class="form-control" v-model="reviewVatTotal" :style="{borderColor: 'green', color: 'darkgreen'}" disabled>
                            </div>
                            <div>
                                <b>Grand Total:</b> <input type="text" class="form-control" v-model="reviewGrandTotal" :style="{borderColor: 'green', color: 'darkgreen'}" disabled>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
            <template #modalFooter>
                <div class="text-right">
                    <button class="btn btn-success" v-on:click="getProduct">Confirm</button>
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
            userLocation: [],
            errors: [],
            selectedStatus: '',
            selectedBrand: '',
            selectedTool: '',
            selectedSpecs: '',
            totalPrice: 0,
            priceVat: 0,
            reviewTotal: 0,
            reviewVatTotal: 0,
            reviewGrandTotal: 0,
            serialNumbers: [],
            checkedSerialNumbers: [],
            selectedIndexes: [],
            vat: 12,
            backendTotal: 0,
            selectedProducts: [],
            vatPercentage: 0.12,
            dataValues: {
            },
            modalId : 'modal-buytools',
            modalIdFinal : 'modal-buytools-final',
            modalIdSelect : 'modal-select-srn',
            modalTitle : 'Buy Tools',
            modalPosition: 'modal-dialog-centered',
            modalSize : 'modal-md',
            modalSizeFinal : 'modal-lg',
            modalIdReview: 'modal-review',
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
            this.priceVat = tool.price * this.vatPercentage;
            this.totalPrice = Number(tool.price) + this.priceVat;
            this.selectedIndexes.length = 0;
            $('#' + this.modalId).modal('show');
        },
        selectAll() {
            // Get the keys of the serialNumbers object and convert them to an array of indexes
            const keys = Object.keys(this.serialNumbers);
            this.selectedIndexes = keys.map(Number); // Convert keys to numbers to ensure proper indexing
        },
        clearSelected()
        {
            this.selectedIndexes.length = 0;
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
                if (existingProduct.dataValues.status !== 'For Sale') {
                    this.selectedProducts.push({
                        dataValues: this.dataValues,
                        status: this.dataValues.status,
                        selectedSerialNumbers: selectedSerials,
                    });
                    Swal.fire({
                        title: 'Added to Cart!',
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
                    vatTotal: this.priceVat,
                });
                console.log(this.selectedProducts);
                Swal.fire({
                    title: 'Added to Cart!',
                    text: '',
                    icon: 'success',
                    timer: 3000
                });
            }
            $('#' + this.modalIdSelect).modal('hide');
        },
        reviewProduct() {
            // // Validate the form
            this.checkedSerialNumbers = [];
            this.serialNumbers = { ...this.dataValues.serial_numbers };
            // Show the final modal
            $('#' + this.modalIdSelect).modal('show');
            $('#' + this.modalId).modal('hide');
        },
        checkout()
        {   
            // // Calculate reviewTotal, reviewVatTotal, and reviewGrandTotal
            const reviewTotalValue = this.selectedProducts.reduce((total, product) => {
                const pricePerSerial = product.dataValues.price * product.selectedSerialNumbers.length;
                return total + pricePerSerial;
            }, 0);

            // Calculate reviewVatTotalValue based on the number of serial numbers collected
            const reviewVatTotalValue = reviewTotalValue * 0.12;

            // Calculate reviewGrandTotalValue
            const reviewGrandTotalValue = reviewTotalValue + reviewVatTotalValue;
            // Format the values with a peso sign and no decimal places
            this.reviewTotal = '₱' + reviewTotalValue.toFixed(0);
            this.reviewVatTotal = '₱' + reviewVatTotalValue.toFixed(0);
            this.reviewGrandTotal = '₱' + reviewGrandTotalValue.toFixed(0);

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
        getProduct(){
            const data = {
                selectedProducts: this.selectedProducts,
                total_price: this.reviewGrandTotal,
            };

            axios.post('/buytools/buyTools', data)
                .then(response => {
                    Swal.fire({
                        title: "Product Purchased Successfully!",
                        icon: 'success',
                        timer: 3000
                    });
                    this.getData();
                    this.selectedProducts = [];
                    $('#' + this.modalIdReview).modal('hide');
                })
                .catch(errors => {
                    // Check if the response contains an error indicating insufficient funds
                    if(errors.response.data.error === 'Insufficient funds') {
                                Swal.fire({
                                    title: 'Insufficient Funds',
                                    text: errors.response.data.error,
                                    icon: 'error',
                                    timer: 3000
                                });
                            }
                            else if(errors.response.data.error === 'Low Stocks')
                            {
                                Swal.fire({
                                    title: 'Low Stocks',
                                    text: 'Please wait for the Supplier to replenish their Product',
                                    icon: 'warning',
                                    timer: 3000
                                });
                            }
                            else {
                                // Display general warning message for other errors
                                Swal.fire({
                                    title: 'Warning',
                                    text: 'An error occurred. Please try again later.',
                                    icon: 'warning',
                                    timer: 3000
                                });
                            }
                            this.errors = errors.response.data.errors;
                });
        }
    },
    mounted() {
            this.getData();
        }
        


}

</script>
