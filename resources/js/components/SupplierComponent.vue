<style scoped> 
    .form-spacing-test{
        padding: 15px 15px 0px 15px;
    }
    .supplier1{
        font-weight: 800;
        border: 1px solid #f18f4e;
        border-radius: 8px;
        padding: 12px;
        letter-spacing: 1px;
        cursor: pointer;
    }
    .supplier2{
        font-weight: 800;
        border: 1px solid  #f18f4e;
        border-radius: 8px;
        padding: 12px;
        letter-spacing: 1px;
        cursor: pointer;
    }
    .supplier3{
        font-weight: 800;
        border: 1px solid #f18f4e;
        border-radius: 8px;
        padding: 12px;
        letter-spacing: 1px;
        cursor: pointer;
    }

    .spacing{
        padding: 0px 10px 10px 0px;
    }
    
    .spacing div:hover{
        background-color: #f18f4e;
        color: #fff;
    }

    .active[data-v-e490b594]{
        background: #f18f4e !important; 
        color: #fff !important;
    }
    .title-design{
        color: #2D2D2D;
        font-weight: 800;
        font-size: 25px;
        padding: 0px 0px 10px 0px;
 
    }

</style>

<template>
    <div class="p-3">
        <div class="card">
            <div>
                <div class="form-spacing-test">
                    <div class="tab-cho d-flex">
                        <div class="spacing">
                            <div v-on:click="onClickSelection(1)" class="supplier1" :class="pageSelected === 1 ? 'active' : '' ">Twinbar Metal Industries</div>
                        </div>
                        <!-- <div class="spacing">
                            <div v-on:click="onClickSelection(2)" class="supplier2" :class="pageSelected === 2 ? 'active' : '' ">Supplier 2</div>
                        </div>
                        <div class="spacing">
                            <div v-on:click="onClickSelection(3)" class="supplier3" :class="pageSelected === 3 ? 'active' : '' ">Supplier 3</div>
                        </div> -->
                    </div>
                    <!-- <request-product-component v-if="pageSelected === 1"></request-product-component> -->
                    <supplier1-component v-if="pageSelected === 1"></supplier1-component>
                    <!-- <supplier2-component v-if="pageSelected === 2"></supplier2-component>
                    <supplier3-component v-if="pageSelected === 3"></supplier3-component> -->
                </div>
            </div>
        </div>
<!-- 
        <ModalComponent :id="modalId" :title="modalTitle" :size="modalSize" :position="modalPosition">
            <template #modalHeader>
                <div class="m-auto">
                    <h4>Add Supplier</h4>
                </div>
            </template>
            <template #modalBody>
                <div class="row">
                    <div class="col-12">
                        <label for="">Name</label>
                        <input type="text" class="form-control" v-model="dataValues.name">
                        <div class="text-danger" v-if="errors.name">{{ errors.name[0] }}</div>
                    </div>  
                </div>
            </template>
            <template #modalFooter>
                <div class="text-right">
                    <button class="btn btn-success" v-on:click="storeData">Save</button>
                </div>
            </template>
        </ModalComponent> -->



    </div>
</template>

<script>    
import FormComponent from "./partials/FormComponent.vue";   
import ModalComponent from "./partials/ModalComponent.vue";
import BreadCrumbComponent from "./partials/BreadCrumbComponent.vue";
import Supplier1Component from "./Supplier1Component.vue";
import RequestProductComponent from "./RequestProductComponent.vue";
import Swal from 'sweetalert2'
import axios from 'axios';

export default{

    data(){
        return{
                data : [],
                pageSelected : 1,
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
                },
                modalId : 'modal-supplier',
                modalTitle : 'Supplier',
                modalPosition: 'modal-dialog-centered',
                modalSize : 'modal-md',
        }
    },
    components: {
        FormComponent,
        ModalComponent,
        BreadCrumbComponent,
        Supplier1Component,
        RequestProductComponent
    },
    methods: {
        addClicked(props){
            $('#' + this.modalId).modal('show');
            this.clearInputs();
        },
        getData() {
            axios.get('/supplier/show').then(response => {
                this.data = response.data.data;
            })
        },
        onClickSelection(page) {
            this.pageSelected = page;
        },
        clearInputs() {
            this.dataValues = {
                name: '',
            }
            this.errors = [];
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
