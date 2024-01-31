<template>
    <div class="p-3">
        <BreadCrumbComponent tab_title="Borrowed History"></BreadCrumbComponent>
        <div class="card">
            <div class="card-body">
                <FormComponent 
                    :data="data"
                    :columns="columns"
                    :options="options"
                    :addButton="false"
                    :option2Switch="false"
                    option1Color = "color: #089000"
                    option1Icon="fa fa-eye mr-2"
                    option1Name="View"
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
                <div class="col-12 pb-2">
                    <label for="">Username</label>
                        <input class="form-control" v-model="dataValues.users_name" disabled>
                </div> 
                <div class="col-12 pb-2">
                    <label for="">Scaffolding Name</label>
                        <input class="form-control" v-model="dataValues.scaffoldings_name" disabled>
                </div> 
                <div class="col-12 pb-2">
                    <label for="">Return Days</label>
                        <input class="form-control" v-model="dataValues.number_of_days" disabled>
                </div> 
                <div class="col-12 pb-2">
                    <label for="">Borrowed At</label>
                        <input class="form-control" v-model="dataValues.borrowed_at" disabled>
                </div> 
            </template>
            <template #modalFooter>
                <div class="text-right">
                    <button class="btn btn-dark" v-on:click="closeModal">Close</button>
                </div>
            </template>
        </ModalComponent>

    </div>
</template>

<script>    
import BreadCrumbComponent from "./partials/BreadCrumbComponent.vue";   
import FormComponent from "./partials/FormComponent.vue";   
import ModalComponent from "./partials/ModalComponent.vue";
import Swal from 'sweetalert2'
import axios from 'axios';

export default{

    data(){
        return{
                data : [],
                errors: [],
                columns : ['users_name', 'scaffoldings_name', 'number_of_days', 'borrowed_at','action'],
                options : {
                    headings : {
                        users_name : 'User',
                        scaffoldings_name : 'Scaffolding',
                        number_of_days : 'Return Days',
                        borrowed_at : 'Borrowed At',
                        action : 'Action',
                    },
                    filterable: false,
                    sortable: []
                },
                dataValues: {
                    name: '',
                },
                modalId : 'modal-borrowedhistory',
                modalTitle : 'Borrowed History',
                modalPosition: 'modal-dialog-centered',
                modalSize : 'modal-md',
        }
    },
    components: {
        FormComponent,
        ModalComponent,
        BreadCrumbComponent
    },
    methods: {
        addClicked(props){
            $('#' + this.modalId).modal('show');
            this.clearInputs();
        },
        closeModal(){
            $('#' + this.modalId).modal('hide');
        },
        getData() {
            axios.get('/borrowedhistory/showHistory').then(response => {
                this.data = response.data.data;
            })
        },
        editClicked(props) {
            this.dataValues = props.data;
            this.modalTitle= 'View Data';
            $('#' + this.modalId).modal('show');
        },
        },
        mounted() {
            this.getData();
        }
}

</script>
