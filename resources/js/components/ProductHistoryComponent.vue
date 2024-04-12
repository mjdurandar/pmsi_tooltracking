<template>
    <div class="p-3">
        <BreadCrumbComponent tab_title="Product History"></BreadCrumbComponent>
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
                <select class="form-control" v-model="supplier_id">
                    <option value="" disabled selected>Select Supplier</option>
                    <option v-for="supplier in suppliers" :value="supplier.id">{{ supplier.name }}</option>
                </select>  
            </div>
            <!-- <div class="col-lg-2">
                <select class="form-control" v-model="status_id">
                    <option value="" disabled selected>Select Status</option>
                    <option value="Unreleased">Unreleased</option>
                    <option value="Selling">Selling</option>
                    <option value="Borrowing">Borrowing</option>
                </select>  
            </div> -->
            <div class="col-lg-2">
                <select class="form-control" v-model="status_id">
                    <option value="" disabled selected>Select Status</option>
                    <option v-for="status in statuses" :value="status.id">{{ status.name }}</option>
                </select>  
            </div>
            <div>
                <button class="btn btn-primary" @click="filterData">Search</button>
                <button class="btn btn-success ml-1" @click="refresh"><i class="fas fa-sync-alt"></i></button>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <FormComponent 
                            :data="data"
                            :columns="columns"
                            :options="options"
                            :option1Switch="false"
                            :option2Switch="false"
                            :option3Switch="true"
                            :addButton="false"
                            option3Name="View"
                            option3Icon="fa-regular fa-eye"
                            @optionalClicked="optionalClicked"
                        >
                        </FormComponent>
                    </div>
                </div>
            </div>
        </div>

        <!-- MODAL RELEASED PRODUCT -->
        <ModalComponent :id="modalId" :title="modalTitle" :size="modalSize" :position="modalPosition">
            <template #modalHeader>
                <div class="m-auto">
                    <h4>View History</h4>
                </div>
            </template>
            <template #modalBody>
                <div class="row">
                    <div class="col-12">
                        <label for="">Name</label>
                        <input type="text" class="form-control" v-model="dataValues.tools_and_equipment_name" disabled>
                    </div> 
                    <div class="col-12">
                        <label for="">Product Code</label>
                        <input type="text" class="form-control" v-model="dataValues.product_code" disabled>
                    </div> 
                    <div class="col-12">
                        <label for="">Status</label>
                        <input type="text" class="form-control" v-model="dataValues.status" disabled>
                    </div> 
                    <div class="col-12">
                        <label for="">Released At</label>
                        <input type="text" class="form-control" v-model="dataValues.created_at" disabled>
                    </div>
                </div>
            </template>
            <template #modalFooter>
                <div class="text-right">
                    <button class="btn btn-dark" v-on:click="dismissModal()">Close</button>
                </div>
            </template>
        </ModalComponent>


    </div>
</template>

<style>
    .dash-title{
        font-size: 20px;
        font-weight: bold;
        text-align: center;
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
                selectedBrand: '',
                selectedTool: '',
                status_id: '',
                supplier_id: '',
                statuses: [],
                suppliers: [],
                columns : ['product_code', 'tools_and_equipment_name', 'supplier_name' ,'status' ,'action'],
                options : {
                    headings : {
                        product_code : 'Product Code',
                        tools_and_equipment_name : 'Name',
                        supplier_name : 'Supplier',
                        status: 'Status',
                        action : 'Action',
                    },
                    filterable: false,
                    sortable: []
                },
                dataValues: {
                    name: '',
                },
                modalId : 'modal-view',
                modalTitle : 'View',
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
        dismissModal(){
            $('#' + this.modalId).modal('hide');
            $('#' + this.modalIdCancel).modal('hide');
        },
        getData() {
            axios.get('/product-history/show').then(response => {
                this.data = response.data.data;
                this.suppliers = response.data.suppliers;
                this.statuses = response.data.statuses;
                this.data.forEach(item => {
                    item.created_at = new Date(item.created_at).toLocaleString(); // Format to the user's locale
                })
            })
        },
        clearInputs() {
            this.dataValues = {
                name: '',
            }
            this.errors = [];
        },
        optionalClicked(props){
            this.dataValues = props.data;
            $('#' + this.modalId).modal('show');
        },
        filterData(){

            const searchData = {
                    brand: this.selectedBrand,
                    tool: this.selectedTool,
                    supplier_id: this.supplier_id,
                    status_id: this.status_id
                };

            axios.post('/product-history/viewHistory', searchData)
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
        }
    },
    mounted() {
            this.getData();
        }
}

</script>
