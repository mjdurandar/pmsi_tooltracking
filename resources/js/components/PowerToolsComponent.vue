<template>
    <div class="p-3">
        <BreadCrumbComponent tab_title="Power Tools"></BreadCrumbComponent>
        <div class="card">
            <div class="card-body">
                <FormComponent 
                    :data="data"
                    :columns="columns"
                    :options="options"
                    btnName="Add Power Tools"
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
                    <h4>Add Power Tools</h4>
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
                suppliers : [],
                imageData: null,
                columns : ['name', 'image' ,'product_code', 'category_name', 'unit_name', 'supplier_name', 'price' ,'action'],
                errors: [],
                options : {
                    headings : {
                        name : 'Unit',
                        image : 'Image',
                        product_code: 'Product Code',
                        category_name: 'Category',
                        unit_name: 'Unit',
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
                modalId : 'modal-powertools',
                modalTitle : 'Power Tools',
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
        getData() {
            axios.get('/powertools/show').then(response => {
                this.data = response.data.data;
                this.categories = response.data.categories;
                this.units = response.data.units;
                this.suppliers = response.data.suppliers;
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
