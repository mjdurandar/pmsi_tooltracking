<template>
    <div class="p-3">
        <BreadCrumbComponent tab_title="Orders"></BreadCrumbComponent>
<!-- 
        <div class="row mb-3">
            <div class="col-lg-2">
                <select v-model="selectedBrand" class="form-control">
                    <option value="" disabled selected>Brand</option>
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
                    <option value="Ryobi">Ryobi</option> 
                </select>
            </div>
            <div class="col-lg-2">
                <select v-model="selectedTool" class="form-control">
                    <option value="" disabled selected>Tools</option> 
                    <option value="Drill">Drill</option>
                    <option value="Screwdriver">Screwdriver</option>
                    <option value="Wrench">Wrench</option>
                    <option value="Grinder">Grinder</option>
                    <option value="Jigsaw">Jigsaw</option>
                    <option value="Saw">Saw</option>
                </select>
            </div>
            <div class="col-lg-2">
                <select v-model="selectedStatus" class="form-control">
                    <option value="" disabled selected>Status</option> 
                    <option value="Preparing">Preparing</option>
                    <option value="Out for Delivery">Out for Delivery</option>
                    <option value="Delay">Delay</option>
                    <option value="Completed">Completed</option>
                    <option value="Pending">Pending</option>
                </select>
            </div>
            <div class="col-lg-2">
                <select v-model="selectedType" class="form-control">
                    <option value="" disabled selected>Type</option> 
                    <option value="Borrowing">Borrowing</option>
                    <option value="Selling">Selling</option>
                </select>
            </div>
            <div>
                <button class="btn btn-primary" @click="filterData">Search</button>
                <button class="btn btn-success ml-1" @click="refresh"><i class="fas fa-sync-alt"></i></button>
            </div>
        </div> -->

        <div class="card">
            <div class="card-body">
                <FormComponent 
                    :data="data"
                    :columns="columns"
                    :options="options"
                    :addButton="true"
                    btnName="Completed"
                    :option1Switch="true"
                    :option2Switch="false"
                    :option3Switch="true"
                    option1Name="Check Order"
                    addButtonColor="btn btn-success"
                    option3Name="Update Status"
                    option3Icon="fa fa-eye mr-2"
                    @editClicked="checkOrder"
                    @optionalClicked="optionalClicked"
                    @addClicked="completedOrder"
                    :otherButton=true
                    otherBtnName="Canceled"
                    @otherClicked="canceledOrder"
                >
                </FormComponent>
            </div>
        </div>

        <ModalComponent :id="modalId" :title="modalTitle" :size="modalSize" :position="modalPosition">
            <template #modalHeader>
                <div class="m-auto">
                    <h4>Update Order</h4>
                </div>
            </template>
            <template #modalBody>
                <div class="row">
                    <div class="col-12">
                        <label for="">Order By</label>
                        <input type="text" class="form-control" v-model="dataValues.user_name" disabled>
                    </div>  
                    <div class="col-6">
                        <label for="">Brand</label>
                        <input type="text" class="form-control" v-model="dataValues.brand_name" disabled>
                    </div>  
                    <div class="col-6">
                        <label for="">Tool</label>
                        <input type="text" class="form-control" v-model="dataValues.tool_name" disabled>
                    </div>  
                    <div class="col-6">
                        <label for="">Shipment Date</label>
                        <input type="date" class="form-control" v-model="dataValues.shipment_date" :disabled="dataValues.status === 'Completed'" :min="today">
                        <div class="text-danger" v-if="errors.shipment_date">{{ errors.shipment_date[0] }}</div>
                    </div> 
                    <div class="col-6">
                        <label for="">Arrival Date</label>
                        <input type="date" class="form-control" v-model="dataValues.delivery_date" :disabled="dataValues.status === 'Completed'" :min="today">
                        <div class="text-danger" v-if="errors.delivery_date">{{ errors.delivery_date[0] }}</div>
                    </div> 
                    <div class="col-12">
                        <label for="">Status</label>
                        <select name="" class="form-control" v-model="dataValues.status_data">
                            <option value="Preparing">Preparing</option>
                            <option value="Out For Delivery">Out For Delivery</option>
                            <option value="Delay">Delay</option>
                            <option value="Completed">Completed</option>
                        </select>
                        <div class="text-danger" v-if="errors.status_data">{{ errors.status_data[0] }}</div>
                    </div>  
                    <div class="col-12">
                        <label for="">Deliver to this Location: </label>
                        <textarea ype="text" class="form-control" v-model="dataValues.location" disabled></textarea>
                    </div>  
                </div>
            </template>
            <template #modalFooter>
                <div class="text-right">
                    <button class="btn btn-success" v-on:click="updateStatus">Update</button>
                </div>
            </template>
        </ModalComponent>

        <ModalComponent :id="modalIdCheckOrder" :title="modalTitle" :size="modalIdCheckOrderSize" :position="modalPosition">
            <template #modalHeader>
                <div class="m-auto">
                    <h4>Ordered Product</h4>
                </div>
            </template>
            <template #modalBody>
                <div class="row">
                    <div class="col-6 text-center m-auto" v-if="dataValues.image">
                        <img :src="'/images/' + dataValues.image" alt="Current Image" class="img-fluid" style="height:300px;">
                    </div>
                    <div class="col-6">
                        <p>
                            <b>{{ this.dataValues.brand_name }} {{ this.dataValues.tool_name }}</b> with the voltage of {{ this.dataValues.voltage }}, dimension of {{ this.dataValues.dimensions }}, weight of {{ this.dataValues.weight }} and powerSources of {{ this.dataValues.powerSources }}.
                        </p>
                        <p>
                            The User ordered a total of <b>{{ this.dataValues.serial_numbers_count }}</b> product(s) with the total price of <b>₱{{ this.dataValues.total_price }}.</b>
                        </p>
                        <p>
                            The Serial Numbers are: <b>{{ this.dataValues.serial_numbers }}</b> 
                        </p>
                        <p>
                            The Price of : <b>₱{{ this.dataValues.total_price }}</b> is already credited to your Account.
                        </p>
                        <p>
                            Please ship the product to the following address: <b>{{ this.dataValues.location }}</b>
                        </p>
                        <p>
                            Customer Details: 
                            <ul>
                                <li>Name: <b>{{ this.dataValues.user_name }}</b></li>
                                <li>Email: <b>{{ this.dataValues.email }}</b></li>
                                <li>Contact Number: <b>{{ this.dataValues.contact_address }}</b></li>
                            </ul>
                        </p>
                    </div>
                </div>
            </template>
            <template #modalFooter>
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
                columns : ['brand_name', 'tool_name', 'status' ,'type', 'ordered_at', 'shipment_date', 'delivery_date' ,'action'],
                errors: [],
                selectedBrand: '',
                selectedTool: '',
                selectedStatus: '',
                selectedType: '',
                options : {
                    headings : {
                        brand_name : 'Brand',
                        tool_name : 'Tool',
                        status : 'Status',
                        type : 'Type',
                        ordered_at : 'Ordered At',
                        shipment_date : 'Shipment Date',
                        delivery_date : 'Arrival Date',
                        action : 'Action',
                    },
                    filterable: false,
                    sortable: []
                },
                dataValues: {
                    name: '',
                },
                modalId : 'modal-unit',
                modalIdCheckOrder : 'modal-check-order',
                modalTitle : 'Unit',
                modalPosition: 'modal-dialog-centered',
                modalSize : 'modal-md',
                modalIdCheckOrderSize : 'modal-lg',
        }
    },
    components: {
        FormComponent,
        ModalComponent,
        BreadCrumbComponent,
    },
    computed: {
        today() {
            const today = new Date();
            const year = today.getFullYear();
            const month = String(today.getMonth() + 1).padStart(2, '0');
            const day = String(today.getDate()).padStart(2, '0');
            return `${year}-${month}-${day}`;
        }
    },
    methods: {
        addClicked(props){
            $('#' + this.modalId).modal('show');
            this.clearInputs();
        },
        completedOrder(){
            window.location.href = '/complete-order-admin-product';
        },
        canceledOrder(){
            window.location.href = '/canceled-order-admin-product';
        },
        checkOrder(props){
            this.dataValues = props.data;
            this.modalTitle= 'View Order';
            $('#' + this.modalIdCheckOrder).modal('show');
        },
        getData() {
            axios.get('/order/show').then(response => {
                this.data = response.data.data;
                this.data.forEach(item => {
                    item.created_at = new Date(item.created_at).toLocaleString(); // Format to the user's locale
                })
            })
        },
        validateForm() {
            this.errors = [];

            if (!this.dataValues.shipment_date) {
                this.errors.shipment_date = ['Shipment Date is required'];
            }

            if (!this.dataValues.delivery_date) {
                this.errors.delivery_date = ['Delivery Date is required'];
            }

            if (!this.dataValues.status_data) {
                this.errors.status_data = ['Status is required'];
            }

            // Check if any errors are present
            if (Object.keys(this.errors).length > 0) {
                return false; // Validation failed
            }

            return true; // Validation passed
        },
        clearInputs() {
            this.dataValues = {
                name: '',
            }
            this.errors = [];
        },
        filterData()
        {
            const searchData = { 
                selectedBrand : this.selectedBrand,
                selectedTool : this.selectedTool,
                selectedStatus : this.selectedStatus,
                selectedType : this.selectedType,
            }

            axios.post('/order/filterData', searchData)
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

        },
        optionalClicked(props) {
            this.dataValues = props.data;
            this.modalTitle= 'View Order';
            $('#' + this.modalId).modal('show');
        },
        updateStatus() {
            if (this.validateForm()) {
                axios.post('/order/updateStatus', this.dataValues).then(response => {
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
                });
            }
            },
        },
        mounted() {
            this.getData();
        }
}

</script>
