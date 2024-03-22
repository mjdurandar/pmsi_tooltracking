<template>
    <div class="p-3">
        <BreadCrumbComponent tab_title="Tools and Equipment"></BreadCrumbComponent>
        <!-- <div class="card">
            <div class="card-body">
                <FormComponent 
                    :data="data"
                    :columns="columns"
                    :options="options"
                    btnName="Add Tools and Equipment"
                    :option3Switch="true"
                    option3Name="View Image"
                    option3Icon="fa-regular fa-image"
                    @deleteClicked="deleteClicked"
                    @editClicked="editClicked"
                    @addClicked="addClicked"
                    @optionalClicked="optionalClicked"
                >
                </FormComponent>
            </div>
        </div> -->

        <!-- <ModalComponent :id="modalId" :title="modalTitle" :size="modalSize" :position="modalPosition">
            <template #modalHeader>
                <div class="m-auto">
                    <h4>Add Tools and Equipment</h4>
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
                        <label for="">Category</label>
                        <select class="form-control" v-model="dataValues.category_id">
                            <option v-for="category in categories" :value="category.id">{{ category.name }}</option>
                        </select>  
                        <div class="text-danger" v-if="errors.category">{{ errors.category[0] }}</div>
                    </div>  
                    <div class="col-12">
                        <label for="">Unit</label>
                        <select class="form-control" v-model="dataValues.unit_id">
                            <option v-for="unit in units" :value="unit.id">{{ unit.name }}</option>
                        </select>  
                        <div class="text-danger" v-if="errors.unit">{{ errors.unit[0] }}</div>
                    </div>  
                    <div class="col-12">
                        <label for="">Supplier</label>
                        <select class="form-control" v-model="dataValues.supplier_id">
                            <option v-for="supplier in suppliers" :value="supplier.id">{{ supplier.name }}</option>
                        </select>
                        <div class="text-danger" v-if="errors.supplier">{{ errors.supplier[0] }}</div>
                    </div> 
                    <div class="col-12">
                        <label for="">Price</label>
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

        <!-- VIEW IMAGE MODAL -->
        <!-- <ModalComponent :id="modalIdImage" :title="modalTitle" :size="modalSize" :position="modalPosition">
            <template #modalBody>
                <div class="row">
                    <div class="col-12" v-if="dataValues.image">
                        <img :src="'/images/' + dataValues.image" alt="Current Image" class="img-fluid">
                    </div>
                </div>
            </template>
            <template #modalFooter>
                <div class="text-right">
                    <button class="btn btn-dark" data-bs-dismiss="modal">Close</button>
                </div>
            </template>
        </ModalComponent> -->

        <div class="d-flex mb-3 justify-content-between">
            <div class="d-flex">
                <select class="form-control" v-model="dataValues.category_id">
                    <option value="" disabled selected>Select a category...</option>
                    <option v-for="category in categories" :value="category.id">{{ category.name }}</option>
                </select> 
                <button class="btn btn-primary" @click="searchCategory()" style="margin-left:5px">Search</button>
            </div>
            <div class="d-flex">
                <input type="text" class="form-control" placeholder="Search Product Code..." v-model="searchData">
                <button class="btn btn-primary" @click="searchProductCode()" style="margin-left:5px">Search</button>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4" v-for="(product, index) in data" :key="index">
                <div class="card" :class="{ 'borrowed-card': product.category_name === 'Borrowing' || product.category_name === 'Selling' }">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div style="width: 50%;">
                                <h2 class="card-title" style="font-weight: bold;">{{ product.name }}</h2>
                                <h4 class="card-text">{{ product.product_code }}</h4>
                                <h6 class="card-text">{{ product.category_name }}</h6>
                            </div>
                            <div style="width: 50%;">
                                <img v-if="product.image" :src="'/images/' + product.image" alt="Product Image" class="img-fluid" style="height: 250px;">
                                <p v-else>No Stocks</p>
                            </div>
                        </div>
                        <button class="btn btn-success" :class="{ 'btn-danger': product.category_name === 'Borrowing' || product.category_name === 'Selling' }" 
                        @click="product.is_approved === 1 ? cancelProduct(product) : releaseProduct(product)">
                            {{ product.category_name === 'Borrowing' || product.category_name === 'Selling' ? 'Cancel' : 'Release Product' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- RELEASE PRODUCT MODAL -->
        <ModalComponent :id="modalId" :title="modalTitle" :size="modalSize" :position="modalPosition">
            <template #modalHeader>
                <div class="m-auto">
                    <h4>Release Product</h4>
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
                        <label for="">Product Code</label>
                        <input type="text" class="form-control" v-model="dataValues.product_code" disabled>
                    </div> 
                    <div class="col-12">
                        <label for="">Category</label>
                        <select class="form-control" v-model="category_id">
                            <option v-for="category in filteredCategories" :value="category.id">{{ category.name }}</option>
                        </select>  
                        <div class="text-danger" v-if="errors.category">{{ errors.category[0] }}</div>
                    </div>
                    <div class="col-12">
                        <label for="">Supplier</label>
                        <input type="text" class="form-control" v-model="dataValues.supplier_name" disabled>
                    </div> 
                    <div class="col-12" v-if="category_id === 2">
                        <label for="">Set Price for Selling</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">₱</span>
                            </div>
                            <input type="text" class="form-control" v-model="selectedPrice">
                        </div>
                    </div>  
                    <div class="col-12" v-if="category_id === 3">
                        <label for="">Set Price for Borrowing</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">₱</span>
                            </div>
                            <input type="text" class="form-control" v-model="selectedPrice">
                        </div>
                    </div>  
                </div>
            </template>
            <template #modalFooter>
                <div class="text-right">
                    <button class="btn btn-success" v-on:click="reviewProduct(props)">Review</button>
                </div>
            </template>
        </ModalComponent>

        <!-- BORROW MODAL -->
        <ModalComponent :id="modalIdBorrow" :title="modalTitle" :size="modalSize" :position="modalPosition">
            <template #modalHeader>
                <div class="m-auto">
                    <h4>Review Product</h4>
                </div>
            </template>
            <template #modalBody>
                <div>
                    <h5>Checklist:</h5>
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="check1" v-model="isChecked1">
                        <label class="form-check-label" for="check1">Inspect the product thoroughly to ensure it's in good condition and functions properly. </label>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="check2" v-model="isChecked2">
                        <label class="form-check-label" for="check2">Check for any damages, defects, or missing parts.</label>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="check3" v-model="isChecked3">
                        <label class="form-check-label" for="check3">Clean and prepare the product to be presentable and usable for the borrower.</label>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="check4" v-model="isChecked4">
                        <label class="form-check-label" for="check3">Gather all necessary documentation related to the product, such as manuals, warranties, or user guides.</label>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="check5" v-model="isChecked5">
                        <label class="form-check-label" for="check3">Ensure that all documentation is complete and up-to-date.</label>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="check6" v-model="isChecked6">
                        <label class="form-check-label" for="check3">Provide clear instructions on how to use the product safely and effectively.</label>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="check7" v-model="isChecked7">
                        <label class="form-check-label" for="check3">Record the product details, including serial numbers and any identifying information.</label>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="check8" v-model="isChecked8">
                        <label class="form-check-label" for="check3">Update the inventory system to reflect the product's availability for borrowing.</label>
                    </div>
                </div>
            </template>
            <template #modalFooter>
                <div class="text-right">
                    <button class="btn btn-success" v-on:click="approveBorrowed(props)" :disabled="!allCheckboxesChecked">Approve</button>
                </div>
            </template>
        </ModalComponent>

        <!-- WHY CANCEL MODAL -->
        <ModalComponent :id="modalIdDescription" :title="modalTitle" :size="modalSize" :position="modalPosition">
            <template #modalBody>
                <div class="row">
                    <div class="col-12">
                        <label for="">Why are we cancelling this Product?</label>
                        <textarea class="form-control" v-model="cancelExplanation"></textarea>
                    </div> 
                </div>
            </template>
            <template #modalFooter>
                <div class="text-right">
                    <button class="btn btn-success" @click="cancelDescription(product)">Submit</button>
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
                categories : [],
                category: '',
                errors: '',
                searchData: '', 
                cancelExplanation: '',
                productData : [],
                units: [],
                suppliers: [],
                category_id: '',
                selectedPrice : 0,
                isApproved: false,
                isChecked1 : false,
                isChecked2 : false,
                isChecked3 : false,
                isChecked4 : false,
                isChecked5 : false,
                isChecked6 : false,
                isChecked7 : false,
                isChecked8 : false,
                dataValues: {
                    name: '',
                    category_id: '',
                },
                modalId : 'modal-powertools',
                modalTitle : 'Power Tools',
                modalPosition: 'modal-dialog-centered',
                modalSize : 'modal-md',

                modalIdImage : 'modal-image',
                modalIdBorrow : 'modal-borrow',

                modalIdDescription : 'modal-description',
        }
    },
    components: {
        FormComponent,
        ModalComponent,
        BreadCrumbComponent,
    },
    computed: {
        allCheckboxesChecked() {
            return this.isChecked1 && this.isChecked2 && this.isChecked3 &&
                   this.isChecked4 && this.isChecked5 && this.isChecked6 &&
                   this.isChecked7 && this.isChecked8;
        },
        filteredCategories() {
            // Filter out the 'Unreleased' category if product's category is already 'Unreleased'
            return this.categories.filter(category => {
                return !(this.dataValues.category_name === 'Unreleased' && category.name === 'Unreleased');
            });
        }
    },
    methods: {
        onFileChange(event) {
            const file = event.target.files[0];
            if (file) {
                this.imageData = file;
            }
        },
        addClicked(props){
            $('#' + this.modalId).modal('show');
            this.clearInputs();
        },
        releaseProduct(product){
            this.category_id = '';
            this.selectedPrice = 0;
            axios.get('/powertools/releaseProduct/' + product.id).then(response => {
                this.dataValues = response.data.data;
                $('#' + this.modalId).modal('show');
                if(this.dataValues.category_id == 1){
                    this.modalTitle = 'Release Product';
                }
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
        cancelProduct(product){
            this.productData = product;
            Swal.fire({
                title: 'Are you sure you want to Cancel the Product?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, do it!'
            }).then((result) => {
            if (result.isConfirmed) {
                $('#' + this.modalIdDescription).modal('show');
            }
            });
        },
        cancelDescription(){
            const requestData = {
                id: this.productData.id,
                description: this.cancelExplanation,
            };
            axios.post('/powertools/cancelProduct', requestData)
                .then(response => {
                    Swal.fire({
                        title: 'Success',
                        text: 'Product Cancelled',
                        icon: 'success',
                        timer: 3000
                    });
                    this.getData();
                    $('#' + this.modalIdDescription).modal('hide');
                })
                .catch(error => {
                    console.error('Error requesting product:', error);
                });
        },
        approveBorrowed(){
            this.isApproved = true;
            const requestData = {
                id: this.dataValues.id,
                selectedCategory: this.category_id,
                selectedPrice: this.selectedPrice
            };
            axios.post('/powertools/updateProduct', requestData)
                .then(response => {
                    Swal.fire({
                        title: 'Success',
                        text: 'Product Approved',
                        icon: 'success',
                        timer: 3000
                    });
                    $('#' + this.modalId).modal('hide');
                    $('#' + this.modalIdBorrow).modal('hide');
                    this.getData();
                })
            .catch(error => {
                // Handle errors from the backend
                console.error('Error requesting product:', error);
            });
        },
        reviewProduct(){
            this.isChecked1 = false;
            this.isChecked2 = false;
            this.isChecked3 = false;
            this.isChecked4 = false;
            this.isChecked5 = false;
            this.isChecked6 = false;
            this.isChecked7 = false;
            this.isChecked8 = false;
            if(this.category_id == null || this.category_id == '' ){
                Swal.fire({
                    title: 'Warning',
                    text: 'Please select a Category',
                    icon: 'warning',
                    timer: 3000
                });
            }
            if(this.category_id == 1){
                const requestData = {
                    id: this.dataValues.id,
                    selectedCategory: this.category_id,
                    selectedPrice: this.selectedPrice
                };
                axios.post('/powertools/updateProduct', requestData)
                    .then(response => {
                        Swal.fire({
                            title: 'Success',
                            text: 'Product set to Unreleased',
                            icon: 'success',
                            timer: 3000
                        });
                        this.getData();
                        $('#' + this.modalId).modal('hide');
                    })
                    .catch(error => {
                        // Handle errors from the backend
                        console.error('Error requesting product:', error);
                    });
                $('#' + this.modalId).modal('hide');
            }
            if(this.category_id == 2 || this.category_id == 3){
                if(this.selectedPrice == null || this.selectedPrice == '' ){
                    Swal.fire({
                        title: 'Warning',
                        text: 'Please input a Price',
                        icon: 'warning',
                        timer: 3000
                    });
                }
                if(this.selectedPrice){
                    $('#' + this.modalIdBorrow).modal('show');
                }
            }
        },
        searchCategory() {
            if (this.dataValues.category_id) {
                axios.post('/powertools/searchCategory', {
                    category_id: this.dataValues.category_id,
                    product_code: this.searchData 
                })
                .then(response => {
                    this.data = response.data.data;
                })
                .catch(error => {
                    console.error('Error searching by category:', error);
                });
            }
        },
        searchProductCode() {
            if (this.searchData) {
                axios.post('/powertools/searchProductCode', {
                    category_id: this.dataValues.category_id, // Include the selected category ID
                    product_code: this.searchData // Include the product code to search
                })
                .then(response => {
                    this.data = response.data.data;
                })
                .catch(error => {
                    console.error('Error searching by product code:', error);
                });
            }
        },
        getData() {
            axios.get('/powertools/show').then(response => {
                this.data = response.data.data;
                this.categories = response.data.categories;
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
                    axios.get('/powertools/destroy/' + props.data.id).then(response => {
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
            formData.append('quantity', this.dataValues.quantity);
            formData.append('price', this.dataValues.price);
            formData.append('category_id', this.dataValues.category_id);
            formData.append('unit_id', this.dataValues.unit_id);
            formData.append('supplier_id', this.dataValues.supplier_id);
            formData.append('total', this.dataValues.total);

            if (this.imageData) {
                formData.append('image', this.imageData);
            }
                axios.post('/powertools/store', formData, {
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
