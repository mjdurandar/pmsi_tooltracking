<template>
    <div class="p-3">
        <BreadCrumbComponent tab_title="Borrowed Tools"></BreadCrumbComponent>
        <div class="d-flex justify-content-center">
            <img src="images\under_maintenance.png" alt="Under Maintenance" style="text-align: center;"> 
        </div>
        <!-- <div class="card">
            <div class="card-body">
                <FormComponent 
                    :data="data"
                    :columns="columns"
                    :options="options"
                    :option3Switch="true"
                    option3Name="View Image"
                    option3Icon="fa-regular fa-image"
                    btnName="Add Borrowed Tools"
                    @deleteClicked="deleteClicked"
                    @editClicked="editClicked"
                    @addClicked="addClicked"
                    @optionalClicked="optionalClicked"
                >
                </FormComponent>
            </div>
        </div>

        <ModalComponent :id="modalId" :title="modalTitle" :size="modalSize" :position="modalPosition">
            <template #modalHeader>
                <div class="m-auto">
                    <h4>Add Borrowed Tools</h4>
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
                        <label for="">Project Site</label>
                        <select class="form-control" v-model="dataValues.project_site_id">
                            <option v-for="project in projects" :value="project.id">{{ project.name }}</option>
                        </select>
                        <div class="text-danger" v-if="errors.project">{{ errors.project[0] }}</div>
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
                errors: [],
                categories: [],
                units: [],
                suppliers: [],
                projects: [],
                imageData: null,
                columns : ['category_name', 'product_code', 'name' ,'image', 'unit_name', 'supplier_name', 'project_site_name', 'price' ,'action'],
                errors: [],
                options : {
                    headings : {
                        category_name: 'Category',
                        product_code : 'Product Code',
                        name : 'Name',
                        image : 'Image',
                        unit_name: 'Unit',
                        supplier_name: 'Supplier',
                        project_site_name: 'Project Site',
                        price: 'Price',
                        action : 'Action',
                    },
                    filterable: true,
                    sortable: []
                },
                dataValues: {
                    name: '',
                },
                modalId : 'modal-scaffolding',
                modalTitle : 'Scaffolding',
                modalPosition: 'modal-dialog-centered',
                modalSize : 'modal-md',

                modalIdImage : 'modal-image'
        }
    },
    components: {
        FormComponent,
        ModalComponent,
        BreadCrumbComponent
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
        optionalClicked(props){
            axios.get('/scaffolding/edit/' + props.data.id).then(response => {
                this.dataValues = response.data.data;
                $('#' + this.modalIdImage).modal('show');
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
        getData() {
            axios.get('/scaffolding/show').then(response => {
                this.data = response.data.data;
                this.categories = response.data.categories;
                this.units = response.data.units;
                this.suppliers = response.data.suppliers;
                this.projects = response.data.projects;
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
            this.dataValues = props.data;
            this.modalTitle= 'Edit Data';

            axios.get('/scaffolding/edit/' + this.dataValues.id).then(response => {
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
                    axios.get('/scaffolding/destroy/' + props.data.id).then(response => {
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
            formData.append('project_site_id', this.dataValues.project_site_id);
            formData.append('total', this.dataValues.total)

            if (this.imageData) {
                formData.append('image', this.imageData);
            }
                axios.post('/scaffolding/store', formData, {
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
