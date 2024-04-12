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
                        <label for="">Contact Number</label>
                        <input type="number" class="form-control" v-model="dataValues.contact_address">
                        <div class="text-danger" v-if="errors.contact_address">{{ errors.contact_address[0] }}</div>
                    </div>  
                    <div class="col-12 pb-2">
                        <label for="">Region</label>
                        <select class="form-control" v-model="dataValues.region_id">
                            <option v-for="region in regions" :value="region.id">{{ region.name }}</option>
                        </select>
                        <div class="text-danger" v-if="errors.region_id">{{ errors.region_id[0] }}</div>
                    </div> 
                    <div class="col-12 pb-2">
                        <label for="">Province</label>
                        <select class="form-control" v-model="dataValues.province_id">
                            <option v-for="province in provinces" :value="province.id">{{ province.name }}</option>
                        </select>
                        <div class="text-danger" v-if="errors.province_id">{{ errors.province_id[0] }}</div>
                    </div> 
                    <div class="col-12 mb-2">
                        <label for="">City</label>
                        <input type="text" class="form-control" v-model="dataValues.city">
                        <div class="text-danger" v-if="errors.city">{{ errors.city[0] }}</div>
                    </div>  
                    <div class="col-12 mb-2">
                        <label for="">Barangay</label>
                        <input type="text" class="form-control" v-model="dataValues.barangay">
                        <div class="text-danger" v-if="errors.barangay">{{ errors.barangay[0] }}</div>
                    </div>  
                    <div class="col-12 mb-2">
                        <label for="">House Number</label>
                        <input type="number" class="form-control" v-model="dataValues.house_number">
                        <div class="text-danger" v-if="errors.house_number">{{ errors.house_number[0] }}</div>
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
                columns : ['name', 'email', 'balance', 'contact_address', 'region_name', 'province_name', 'city', 'barangay', 'house_number' ,'action'],
                errors: [],
                options : {
                    headings : {
                        name : 'Name',
                        email : 'Email',
                        balance : 'Balance',
                        contact_address : 'Contact Address',
                        region_name : 'Region',
                        province_name : 'Province',
                        city : 'City',
                        barangay : 'Barangay',
                        house_number : 'House Number',
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
                this.regions = response.data.regions;
                this.provinces = response.data.provinces;
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
