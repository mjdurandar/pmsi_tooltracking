<template>
    <div class="p-3">
        <BreadCrumbComponent tab_title="Request Return"></BreadCrumbComponent>
        <div class="row mb-3">
            <div class="col-lg-2">
                <input type="text" class="form-control" v-model="serialNumber" placeholder="Serial Number">
            </div>
            <div class="col-lg-2">
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
                    option3Name="View"
                    option3Icon="fa-solid fa-eye mr-2"
                    @optionalClicked="viewRequest"
                >
                </FormComponent>
            </div>
        </div>

        <ModalComponent :id="modalId" :title="modalTitle" :size="modalSize" :position="modalPosition">
        <!-- Modal Header -->
        <template #modalHeader>
            <div class="m-auto">
                <h4>Request</h4>
            </div>
        </template>

        <!-- Modal Body -->
        <template #modalBody>
            <div class="row text-v">
                <div class="col-12">
                    <p>
                        The user has requested to return the items with this serial number:
                        <br>
                        <b>{{ dataValues.serial_number }}</b>
                    </p>
                </div>  

                <div class="col-12">
                    <p>
                        Description:
                        <br>
                        <b>{{ dataValues.description }}</b>
                    </p>
                </div>  

                <div class="col-12 mt-2">
                    <p>
                        Please contact the user to confirm the return of the items. Here are the details:
                    </p>
                    <p><b>Name:</b> {{ dataValues.user_name }}</p>
                    <p><b>Email:</b> {{ dataValues.user_email }}</p>
                    <p><b>Phone:</b> {{ dataValues.user_phone }}</p>
                    <p><b>Location:</b> {{ dataValues.user_address }}</p>
                </div>  
            </div>
        </template>

        <!-- Modal Footer -->
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
import refresh from "v-tables-3/compiled/methods/refresh";

export default{

    data(){
        return{
                data : [],
                columns : ['serial_number', 'description' ,'action'],
                errors: [],
                options : {
                    headings : {
                        serial_number : 'Serial Number',
                        description : 'Description',
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
        refresh(){
            window.location.reload();
        },
        getData() {
            axios.get('/messages/show').then(response => {
                this.data = response.data.data;
            })
        },
        clearInputs() {
            this.dataValues = {
                name: '',
            }
            this.errors = [];
        },
        viewRequest(props){
            this.dataValues = props.data;
            $('#' + this.modalId).modal('show');
        },
        },
        mounted() {
            this.getData();
        }
}

</script>
<style scoped>
.text-v {
    font-size: 1.2em;
}
.modal-header {
    margin: auto;
}
.modal-body {
    padding: 20px;
}
.modal-footer {
    padding: 10px;
    text-align: right;
}
</style>