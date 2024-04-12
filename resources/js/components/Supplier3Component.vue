<template>
    <div class="p-3">
        <!-- <BreadCrumbComponent tab_title="Supplier 1"></BreadCrumbComponent> -->
        
        <!-- ADD supplier3 PRODUCT -->
        <!-- <div class="card">
            <div class="card-body">
                <FormComponent 
                    :data="data"
                    :columns="columns"
                    :options="options"
                    btnName="Add Supplier Product"
                    @deleteClicked="deleteClicked"
                    @editClicked="editClicked"
                    @addClicked="addClicked"
                >
                </FormComponent>
            </div>
        </div>

        <ModalComponent :id="modalId" :title="modalTitle" :size="modalSize" :position="modalPosition">
            <template #modalHeader>
                <div class="m-auto">
                    <h4>Add Supplier Product</h4>
                </div>
            </template>
            <template #modalBody>
                <div class="row">
                    <div class="col-12">
                        <label for="">Name</label>
                        <input type="text" class="form-control" v-model="dataValues.name">
                        <div class="text-danger" v-if="errors.name">{{ errors.name[0] }}</div>
                    </div>  
                    <div class="col-12">
                        <label for="image">Image</label>
                        <input type="file" id="image" class="form-control" @change="onFileChange">
                        <div class="text-danger" v-if="errors.image">{{ errors.image[0] }}</div>
                    </div>
                    <div class="col-12" v-if="dataValues.image">
                        <label for="currentImage">Current Image</label>
                        <img :src="'/images/' + dataValues.image" alt="Current Image" class="img-fluid">
                    </div>
                    <div class="col-12">
                        <label for="">Description</label>
                        <textarea class="form-control" v-model="dataValues.description"></textarea>
                        <div class="text-danger" v-if="errors.description">{{ errors.description[0] }}</div>
                    </div>
                    <div class="col-12">
                        <label for="">Stocks</label>
                        <input type="number" class="form-control" v-model="dataValues.stocks">
                        <div class="text-danger" v-if="errors.stocks">{{ errors.stocks[0] }}</div>
                    </div>
                    <div class="col-12">
                        <label for="">Price per pc</label>
                        <input type="number" class="form-control" v-model="dataValues.price">
                        <div class="text-danger" v-if="errors.price">{{ errors.price[0] }}</div>
                    </div>  
                </div>
            </template>
            <template #modalFooter>
                <div class="text-right">
                    <button class="btn btn-success" v-on:click="storeData">Save</button>
                </div>
            </template>
        </ModalComponent> -->

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
                <input v-model="priceBelow" type="number" class="form-control" placeholder="Enter Price Below">
            </div>
            <div class="col-lg-2">
                <select v-model="selectedSpecs" class="form-control">
                    <option value="" disabled selected>Select Specifications</option> 
                    <option value="Battery">Battery</option>
                    <option value="Corded">Corded</option>
                </select>
            </div>
            <div>
                <button class="btn btn-primary" @click="filterData">Search</button>
                <button class="btn btn-success ml-1" @click="refresh"><i class="fas fa-sync-alt"></i></button>
                <button class="btn btn-warning ml-1" @click="information"><i class="fas fa-info-circle"></i></button>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-4" v-for="(product, index) in data" :key="index">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div style="width: 50%;">
                                <h2 class="card-title" style="font-weight: bold;">{{ product.name }}</h2>
                            </div>
                            <div style="width: 50%;">
                                <img v-if="product.image" :src="'/images/' + product.image" alt="Product Image" class="img-fluid" style="height: 250px;">
                                <p v-else>No Stocks</p>
                            </div>
                        </div>
                        <button class="btn btn-success" @click="requestProduct(product)">Request this Product</button>
                    </div>
                </div>
            </div>
        </div>
        
        <ModalComponent :id="modalIdFinal" :title="modalTitle" :size="modalSizeFinal" :position="modalPosition">
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
                    <button class="btn btn-success" v-on:click="getProduct" :disabled="!agreementChecked">Request</button>
                </div>
            </template>
        </ModalComponent>

        <!-- REQUEST THIS PRODUCT MODAL -->
        <ModalComponent :id="modalId" :title="modalTitle" :size="modalSize" :position="modalPosition">
            <template #modalHeader>
                <div class="m-auto">
                    <h4>Request this Product</h4>
                </div>
            </template>
            <template #modalBody>
                <div class="row">
                    <div class="col-12">
                        <label for="">Name</label>
                        <input type="text" class="form-control" v-model="dataValues.name" disabled>
                        <div class="text-danger" v-if="errors.name">{{ errors.name[0] }}</div>
                    </div>  
                    <div class="col-12">
                        <label for="">Specifications</label>
                        <textarea class="form-control" v-model="dataValues.specifications" disabled></textarea>
                        <div class="text-danger" v-if="errors.specifications">{{ errors.specifications[0] }}</div>
                    </div>
                    <div class="col-12">
                        <label for="">Stocks</label>
                        <input type="number" class="form-control" v-model="dataValues.stocks" disabled>
                        <div class="text-danger" v-if="errors.stocks">{{ errors.stocks[0] }}</div>
                    </div>
                    <div class="col-12">
                        <label for="">Supplier</label>
                        <select class="form-control" v-model="dataValues.supplier_id" disabled>
                            <option v-for="supplier in suppliers" :value="supplier.id">{{ supplier.name }}</option>
                        </select>  
                        <div class="text-danger" v-if="errors.supplier">{{ errors.supplier[0] }}</div>
                    </div> 
                    <div class="col-12">
                        <label for="">Price per pc</label>
                        <input type="number" class="form-control" v-model="dataValues.price" disabled>
                        <div class="text-danger" v-if="errors.price">{{ errors.price[0] }}</div>
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
                    <div class="col-12">
                        <label for="">Your Balance</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">₱</span>
                        </div>
                        <input type="text" class="form-control" v-model="balance" disabled>
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
        
        <!-- Information MODAL -->
        <ModalComponent :id="modalIdInfo" :title="modalTitle" :size="modalSizeFinal" :position="modalPosition">
            <template #modalHeader>
                <div class="m-auto">
                    <h4>Genesis Hardware: General Construction Supply</h4>
                </div>
            </template>
            <template #modalBody>
                <div>
                    <p>Welcome to Genesis Hardware: Your Partner in Building Excellence</p>
                    <p>Genesis Hardware: General Construction Supply is your premier destination for all your construction and building material needs. With a commitment to quality, reliability, and customer satisfaction, we have earned a reputation as a trusted provider of construction supplies and solutions.</p>
                    <p>Why Choose Genesis Hardware?</p>
                    <ol>
                        <li>Quality Products</li>
                        <li>Expert Guidance</li>
                        <li>Competitive Pricing</li>
                        <li>Convenience and Efficiency</li>
                        <li>Customer Satisfaction</li>
                    </ol>
                    <p>Experience the Genesis Hardware Difference</p>
                    <p>Whether you're embarking on a new construction project, renovating your home, or tackling a DIY project, Genesis Hardware: General Construction Supply is here to support you every step of the way. With our quality products, expert guidance, competitive pricing, and exceptional service, we're your partner in building excellence. Contact us today to experience the Genesis Hardware difference for yourself!</p>
                
                    <div><i class="fas fa-map-marker-alt"></i> Location: <b>217-C General Kalentong, Daang Bakal, Mandaluyong, 1550 Metro Manila</b> </div>
                </div>
            </template>
            <template #modalFooter>
                <div class="text-right">
                    <button style="margin-left: 5px;" class="btn btn-dark" v-on:click="noProduct">Close</button>
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
                categories : [],
                units : [],
                searchData : '',
                requestData : [],
                countRequestProduct : [],
                selectedBrand : '',
                selectedTool : '',
                priceBelow : '',
                selectedSpecs : '',
                agreementChecked: false,
                suppliers : [],
                balance: 0,
                imageData: null,
                requestedItems: 0,
                total: 0,
                columns : ['name', 'specifications' ,'image', 'stocks', 'supplier_name', 'price' ,'action'],
                errors: [],
                options : {
                    headings : {
                        name : 'Product Name', 
                        specifications : 'Specifications',
                        image : 'Image',
                        supplier_name: 'Supplier',
                        price: 'Price',
                        action : 'Action',
                    },
                    filterable: false,
                    sortable: []
                },
                dataValues: {
                    name: '',
                },
                modalId : 'modal-request-product',
                modalTitle : 'Request a Product',
                modalPosition: 'modal-dialog-centered',
                modalSize : 'modal-md',

                modalIdFinal : 'modal-final-product',
                modalSizeFinal : 'modal-lg',

                modalIdInfo : 'modal-info',
        }
    },
    components: {
        FormComponent,
        ModalComponent,
        BreadCrumbComponent,
    },
    methods: {
        onFileChange(event) {
            const file = event.target.files[0];
            if (file) {
                this.imageData = file;
            }
        },
        addClicked(){
            $('#' + this.modalId).modal('show');
        },
        refresh(){
            window.location.reload();
        },
        information(){
            $('#' + this.modalIdInfo).modal('show');
        },
        filterData(){
            if (!this.selectedBrand || !this.selectedTool || !this.priceBelow || !this.selectedSpecs) {
                    Swal.fire({
                        title: "Please select all fields!",
                        icon: 'warning',
                        timer: 3000
                    });
                    return;
                }

                const searchData = {
                    brand: this.selectedBrand,
                    tool: this.selectedTool,
                    price: this.priceBelow,
                    specs: this.selectedSpecs
                };

                axios.post('/supplier3/filterData', searchData)
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
        requestProduct(product){
            axios.get('/supplier3/edit/' + product.id).then(response => {
                this.dataValues = response.data.data;
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
        calculateTotal() {
            this.total = this.requestedItems * this.dataValues.price;
        },
        yesProduct(){
            if(this.requestedItems == 0){
                Swal.fire({
                    title: "Insufficient Stocks!",
                    icon: 'warning',
                    timer: 3000
                });
                return;
            }
            if (this.requestedItems < this.dataValues.stocks) {
                const requestData = {
                    requestedQuantity: this.requestedItems,
                    requestedId: this.dataValues.id,
                    total: this.total
                };

                this.requestData = requestData;
                $('#' + this.modalIdFinal).modal('show');
                $('#' + this.modalId).modal('hide');
            } 
            else if (this.requestedItems > this.dataValues.stocks) {
                Swal.fire({
                    title: "Insufficient Stocks!",
                    icon: 'warning',
                    timer: 3000
                });
            }
            else if (this.total > this.balance) {
                Swal.fire({
                    title: "Insufficient Balance!",
                    icon: 'warning',
                    timer: 3000
                });
            }
        },
        noProduct(){
            $('#' + this.modalId).modal('hide');
            $('#' + this.modalIdFinal).modal('hide');
            $('#' + this.modalIdInfo).modal('hide');
        },
        getProduct(){
            this.requestedItems = 0;
            this.total = 0;
            this.agreementChecked = false;
            axios.post('/supplier3/requestproduct', this.requestData)
                    .then(response => {
                        this.dataValues.stocks -= this.requestedItems;
                        this.balance -= this.total;
                        Swal.fire({
                            title: "Request Product Successfully!",
                            icon: 'success',
                            timer: 3000
                        });
                        $('#' + this.modalId).modal('hide');
                        $('#' + this.modalIdFinal).modal('hide');
                        window.location.reload();
                    })
                    .catch(error => {
                        // Handle errors from the backend
                        console.error('Error requesting product:', error);
                    });
        },
        getData() {
            axios.get('/supplier3/show').then(response => {
                this.data = response.data.data;
                this.suppliers = response.data.suppliers;
                this.balance = response.data.balance;
            })
        },
        clearInputs() {
            this.dataValues = {
                name: '',
            }
            this.imageData = null;
            this.errors = [];
        },
        editClicked(props) {
            this.modalTitle = 'Edit Data';

            axios.get('/supplier3/edit/' + props.data.id).then(response => {
                this.dataValues = response.data.data;
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
                    axios.get('/supplier3/destroy/' + props.data.id).then(response => {
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
            const formData = new FormData();
            // Check if id is present and not null before appending it to the form data
            if (this.dataValues.id !== null && this.dataValues.id !== undefined) {
                formData.append('id', this.dataValues.id);
            }
            formData.append('name', this.dataValues.name);
            formData.append('specifications', this.dataValues.specifications);
            formData.append('image', this.dataValues.image);
            formData.append('stocks', this.dataValues.stocks);
            formData.append('supplier_id', this.dataValues.supplier_id);
            formData.append('price', this.dataValues.price);

            if (this.imageData) {
                formData.append('image', this.imageData);
            }
                axios.post('/supplier3/store', formData, {
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
                }).catch(errors => {
                    this.errors = errors.response.data.errors;
                });
            },
        },
        mounted() {
            this.getData();
        }
        
}

</script>
