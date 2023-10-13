<template>
    <div class="p-3">
        <BreadCrumbComponent tab_title="History"></BreadCrumbComponent>
        <div class="card">
            <div class="card-body">
                <FormComponent 
                    :data="data"
                    :columns="columns"
                    :options="options"
                    :option2Switch="false"
                    option1Color="color: #32CD32;"
                    option1Icon="fa-solid fa-eye"
                    option1Name="View"
                    :addButton="false"
                    @editClicked="editClicked"
                >
                </FormComponent>
            </div>
        </div>

        <ModalComponent :id="modalId" :title="modalTitle" :size="modalSize" :position="modalPosition">
            <template #modalHeader>
                <div class="m-auto">
                    <h4>View</h4>
                </div>
            </template>
            <template #modalBody>
                <div class="row">
                </div>
            </template>
            <template #modalFooter>
                <div class="text-right">
                    <button class="btn btn-dark" v-on:click="storeData">Close</button>
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
                columns : ['users_name','power_tools_name','total','purchased_at','status','action'],
                errors: [],
                options : {
                    headings : {
                        users_name : 'User Name',
                        power_tools_name : 'Power Tools',
                        total : 'Total',
                        purchased_at : 'Purchased Date',
                        status : 'Status',
                        action : 'Action',
                    },
                    filterable: false,
                    sortable: []
                },
                dataValues: {
                    name: '',
                },
                modalId : 'modal-history',
                modalTitle : 'History',
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
            editClicked(props){
                $('#' + this.modalId).modal('show');
                this.clearInputs();
            },
            storeData(){
                $('#' + this.modalId).modal('hide');
            },
            getData() {
                axios.get('/history/show').then(response => {
                    this.data = response.data.data;
                })
            },
        },
        mounted() {
            this.getData();
        }
        


}

</script>
