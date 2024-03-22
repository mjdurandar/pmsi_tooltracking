<template>
    <div class="p-3">
        <BreadCrumbComponent tab_title="Customer Data"></BreadCrumbComponent>
        <div class="card">
            <div class="card-body">
                <FormComponent 
                    :data="data"
                    :columns="columns"
                    :options="options"
                    btnName="Add Users Account"
                    :option3Switch="false"
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
                        <label for="">Balance</label>
                        <input type="text" class="form-control" v-model="dataValues.balance">
                        <div class="text-danger" v-if="errors.balance">{{ errors.balance[0] }}</div>
                    </div>
                    <div class="col-12 mb-2">
                        <label for="">Address</label>
                        <input type="text" class="form-control" v-model="dataValues.address">
                        <div class="text-danger" v-if="errors.address">{{ errors.address[0] }}</div>
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

        <!-- Purchased -->
        <ModalComponent :id="modalIdReceipts" :title="modalTitle" :size="modalSizeReceipts" :position="modalPosition">
            <template #modalBody>
                <div class="row">
                    <div class="col-lg-6 text-center">
                        <label for="">Purchased</label>
                        <div class="card">
                            <div class="card-body">
                                <FormComponent 
                                    :data="dataReceipts"
                                    :columns="columnsReceipts"
                                    :options="optionsReceipts"
                                    :addButton="false"
                                    :option1Switch="false"
                                    :option2Switch="false"
                                >
                                </FormComponent>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 text-center">
                        <label for="">Borrowed</label>
                        <div class="card">
                            <div class="card-body">
                                <FormComponent 
                                    :data="dataBorrowed"
                                    :columns="columnsBorrowed"
                                    :options="optionsBorrowed"
                                    :addButton="false"
                                    :option1Switch="false"
                                    :option2Switch="false"
                                >
                                </FormComponent>
                            </div>
                        </div>
                    </div>
                </div>
                
            </template>
            <template #modalFooter>
                <div class="text-right">
                    <button class="btn btn-dark" v-on:click="dismissModal">Close</button>
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
                columns : ['name', 'email', 'balance' ,'address' ,'action'],
                errors: [],
                options : {
                    headings : {
                        name : 'Name',
                        email : 'Email',
                        balance : 'Balance',
                        address : 'Complete Address',
                        action : 'Action',
                    },
                    filterable: false,
                    sortable: []
                },
                dataReceipts : [],
                columnsReceipts : ['users_name', 'power_tools_name' ,'price', 'purchased_at'],
                optionsReceipts : {
                    headings : {
                        users_name : 'Name',
                        power_tools_name : 'Tools',
                        price : 'Price',
                        purchased_at : 'Purchased Date',
                    },
                    filterable: false,
                    sortable: []
                },
                dataBorrowed : [],
                columnsBorrowed : ['users_name', 'scaffoldings_name' ,'price', 'borrowed_at'],
                optionsBorrowed : {
                    headings : {
                        users_name : 'Name',
                        scaffoldings_name : 'Tools',
                        price : 'Price',
                        borrowed_at : 'Borrowed Date',
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
