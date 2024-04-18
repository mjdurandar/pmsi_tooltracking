<template>
    <div class="p-3">
        <BreadCrumbComponent tab_title="Customer Data"></BreadCrumbComponent>
        <div class="row mb-3">
            <div class="col-lg-2">
                <select class="form-control" v-model="account_type">
                    <option value="" disabled selected>Account Type</option>
                    <option value="User">User</option>
                    <option value="Supplier">Supplier</option>
                </select>
            </div>
            <div>
                <button class="btn btn-primary" @click="filterData">Search</button>
                <button class="btn btn-success ml-1" @click="refresh"><i class="fas fa-sync-alt"></i></button>
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <FormComponent 
                    :data="data"
                    :columns="columns"
                    :options="options"
                    btnName="Add Users Account"
                    :option3Switch="false"
                    option3Name="Transactions"
                    @deleteClicked="deleteClicked"
                    @editClicked="editClicked"
                    @optionalClicked="optionalClicked"
                    @addClicked="addClicked"
                >
                </FormComponent>
            </div>
        </div>

        <ModalComponent :id="modalId" :title="modalTitle" :size="modalSize" :position="modalPosition">
            <template #modalHeader>
                <div class="m-auto">
                    <h4>Add Users Account</h4>
                </div>
            </template>
            <template #modalBody>
                <div class="row">
                    <div class="col-12 mb-2">
                        <label for="">Name</label>
                        <input type="text" class="form-control" v-model="dataValues.name">
                        <div class="text-danger" v-if="errors.name">{{ errors.name[0] }}</div>
                    </div>  
                    <div class="col-12 mb-2">
                        <label for="">Email</label>
                        <input type="text" class="form-control" v-model="dataValues.email">
                        <div class="text-danger" v-if="errors.email">{{ errors.email[0] }}</div>
                    </div>  
                    <div class="col-12 mb-2">
                        <label for="">Contact Person</label>
                        <input type="text" class="form-control" v-model="dataValues.contact_person">
                        <div class="text-danger" v-if="errors.contact_person">{{ errors.contact_person[0] }}</div>
                    </div>  
                    <div class="col-12 mb-2">
                        <label for="">Contact Number</label>
                        <input type="number" class="form-control" v-model="dataValues.contact_address">
                        <div class="text-danger" v-if="errors.contact_address">{{ errors.contact_address[0] }}</div>
                    </div>  
                    <div class="col-12 mb-2">
                        <label for="">Location</label>
                        <input type="location" class="form-control" v-model="dataValues.location">
                        <div class="text-danger" v-if="errors.location">{{ errors.location[0] }}</div>
                    </div>  
                    <div class="col-12 mb-2">
                        <label for="">Role</label>
                        <select class="form-control" v-model="dataValues.role">
                            <option value="0">Users</option>
                            <option value="1">Admin</option>
                        </select>
                    </div>  
                    <div class="col-12 mb-2">
                        <label for="">Password</label>
                        <input type="password" class="form-control" v-model="dataValues.password">
                        <div class="text-danger" v-if="errors.password">{{ errors.password[0] }}</div>
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
                regions : [],
                provinces : [],
                account_type : '',
                columns : ['name', 'email', 'accounts' ,'contact_address', 'contact_person','location' ,'action'],
                errors: [],
                options : {
                    headings : {
                        name : 'Name',
                        email : 'Email',
                        accounts : 'Account Type',
                        contact_address : 'Contact Address',
                        contact_person : 'Contact Person',
                        location : 'Location',
                        action : 'Action',
                    },
                    filterable: false,
                    sortable: []
                },
                dataValues: {
                    name: '',
                },
                modalId : 'modal-users',
                modalIdReceipts: 'modal-receipts',
                modalTitle : 'Users Account',
                modalPosition: 'modal-dialog-centered',
                modalSize : 'modal-md',
                modalSizeReceipts : 'modal-xl'
        }
    },
    components: {
        FormComponent,
        ModalComponent,
        BreadCrumbComponent,
    },
    methods: {
        addClicked(){
            $('#' + this.modalId).modal('show');
            this.clearInputs();
        },
        dismissModal(){
            $('#' + this.modalIdReceipts).modal('hide');
        },
        getData() {
            axios.get('/users/show').then(response => {
                this.data = response.data.data;
            })
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

            axios.get('/users/edit/' + this.dataValues.id).then(response => {
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
        refresh(){
            window.location.reload();
        },
        filterData(){
            const searchData = {
                account_type: this.account_type,
            };
            axios.post('/users/filterData', searchData).then(response => {
                    this.data = response.data.data;
                    if (this.data.length === 0) {
                        Swal.fire({
                            title: "No Data available!",
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
        optionalClicked(props) {
            const userId = props.data.id;
            
            axios.get(`/users/showBuyingHistory/${userId}`)
                .then(response => {
                    this.dataReceipts = response.data.data;
                    this.dataBorrowed = response.data.dataBorrow;
                    $('#' + this.modalIdReceipts).modal('show');
                })
                .catch(error => {
                    console.error('Error fetching data:', error);
                    // Handle the error as needed
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
                    axios.get('/users/destroy/' + props.data.id).then(response => {
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
                axios.post('/users/store', this.dataValues).then(response => {
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
