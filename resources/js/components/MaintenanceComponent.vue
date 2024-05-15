<template>
    <div class="p-3">
        <BreadCrumbComponent tab_title="Maintenance"></BreadCrumbComponent>

        <div class="row mb-3">
            <div class="col-lg-2">
                <input type="text" class="form-control" v-model="serialNumbers" placeholder="Serial Numbers">
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
                    :addButton="false"
                    :option1Switch="false"
                    :option2Switch="false"
                    :option3Switch="true"
                    option3Name=""
                    :option3Icon="'fas fa-eye'"
                    @optionalClicked="viewClicked"
                >
                </FormComponent>
            </div>
        </div>

        <!-- <ModalComponent :id="modalId" :title="modalTitle" :size="modalSize" :position="modalPosition">
            <template #modalHeader>
                <div class="m-auto">
                    <h4>Add Unit</h4>
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
import Swal from 'sweetalert2'
import axios from 'axios';
import refresh from "v-tables-3/compiled/methods/refresh";

export default{

    data(){
        return{
                data : [],
                serialNumbers: '',
                columns : ['serial_number', 'status' ,'action'],
                errors: [],
                options : {
                    headings : {
                        serial_number : 'Serial Numbers',
                        status : 'Description',
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
            axios.get('/maintenance/show').then(response => {
                this.data = response.data.data;
            })
        },
        clearInputs() {
            this.dataValues = {
                name: '',
            }
            this.errors = [];
        },
        viewClicked(props){
            console.log(props);
        }
        
        },
        mounted() {
            this.getData();
        }
        


}

</script>
