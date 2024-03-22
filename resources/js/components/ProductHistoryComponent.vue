<template>
    <div class="p-3">
        <BreadCrumbComponent tab_title="Product History"></BreadCrumbComponent>
        <div class="row">
            <div class="col-lg-6 text-center">
                <BreadCrumbComponent tab_title="Released"></BreadCrumbComponent>
            </div>
            <div class="col-lg-6 text-center">
                <BreadCrumbComponent tab_title="Cancelled"></BreadCrumbComponent>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex mb-3" style="width: 50%;">
                                <input type="text" class="form-control" placeholder="Search Released History..." v-model="releasedData">
                                <button class="btn btn-primary" style="margin-left: 5px;" @click="releasedSearch()">Search</button>
                        </div>
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
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex mb-3" style="width: 50%;">
                                <input type="text" class="form-control" placeholder="Search Cancelled History..." v-model="cancelledData">
                                <button class="btn btn-primary" style="margin-left: 5px;" @click="cancelledSearch()">Search</button>
                        </div>
                        <FormComponent 
                            :data="dataCancelled"
                            :columns="columnsCancelled"
                            :options="optionsCancel"
                            :option1Switch="false"
                            :option2Switch="false"
                            :option3Switch="true"
                            :addButton="false"
                            option3Name="View"
                            option3Icon="fa-regular fa-eye"
                            @optionalClicked="optionalClicked2"
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

        <!-- MODAL CANCEL PRODUCT -->
        <ModalComponent :id="modalIdCancel" :title="modalTitle" :size="modalSize" :position="modalPosition">
            <template #modalHeader>
                <div class="m-auto">
                    <h4>View History</h4>
                </div>
            </template>
            <template #modalBody>
                <div class="row">
                    <div class="col-12">
                        <label for="">Name</label>
                        <input type="text" class="form-control" v-model="dataCancelValues.tools_and_equipment_name" disabled>
                    </div> 
                    <div class="col-12">
                        <label for="">Product Code</label>
                        <input type="text" class="form-control" v-model="dataCancelValues.product_code" disabled>
                    </div> 
                    <div class="col-12">
                        <label for="">Status</label>
                        <input type="text" class="form-control" v-model="dataCancelValues.status" disabled>
                    </div> 
                    <div class="col-12">
                        <label for="">Why we Cancelled?</label>
                        <textarea type="text" class="form-control" v-model="dataCancelValues.description" disabled></textarea>
                    </div> 
                    <div class="col-12">
                        <label for="">Cancelled At</label>
                        <input type="text" class="form-control" v-model="dataCancelValues.created_at" disabled>
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
                dataCancelled : [],
                releasedData : '',
                cancelledData : '',
                columns : ['product_code', 'status' ,'created_at' ,'action'],
                columnsCancelled : ['product_code', 'status' ,'created_at' ,'action'],
                errors: [],
                optionsCancel : {
                    headings : {
                        product_code : 'Product Code',
                        status: 'Status',
                        created_at : 'Cancelled At',
                        action : 'Action',
                    },
                    filterable: false,
                    sortable: []
                },
                options : {
                    headings : {
                        product_code : 'Product Code',
                        status: 'Status',
                        created_at : 'Released At',
                        action : 'Action',
                    },
                    filterable: false,
                    sortable: []
                },
                dataValues: {
                    name: '',
                },
                dataCancelValues: {
                    name: '',
                },
                modalId : 'modal-view',
                modalIdCancel : 'modal-view-cancel',
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
                this.dataCancelled = response.data.dataCancelled;
                this.data.forEach(item => {
                    item.created_at = new Date(item.created_at).toLocaleString(); // Format to the user's locale
                })
                this.dataCancelled.forEach(item => {
                    item.created_at = new Date(item.created_at).toLocaleString(); // Format to the user's locale
                })
            })
        },
        releasedSearch(){
                axios.post('/product-history/releasedSearch', {
                    releasedData: this.releasedData, 
                })
                .then(response => {
                    this.data = response.data.data;
                    this.data.forEach(item => {
                        item.created_at = new Date(item.created_at).toLocaleString(); // Format to the user's locale
                    })
                })
                .catch(error => {
                    console.error('Error searching by product code:', error);
                });
        },
        cancelledSearch(){
                axios.post('/product-history/cancelledSearch', {
                    cancelledData: this.cancelledData, 
                })
                .then(response => {
                    this.dataCancelled = response.data.dataCancelled;
                    this.dataCancelled.forEach(item => {
                        item.created_at = new Date(item.created_at).toLocaleString(); // Format to the user's locale
                    })
                })
                .catch(error => {
                    console.error('Error searching by product code:', error);
                });
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
        optionalClicked2(props){
            this.dataCancelValues = props.data;
            $('#' + this.modalIdCancel).modal('show');
        }
    },
    mounted() {
            this.getData();
        }
}

</script>
