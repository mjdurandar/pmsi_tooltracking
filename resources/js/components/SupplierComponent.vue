<template>
    <div class="p-3">
        <BreadCrumbComponent tab_title="Supplier"></BreadCrumbComponent>
        <div class="row mb-3">
            <div class="col-lg-2">
                <select class="form-control" v-model="dataValues.supplier_id">
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
        <div class="row">
            <div class="col-md-4" v-for="(product, index) in products" :key="index">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div style="width: 50%;">
                                <h2 class="card-title" style="font-weight: bold;">{{ product.brand + ' ' + product.tool }}</h2>
                                <p class="card-title">{{ product.supplier_name }}</p>
                            </div>
                            <div style="width: 50%;">
                                <img v-if="product.image" :src="'/images/' + product.image" alt="Product Image" class="img-fluid" style="height: 250px;">
                                <p v-else>No Stocks</p>
                            </div>
                        </div>
                        <button class="btn btn-success" @click="purchaseProduct(product)">Purchase Product</button>
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
                    <div class="col-12">
                        <label for="">Price per pc</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">₱</span>
                            </div>
                            <input type="text" class="form-control" v-model="dataValues.price" disabled>
                        </div>
                    </div>  
                    <div class="col-12">
                        <label for="">How many?</label>
                        <input type="number" class="form-control" v-model="requestedItems" @input="calculateTotal">
                        <div class="text-danger" v-if="errors.requestedItems">{{ errors.requestedItems[0] }}</div>
                    </div>  
                    <div class="col-12">
                        <label for="">Total</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">₱</span>
                            </div>
                            <input type="text" class="form-control" v-model="total" disabled>
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

        <!-- TERMS AND CONDITION MODAL -->
        <ModalComponent :id="modalIdTermsandCondition" :title="modalTitle" :size="modalSizeTermsandCondition" :position="modalPosition">
            <template #modalHeader>
                <div class="m-auto">
                    <h4>Terms and Conditions</h4>
                </div>
            </template>
            <template #modalBody>
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
                <div class="text-right">
                    <button class="btn btn-success" v-on:click="getProduct" :disabled="!agreementChecked">Purchase</button>
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
                selectedSpecs : '',
                requestedItems : '',
                suppliers : [],
                supplier_id : '',
                products : [],  
                requestData : [],
                agreementChecked: false,
                requestedItems : 0,
                total: 0,
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
                modalSizeTermsandCondition : 'modal-lg',
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
        getData() {
            axios.get('/supplier/show').then(response => {
                this.data = response.data.data;
                this.suppliers = response.data.suppliers;
                this.products = response.data.products;
            })
        },
        clearInputs() {
            this.dataValues = '';
        },
        refresh() {
            window.location.reload();
        },
        filterData() {
            const searchData = {
                supplier_id: this.dataValues.supplier_id,
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
        purchaseProduct(product){
            this.dataValues = product;
            this.requestedItems = '';
            this.total = '';
            $('#' + this.modalIdPurchaseProduct).modal('show');
        },
        yesProduct() {
            this.agreementChecked = false;
            if(this.requestedItems == 0){
                Swal.fire({
                    title: "Insufficient Stocks!",
                    icon: 'warning',
                    timer: 3000
                });
                return;
            }
            const requestData = {
                id: this.dataValues.id,
                requestedItems: this.requestedItems,
                total: this.total
            }

            this.requestData = requestData;

            $('#' + this.modalIdPurchaseProduct).modal('hide');
            $('#' + this.modalIdTermsandCondition).modal('show');
        },
        calculateTotal() {
            this.total = this.requestedItems * this.dataValues.price;
        },
        getProduct(){
            this.requestedItems = 0;
            this.total = 0;
            this.agreementChecked = false;
            axios.post('/supplier/purchaseProduct', this.requestData)
                .then(response => {
                    // this.dataValues.stocks -= this.requestedItems;
                    // this.balance -= this.total;
                    Swal.fire({
                        title: "Product Purchased Successfully!",
                        icon: 'success',
                        timer: 3000
                    });
                    $('#' + this.modalIdPurchaseProduct).modal('hide');
                    $('#' + this.modalIdTermsandCondition).modal('hide');
                })
                .catch(error => {
                    console.error('Error requesting product:', error);
                });
        },
        editClicked(props) {
            this.dataValues = props.data;
            this.modalTitle= 'Edit Data';

            axios.get('/supplier/edit/' + this.dataValues.id).then(response => {
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
                    axios.get('/supplier/destroy/' + props.data.id).then(response => {
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
                axios.post('/supplier/store', this.dataValues).then(response => {
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
