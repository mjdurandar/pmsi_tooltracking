<template>
    <div class="p-3">
        <BreadCrumbComponent tab_title="Completed Orders"></BreadCrumbComponent>
        <div class="row mb-3">
            <div class="col-lg-2">
                <input type="text" class="form-control" v-model="orderNumber" placeholder="Order Number">
            </div>
            <div class="col-lg-2">
                <select v-model="selectedType" class="form-control">
                    <option value="" disabled selected>Type</option> 
                    <option value="Borrowing">Borrowing</option>
                    <option value="Buying">Buying</option>
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
                    btnName="Add Unit"
                    :addButton="false"
                    :option1Switch="false"
                    :option2Switch="false"
                    :option3Switch="true"
                    @optionalClicked="viewClicked" 
                    option3Name=""
                    option3Icon="fa fa-eye mr-2"
                >
                </FormComponent>
            </div>
        </div>

        <ModalComponent :id="modalId" :title="modalTitle" :size="modalSize" :position="modalPosition">
            <template #modalHeader>
                <div class="m-auto">
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
                            Serial Numbers:
                            <ul>
                                <li v-for="serial in this.dataValues.serial_numbers"><b>{{ serial }}</b></li>
                            </ul>
                        </p>
                        <p>
                            With a total of: <b>₱{{ this.dataValues.total_price }}</b>
                        </p>
                        <p>
                            This Product is ordered by: <b>{{ this.dataValues.user_name }}</b>
                        </p>
                        <p>
                            This Product is Delivered at this location: <b>{{ this.dataValues.location }}</b>
                        </p>
                        <p>
                            This Product is Delivered at:<br> <b>{{ this.dataValues.completed_at }}</b>
                        </p>
                        <p>
                            This Product is Ordered/Borrowed at: <b>{{ this.dataValues.date_completed }}</b>
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
                orderNumber: '',
                selectedType: '',
                columns : ['order_number','brand_name', 'tool_name', 'type', 'date_completed' ,'completed_at' ,'action'],
                errors: [],
                serialNumbers: [],
                options : {
                    headings : {
                        order_number : 'Order Number',
                        brand_name : 'Brand',
                        tool_name : 'Tool',
                        type : 'Type',
                        date_completed : 'Ordered At/Borrowed At',
                        completed_at : 'Completed At',
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
                modalSize : 'modal-lg',
        }
    },
    components: {
        FormComponent,
        ModalComponent,
        BreadCrumbComponent,
    },
    methods: {
        getData() {
            axios.get('/complete-order-admin-product/completeOrderAdminShow').then(response => {
                this.data = response.data.data;
            })
        },
        viewClicked(props){
            this.dataValues = props.data;
            $('#' + this.modalId).modal('show');
        },
        refresh(){
            window.location.reload();
        },
        filterData()
        {
            const searchData = { 
                orderNumber : this.orderNumber,
                selectedType : this.selectedType,
            }

            axios.post('/completed-order-admin-product/completedFilterData', searchData)
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
    },
        mounted() {
            this.getData();
        }
        


}

</script>
