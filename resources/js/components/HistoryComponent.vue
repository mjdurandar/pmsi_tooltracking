<template>
    <div class="p-3">
        <BreadCrumbComponent tab_title="History"></BreadCrumbComponent>
        <div class="row mb-3">
            <div class="col-lg-2">
                <input type="text" class="form-control" v-model="historyNumber" placeholder="History Number">
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
                    :option2Switch="false"
                    option1Color="color: #32CD32;"
                    option1Icon="fa-solid fa-eye"
                    option1Name=""
                    :addButton="false"
                    @editClicked="editClicked"
                >
                </FormComponent>
            </div>
        </div>

     <!-- View History -->
     <ModalComponent :id="modalId" :title="modalTitle" :size="modalSize" :position="modalPosition">
            <template #modalHeader>
                <div class="m-auto">
                    <h3>View History</h3>
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
                            The Description is: <b>{{ this.dataValues.action_name }}</b>
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
                historyNumber: '',
                columns : ['history_number','action_name','action'],
                errors: [],
                options : {
                    headings : {
                        history_number : 'History Number',
                        action_name : 'Description',
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
                modalSize : 'modal-lg',
        }
    },
    components: {
        FormComponent,
        ModalComponent,
        BreadCrumbComponent,
    },
    methods: {
            editClicked(props){
                this.dataValues = props.data;
                $('#' + this.modalId).modal('show');
            },
            storeData(){
                $('#' + this.modalId).modal('hide');
            },
            getData() {
                axios.get('/history/show').then(response => {
                    this.data = response.data.data;
                })
            },
            refresh(){
                window.location.reload();
            },  
            filterData() {
            const searchData = {
                historyNumber: this.historyNumber,
            };

            axios.post('/history/filterData', searchData)
                .then(response => {
                    this.data = response.data.data;
                    this.data.forEach(item => {
                        item.created_at = new Date(item.created_at).toLocaleString(); // Format to the user's locale
                    })
                    if (this.data.length === 0) {
                        Swal.fire({
                            title: "No History Data!",
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
