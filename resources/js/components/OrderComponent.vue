<template>
    <div class="p-3">
        <BreadCrumbComponent tab_title="Orders"></BreadCrumbComponent>
        <div class="card">
            <div class="card-body">
                <FormComponent 
                    :data="data"
                    :columns="columns"
                    :options="options"
                    :addButton="false"
                    :option1Switch="false"
                    :option2Switch="false"
                    :option3Switch="true"
                    option3Name="View"
                    option3Icon="fa fa-eye mr-2"
                    @optionalClicked="optionalClicked"
                >
                </FormComponent>
            </div>
        </div>

        <ModalComponent :id="modalId" :title="modalTitle" :size="modalSize" :position="modalPosition">
            <template #modalHeader>
                <div class="m-auto">
                    <h4>View Order</h4>
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
                        <input type="date" class="form-control" v-model="dataValues.shipment_date" :disabled="dataValues.status === 'Completed'">
                    </div> 
                    <div class="col-6">
                        <label for="">Delivery Date</label>
                        <input type="date" class="form-control" v-model="dataValues.delivery_date" :disabled="dataValues.status === 'Completed'">
                    </div> 
                    <div class="col-12">
                        <label for="">Status</label>
                        <select name="" class="form-control" v-model="dataValues.status_data">
                            <option value="Preparing">Preparing</option>
                            <option value="Out For Delivery">Out For Delivery</option>
                            <option value="Delay">Delay</option>
                            <option value="Completed">Completed</option>
                        </select>
                        <div class="text-danger" v-if="errors.status">{{ errors.status[0] }}</div>
                    </div>  
                    <div class="col-12">
                        <label for="">Deliver to this Location: </label>
                        <input type="text" class="form-control" v-model="dataValues.location" disabled>
                    </div>  
                </div>
            </template>
            <template #modalFooter>
                <div class="text-right">
                    <button class="btn btn-success" v-on:click="storeData">Update</button>
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
                columns : ['brand_name', 'tool_name', 'type' ,'status' ,'action'],
                errors: [],
                options : {
                    headings : {
                        brand_name : 'Brand',
                        tool_name : 'Tool',
                        type : 'Type',
                        status : 'Status',
                        action : 'Action',
                    },
                    filterable: false,
                    sortable: []
                },
                dataValues: {
                    name: '',
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
            axios.get('/order/show').then(response => {
                this.data = response.data.data;
            })
        },
        clearInputs() {
            this.dataValues = {
                name: '',
            }
            this.errors = [];
        },
        optionalClicked(props) {
            this.dataValues = props.data;
            this.modalTitle= 'View Order';
            $('#' + this.modalId).modal('show');
        },
        storeData() {
                axios.post('/order/store', this.dataValues).then(response => {
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
