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
                    <option v-for="category in categories" :value="category.id">{{ category.name }}</option>
                </select> 
                <button class="btn btn-primary" @click="searchCategory()">Search</button>
            </div>
            <div class="d-flex">
                <input type="text" class="form-control" placeholder="Search Product Code..." v-model="searchData">
                <button class="btn btn-primary" @click="searchProductCode()">Search</button>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4" v-for="(product, index) in data" :key="index">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div style="width: 50%;">
                                <h2 class="card-title" style="font-weight: bold;">{{ product.name }}</h2>
                                <h4 class="card-text">{{ product.product_code }}</h4>
                                <h6 class="card-text">{{ product.category_name }}</h6>
                            </div>
                            <div style="width: 50%;">
                                <img v-if="product.image" :src="'/images/' + product.image" alt="Product Image" class="img-fluid">
                                <p v-else>No Stocks</p>
                            </div>
                        </div>
                        <button class="btn btn-success" @click="releaseProduct(product)">Release Product</button>
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
                        <div class="col-12" v-if="dataValues.image">
                            <label for="currentImage">Image</label>
                            <img :src="'/images/' + dataValues.image" alt="Current Image" class="img-fluid">
                        </div>
                        <div class="col-12">
                            <label for="">Category</label>
                            <select class="form-control" v-model="category_id">
                                <option v-for="category in categories" :value="category.id">{{ category.name }}</option>
                            </select>  
                            <div class="text-danger" v-if="errors.category">{{ errors.category[0] }}</div>
                        </div>  
                        <div class="col-12">
                            <label for="">Supplier</label>
                            <input type="text" class="form-control" v-model="dataValues.supplier_name" disabled>
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
                        <button class="btn btn-success" v-on:click="storeData">Review</button>
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
                category: '',
                errors: '',
                searchData: '', 
                units: [],
                suppliers: [],
                category_id: '',
                dataValues: {
                    name: '',
                    category_id: 1,
                },
                modalId : 'modal-powertools',
                modalTitle : 'Power Tools',
                modalPosition: 'modal-dialog-centered',
                modalSize : 'modal-md',

                modalIdImage : 'modal-image'
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
        addClicked(props){
            $('#' + this.modalId).modal('show');
            this.clearInputs();
        },
        releaseProduct(product){
            axios.get('/powertools/releaseProduct/' + product.id).then(response => {
                this.dataValues = response.data.data;
                console.log(this.dataValues);
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
        searchCategory() {
            if (this.dataValues.category_id) {
                axios.post('/powertools/searchCategory', {
                    category_id: this.dataValues.category_id,
                    product_code: this.searchData // Assuming searchData contains the product code to search
                })
                .then(response => {
                    this.data = response.data.data;
                })
                .catch(error => {
                    console.error('Error searching by category:', error);
                });
            } else {
                this.getData(); // Or any other fallback action you want
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
            } else {
                this.getData(); // Or any other fallback action you want
            }
        },
        getData() {
            axios.get('/powertools/show').then(response => {
                this.data = response.data.data;
                this.categories = response.data.categories;
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

            axios.get('/powertools/edit/' + props.data.id).then(response => {
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
