<template>
    <div class="p-3">
        <BreadCrumbComponent tab_title="Add Balance"></BreadCrumbComponent>
        <div class="card">
            <div class="card-body">
                <FormComponent 
                    :data="data"
                    :columns="columns"
                    :options="options"
                    btnName="Add Balance"
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
                    <h4>Balance</h4>
                </div>
            </template>
            <template #modalBody>
                <div class="row">
                    <div class="col-12">
                    <label for="">Payment Method</label>
                    <div>
                        <input type="radio" id="gcash" value="Gcash" v-model="dataValues.card_type">
                        <label for="gcash">GCash</label>
                    </div>
                    <div>
                        <input type="radio" id="paypal" value="Paypal" v-model="dataValues.card_type">
                        <label for="paypal">PayPal</label>
                    </div>
                    <div>
                        <input type="radio" id="card" value="Card" v-model="dataValues.card_type">
                        <label for="card">Card</label>
                    </div>
                </div>

                <div class="col-12" v-if="dataValues.card_type === 'Gcash'">
                    <label for="">GCash Information: Your GCash information here.</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fa-solid fa-credit-card"></i></span>
                        </div>
                        <input type="number" class="form-control" v-model="dataValues.card_number">
                    </div>
                </div>
                <div class="col-12" v-if="dataValues.card_type === 'Paypal'">
                    <label for="">PayPal Information: Your PayPal information here.</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fa-solid fa-credit-card"></i></span>
                        </div>
                        <input type="number" class="form-control" v-model="dataValues.card_number">
                    </div>
                </div>
                <div class="col-12" v-if="dataValues.card_type === 'Card'">
                    <label for="">Card Information: Your card information here.</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fa-solid fa-credit-card"></i></span>
                        </div>
                        <input type="number" class="form-control" v-model="dataValues.card_number">
                    </div>
                </div>

                <div class="col-12">
                    <label for="">Balance</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">₱</span>
                        </div>
                        <input type="number" class="form-control" v-model="dataValues.balance">
                    </div>
                    <div class="text-danger" v-if="errors.balance">{{ errors.balance[0] }}</div>
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
                columns : ['card_number', 'card_type', 'description', 'balance' , 'created_at'],
                errors: [],
                card_type : 'Gcash',
                options : {
                    headings : {
                        card_number : 'Card Number',
                        card_type : 'Card Type',
                        description : 'Description',
                        balance : 'Total Balance',
                        created_at : 'Cash In At'
                    },
                    filterable: false,
                    sortable: []
                },
                dataValues: {
                    name: '',
                    card_type : 'Gcash',
                },
                modalId : 'modal-unit',
                modalTitle : 'Unit',
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
        addClicked(props){
            $('#' + this.modalId).modal('show');
            this.clearInputs();
        },
        getData() {
            axios.get('/add-balance/show').then(response => {
                this.data = response.data.data;
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
        editClicked(props) {
            this.dataValues = props.data;
            this.modalTitle= 'Edit Data';

            axios.get('/unit/edit/' + this.dataValues.id).then(response => {
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
                    axios.get('/unit/destroy/' + props.data.id).then(response => {
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
                axios.post('/add-balance/store', this.dataValues).then(response => {
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
