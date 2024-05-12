<template>
    <div class="p-3">
        <BreadCrumbComponent tab_title="Supplier"></BreadCrumbComponent>
        <!-- FILTER DATA -->
        <div class="row mb-3">
            <div class="col-lg-2">
                <select class="form-control" v-model="supplier_id">
                    <option value="" disabled selected>Supplier</option>
                    <option v-for="supplier in suppliers" :value="supplier.id">{{ supplier.name }}</option>
                </select>
            </div>
            <div class="col-lg-2">
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
                    <option value="Ryobi">Ryobi</option> 
                </select>
            </div>
            <div class="col-lg-2">
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
            <div class="col-lg-2">
                <select v-model="selectedSpecs" class="form-control">
                    <option value="" disabled selected>Specifications</option> 
                    <option value="battery">Battery</option>
                    <option value="corded">Corded</option>
                </select>
            </div>
            <div class="col-lg-2">
                <button class="btn btn-primary" @click="filterData">Search</button>
                <button class="btn btn-success ml-1" @click="refresh"><i class="fas fa-sync-alt"></i></button>
            </div>
            <div class="col-lg-2 d-flex justify-content-end">
                <button class="btn btn-success p-3" @click="checkout"><b>Checkout</b></button>
            </div>
        </div>

        <!-- PRODUCT CARD -->
        <div class="row">
            <div class="col-md-4" v-for="(product, index) in products" :key="index">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div style="width: 50%;">
                                <h2 class="card-title" style="font-weight: bold;">{{ product.brand + ' ' + product.tool }}</h2>
                                <p class="card-title">{{ product.supplier_name }} <br> Stocks: {{ product.stocks }} </p>
                            </div>
                            <div style="width: 50%;">
                                <img v-if="product.image" :src="'/images/' + product.image" alt="Product Image" class="img-fluid" style="height: 250px;">
                                <p v-else>No Stocks</p>
                            </div>
                        </div>
                        <button class="btn btn-success" @click="purchaseProduct(product)">Purchase Product</button>
                        <button class="btn btn-warning ml-1" @click="companyInformation(product)"><i class="fa-solid fa-circle-info"></i></button>  
                    </div>
                </div>
            </div>
        </div>

        <!-- PURCHASE PRODUCT MODAL -->
        <ModalComponent :id="modalIdPurchaseProduct" :title="modalTitle" :size="modalSize" :position="modalPosition">
            <template #modalHeader>
                <div class="m-auto">
                    <h4>Purchase this Product</h4>
                </div>
            </template>
            <template #modalBody>
                <div class="row">
                    <div class="col-6">
                        <label for="">Brand</label>
                        <input type="text" class="form-control" v-model="dataValues.brand" disabled>
                    </div> 
                    <div class="col-6">
                        <label for="">Tool</label>
                        <input type="text" class="form-control" v-model="dataValues.tool" disabled>
                    </div> 
                </div>
                <div class="row">
                    <div class="col-12">
                        <label for="">Power Source</label>
                        <input type="text" class="form-control" v-model="dataValues.powerSources" disabled>
                    </div> 
                </div>
                <div class="row">
                    <div class="col-6">
                        <label for="">Voltage</label>
                        <input type="text" class="form-control" v-model="dataValues.voltage" disabled>
                    </div> 
                    <div class="col-6">
                        <label for="">Weight</label>
                        <input type="text" class="form-control" v-model="dataValues.weight" disabled>
                    </div> 
                    <div class="col-6">
                        <label for="">Dimension</label>
                        <input type="text" class="form-control" v-model="dataValues.dimensions" disabled>
                    </div> 
                    <div class="col-6">
                        <label for="">Material</label>
                        <input type="text" class="form-control" v-model="dataValues.material" disabled>
                    </div> 
                    <div class="col-12">
                        <label for="">Supplier</label>
                        <input type="text" class="form-control" v-model="dataValues.supplier_name" disabled>
                    </div>
                    <div class="col-6">
                        <label for="">Price per pc</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">₱</span>
                            </div>
                            <input type="text" class="form-control" v-model="dataValues.price" disabled>
                        </div>
                    </div>  
                    <div class="col-6">
                        <label for="">12% Vat</label>
                        <input type="number" class="form-control" v-model="vat" disabled>
                    </div>
                    <div class="col-6">
                        <label for="">How many?</label>
                        <input type="number" class="form-control" v-model="requestedItems" @input="calculateTotal" min="0">
                        <div class="text-danger" v-if="errors.requestedItems">{{ errors.requestedItems[0] }}</div>
                    </div>  
                    <div class="col-6">
                        <label for="">Total</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">₱</span>
                            </div>
                            <input type="text" class="form-control" v-model="total" disabled>
                        </div>
                    </div>
                    <div class="col-12">
                        <label for="">Grand Total including 12% Vat</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">₱</span>
                            </div>
                            <input type="text" class="form-control" v-model="vatTotal" disabled>
                        </div>
                    </div>
                </div>
            </template>
            <template #modalFooter>
                <div class="text-right">
                    <button class="btn btn-success" v-on:click="yesProduct(props)">Yes</button>
                    <button style="margin-left: 5px;" class="btn btn-danger" v-on:click="noProduct">No</button>
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
                    <div class="row" v-for="(serialNumber, index) in serialNumbers" :key="index">
                        <div class="col-lg-12 d-flex justify-content-around mt-2">
                        <div class="m-auto">
                            <input type="checkbox" v-model="selectedIndexes" :value="index" :disabled="selectedIndexes.length >= requestedItems">
                        </div>
                        <div class="m-auto">
                            <div>Serial Number {{ index + 1 }}</div>
                        </div>
                        <div>
                            <input type="text" v-model="serialNumber.serial_number" class="form-control" disabled>
                        </div>
                        </div>
                    </div>
                </div>
            </template>
            <template #modalFooter>
                <div class="text-right">
                    <button class="btn btn-primary mr-2" v-on:click="selectAll">Select All</button>
                    <button class="btn btn-primary mr-2" v-on:click="clearSelected">Clear</button>
                    <button class="btn btn-warning" v-on:click="getSelectedValue">Add to Cart</button>
                </div>
            </template>
        </ModalComponent>

        <!-- REVIEW PURCHASED MODAL -->
        <ModalComponent :id="modalIdReview" :title="modalTitle" :size="modalSizeTermsandCondition" :position="modalPosition">
            <template #modalHeader>
                <div class="m-auto">
                    <h4>Review your Purchased</h4>
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
                    <button class="btn btn-success" v-on:click="termsAndConditionModal">Confirm</button>
                </div>
            </template>
        </ModalComponent>

        <!-- TERMS AND CONDITION MODAL -->
        <ModalComponent :id="modalIdTermsandCondition" :title="modalTitle" :size="modalSizeTermsandCondition" :position="modalPosition">
            <template #modalHeader>
                <div class="m-auto">
                    <h4>Terms and Conditions</h4>
                </div>
            </template>
            <template #modalBody style="max-height: 400px; overflow-y: auto;">
                <div class="container">
                <div class="row">
                    <div class="col-md-6">
                        <ol>
                            <li>
                                <strong>PRODUCT DESCRIPTION:</strong> The Seller agrees to supply, and the Buyer agrees to purchase the product(s) described as follows ([provide detailed description of the product(s), including specifications, quality standards, and any other relevant details]).
                            </li>
                            <li>
                                <strong>QUANTITY:</strong> The total quantity of the product(s) to be supplied under this Contract is [Quantity], as detailed in the attached Schedule A.
                            </li>
                            <li>
                                <strong>PRICE AND PAYMENT TERMS:</strong> The total purchase price for the product(s) shall be [Total Price] payable by the Buyer to the Seller. Payment shall be made according to the following terms: [Payment Terms, e.g., payment schedule, due dates, acceptable methods of payment].
                            </li>
                            <li>
                                <strong>DELIVERY TERMS:</strong> The Seller shall deliver the product(s) to [Delivery Address] on or before [Delivery Date]. The Seller is responsible for all shipping costs and insurance until the product(s) is delivered to the specified address.
                            </li>
                        </ol>
                    </div>
                    <div class="col-md-6">
                        <ol start="6">
                            <li>
                                <strong>WARRANTY:</strong> The Seller warrants that the product(s) shall be free from defects in material and workmanship for a period of [Warranty Period] from the date of delivery.
                            </li>
                            <li>
                                <strong>LIMITATION OF LIABILITY:</strong> The Seller's liability under this Contract shall be limited to the replacement of defective products or refund of the purchase price, at the Seller's option. In no event shall the Seller be liable for any incidental or consequential damages.
                            </li>
                            <li>
                                <strong>TERMINATION:</strong> This Contract may be terminated by either party upon [Number of Days] days written notice if the other party breaches any of its obligations under this Contract and fails to cure such breach within said notice period.
                            </li>
                            <li>
                                <strong>ENTIRE AGREEMENT:</strong> This Contract constitutes the entire agreement between the parties and supersedes all prior negotiations, understandings, and agreements between the parties.
                            </li>
                        </ol>
                    </div>
                </div>
                <div class="d-flex">
                    <div>
                        <input type="checkbox" v-model="agreementChecked">
                    </div>
                    <div style="margin-left:5px">
                        <strong>Please check the box if you agree to the terms and conditions above.</strong>
                    </div>
                </div>
                </div>
            </template>
            <template #modalFooter>
                <div class="d-flex justify-content-between">
                    <div>
                        <button class="btn btn-danger mr-2" v-on:click="cancelProduct">Cancel</button>
                    </div>
                    <div>
                        <button class="btn btn-success" v-on:click="getProduct" :disabled="!agreementChecked">Purchase</button>
                    </div>
                </div>
            </template>
        </ModalComponent>

        <!-- COMPANY INFORMATION MODAL -->
        <ModalComponent :id="modalCompanyInfo" :title="modalTitle" :size="modalSize" :position="modalPosition">
            <template #modalHeader>
                <div class="m-auto">
                    <h4>{{ this.supplierName }}</h4>
                </div>
            </template>
            <template #modalBody>
                <div class="modal-body" style="max-height: 400px; overflow-y: auto; text-align: justify;">
                    {{ this.company_description }}
                </div>
            </template>
            <template #modalFooter>
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
                selectedSpecs : '',
                requestedItems : '',
                supplierName : '',
                reviewTotal: 0,
                reviewVatTotal: 0,
                reviewGrandTotal: 0,
                company_description: '',
                userLocation : [],
                vat: 12,
                selectedSerialNumbers : [],
                serialNumbers: [],
                selectedIndexes: [],
                suppliers : [],
                supplier_id : '',
                products : [],  
                requestData : [],
                userBalance: [],
                selectedProducts: [],
                agreementChecked: false,
                requestedItems : 0,
                total: 0,
                vatTotal: 0,
                columns : ['name' ,'action'],
                errors: [],
                options : {
                    headings : {
                        name : 'Supplier',
                        action : 'Action',
                    },
                    filterable: false,
                    sortable: []
                },
                dataValues: {
                    name: '',
                    supplier_id: '',    
                },
                modalId : 'modal-supplier',
                modalIdPurchaseProduct : 'modal-purchase-product',
                modalIdTermsandCondition : 'modal-terms-and-condition',
                modalCompanyInfo : 'modal-company-info',
                modalSizeTermsandCondition : 'modal-lg',
                modalIdReview : 'modal-review',
                modalIdSelect : 'modal-select-code',
                modalTitle : 'Supplier',
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
        clearSelected()
        {
            this.selectedIndexes.length = 0;
        },
        selectAll() {
            this.selectedIndexes = this.serialNumbers
            .map((serialNumber, index) => index)
            .slice(0, this.requestedItems);
        },
        getSelectedValue() {
            if (this.selectedIndexes.length === 0) {
                Swal.fire({
                    title: 'No Serial Number Selected',
                    text: 'Please select a Serial Number!',
                    icon: 'warning',
                    timer: 3000
                });
            } else {
                const selectedSerialNumbers = this.selectedIndexes.map(index => this.serialNumbers[index].serial_number);
                let existingDataIndex = -1;
                
                // Find if data with the same values already exists
                existingDataIndex = this.selectedProducts.findIndex(product => {
                    // Check if dataValues are the same
                    return JSON.stringify(product.dataValues) === JSON.stringify(this.dataValues);
                });

                if (existingDataIndex !== -1) {
                    // Data with same values already exists
                    const existingProduct = this.selectedProducts[existingDataIndex];
                    const newSerialNumbers = selectedSerialNumbers.filter(serial => !existingProduct.selectedSerialNumbers.includes(serial));
                    
                    if (newSerialNumbers.length > 0) {
                        // Add new serial numbers to existing data
                        existingProduct.selectedSerialNumbers.push(...newSerialNumbers);
                        Swal.fire({
                            title: 'Serial Number Added to Existing Data!',
                            text: '',
                            icon: 'success',
                            timer: 3000
                        });
                    } else {
                        Swal.fire({
                            title: 'Serial Number Already Selected for This Data!',
                            text: 'Please select a different one!',
                            icon: 'warning',
                            timer: 3000
                        });
                        return;
                    }
                } else {
                    // Add new data entry with selected serial numbers
                    this.selectedProducts.push({
                        dataValues: this.dataValues,
                        selectedSerialNumbers: selectedSerialNumbers,
                        total: this.total,
                        vatTotal: this.vatTotal
                    });
                    Swal.fire({
                        title: 'Added to Cart!',
                        text: '',
                        icon: 'success',
                        timer: 3000
                    });
                }

                $('#' + this.modalIdSelect).modal('hide');
                // $('#' + this.modalIdReview).modal('show');
            }
        },
        termsAndConditionModal()
        {
            $('#' + this.modalIdReview).modal('hide');
            $('#' + this.modalIdTermsandCondition).modal('show');
        },
        getData() {
            axios.get('/supplier/show').then(response => {
                this.data = response.data.data;
                this.suppliers = response.data.suppliers;
                this.products = response.data.products;
                this.userLocation = response.data.userLocation;
                this.userBalance = response.data.userBalance;
            })
        },
        clearInputs() {
            this.dataValues = '';
        },
        noProduct() {
            $('#' + this.modalIdPurchaseProduct).modal('hide');
        },
        refresh() {
            window.location.reload();
        },
        filterData() {
            const searchData = {
                supplier_id: this.supplier_id,
                brand: this.selectedBrand,
                tool: this.selectedTool,
                specs: this.selectedSpecs
            };

            axios.post('/supplier/filterData', searchData)
                .then(response => {
                    this.products = response.data.products;
                    if (this.products.length === 0) {
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
        validateForm() {
            this.errors = [];

            if (!this.requestedItems) {
                this.errors.requestedItems = ['Please input how many Products.'];
            }

            // Check if any errors are present
            if (Object.keys(this.errors).length > 0) {
                return false; // Validation failed
            }

            return true; // Validation passed
        },
        cancelProduct() {
            Swal.fire({
                title: 'Are you sure you want to cancel?',
                text: 'All the products you added to the cart will be removed!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, cancel it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Proceed with canceling the product
                    $('#' + this.modalIdTermsandCondition).modal('hide');
                    this.selectedProducts = [];
                    Swal.fire({
                    title: "Products Canceled!",
                    icon: 'success',
                    timer: 3000
                });
                    // You can call a method to handle the cancellation here
                }
            });
        },
        purchaseProduct(product){
            this.dataValues = product;
            this.requestedItems = '';
            this.total = 0;
            this.vatTotal = 0;
             // Clear selected serial numbers
            this.selectedSerialNumbers = [];
            $('#' + this.modalIdPurchaseProduct).modal('show');
        },
        companyInformation(product){
            $('#' + this.modalCompanyInfo).modal('show');
            this.company_description = product.company_description;
            this.supplierName = product.supplier_name;
        },
        checkout(){
            if(this.selectedProducts.length === 0){
                Swal.fire({
                    title: "No Products selected!",
                    icon: 'warning',
                    timer: 3000
                });
                return;
            }
            // // Calculate reviewTotal, reviewVatTotal, and reviewGrandTotal
            const reviewTotalValue = this.selectedProducts.reduce((total, product) => total + product.total, 0);
            const reviewVatTotalValue = this.selectedProducts.reduce((total, product) => total + (product.total * 0.12), 0);
            const reviewGrandTotalValue = reviewTotalValue + reviewVatTotalValue;

            // Format the values with a peso sign and no decimal places
            this.reviewTotal = '₱' + reviewTotalValue.toFixed(0);
            this.reviewVatTotal = '₱' + reviewVatTotalValue.toFixed(0);
            this.reviewGrandTotal = '₱' + reviewGrandTotalValue.toFixed(0);
            console.log(this.reviewTotal, this.reviewVatTotal, this.reviewGrandTotal);
            $('#' + this.modalIdReview).modal('show');
        },
        yesProduct() {
            this.selectedIndexes.length = 0;
            if (this.validateForm()) {
                if(this.requestedItems > this.dataValues.stocks){
                    Swal.fire({
                            title: "Insufficient Stocks!",
                            icon: 'warning',
                            timer: 3000
                        });
                        return;
                }
                if(this.userBalance < this.total){
                    Swal.fire({
                            title: "Low Balance!",
                            icon: 'warning',
                            timer: 3000
                        });
                        return;
                }
                axios.post('/supplier/getId', this.dataValues).then(response => {
                    this.agreementChecked = false;
                    this.serialNumbers = response.data.serialNumbers;
                    const requestData = {
                        id: this.dataValues.id,
                        requestedItems: this.requestedItems,
                        total: this.vatTotal
                    }
                    this.requestData = requestData;
                    $('#' + this.modalIdPurchaseProduct).modal('hide');
                    $('#' + this.modalIdSelect).modal('show');
                })
                .catch(errors => {
                        this.errors = errors.response.data.errors;
                })
            }
        },
        calculateTotal() {
            this.total = this.requestedItems * this.dataValues.price;
            this.vatTotal = this.total + (this.total * (this.vat / 100));
        },
        getProduct(){
            this.requestedItems = 0;
            // this.vatTotal = 0;
            this.agreementChecked = false;
            this.requestData.selectedSerialNumbers = this.selectedSerialNumbers;
            
            axios.post('/supplier/purchaseProduct', this.selectedProducts)
                .then(response => {
                    Swal.fire({
                        title: "Product Purchased Successfully!",
                        icon: 'success',
                        timer: 3000
                    });
                    this.getData();
                    this.selectedProducts = [];
                    $('#' + this.modalIdPurchaseProduct).modal('hide');
                    $('#' + this.modalIdTermsandCondition).modal('hide');
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
        },
        },
        mounted() {
            this.getData();
        }
        


}

</script>
