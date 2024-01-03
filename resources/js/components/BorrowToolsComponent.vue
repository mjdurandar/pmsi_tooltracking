<template>
    <div class="p-3">
        <BreadCrumbComponent tab_title="Borrow Tools"></BreadCrumbComponent>
        <div class="row">
            <div class="col-md-4" v-for="(tool, index) in data" :key="index">
                <!-- Card component for each tool -->
                <div class="card">
                    <div class="card-body">
                        <!-- Display tool information -->
                        <div class="d-flex justify-content-between">
                            <div style="width: 50%;">
                                <h5 class="card-title">{{ tool.name }}</h5>
                                <p class="card-text">Price: {{ tool.price }}</p>
                                <p class="card-text">Stocks: {{ tool.quantity }}</p>
                            </div>
                            <div style="width: 50%;">
                                <img v-if="tool.image" :src="'/images/' + tool.image" alt="Tool Image" class="img-fluid">
                                <p v-else>No Image</p>
                            </div>
                        </div>
                        <button class="btn btn-primary" v-on:click="showDetails(tool)">Borrow Tool</button>
                    </div>
                </div>
            </div>
        </div>
        <ModalComponent :id="modalId" :title="modalTitle" :size="modalSize" :position="modalPosition">
            <template #modalHeader>
                <div class="m-auto">
                    <h4>Borrow a Tool</h4>
                </div>
            </template>
            <template #modalBody>
                <div class="row">
                    <div class="col-12 pb-2">
                        <input type="hidden" v-model="dataValues.scaffoldings_id">
                        <label for="">Scaffolding</label>
                        <input type="text" v-model="selectedTool.name" class="form-control" disabled> 
                    </div>  
                    <div class="col-12 pb-2">
                        <label for="">Price</label>
                        <input type="number" v-model="selectedTool.price" class="form-control" disabled> 
                    </div>  
                    <div class="col-12 pb-2">
                        <label for="">Quantity</label>
                        <input type="number" v-model="dataValues.quantity" class="form-control" @keyup="onChange()"> 
                        <div class="text-danger" v-if="errors.quantity">{{ errors.quantity[0] }}</div>
                    </div> 
                    <div class="col-12 pb-2">
                        <label for="">Return Days</label>
                        <select class="form-control" v-model="dataValues.return_days_id">
                            <option v-for="returnday in returndays" :value="returnday.id">{{ returnday.number_of_days }}</option>
                        </select>
                        <div class="text-danger" v-if="errors.returnday">{{ errors.returnday[0] }}</div>
                    </div> 
                    <div class="col-12">
                        <label for="">Total</label>
                        <input type="number" v-model="dataValues.total" class="form-control" @keyup="onChange()" disabled> 
                        <div class="text-danger" v-if="errors.total">{{ errors.total[0] }}</div>
                    </div>  
                </div>
            </template>
            <template #modalFooter>
                <div class="text-right">
                    <button class="btn btn-primary" v-on:click="storeData">Borrow a Tool</button>
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
            selectedTool: [],
            returndays: '',
            dataValues: {},
            modalId : 'modal-borrowtools',
            modalTitle : 'Borrow Tools',
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
        onChange(){
            if(this.dataValues.quantity === ''){
                this.dataValues.total = 0;
                return;
            }
            this.dataValues.total = this.dataValues.quantity * this.selectedTool.price;
        },
        showDetails(tool) {
            console.log(tool);
            this.selectedTool = tool;
            this.dataValues = {
                scaffoldings_id: tool.id,
            };
            $('#' + this.modalId).modal('show');
        },
        getData() {
            axios.get('/borrowtools/show').then(response => {
                this.data = response.data.data;
                this.returndays = response.data.returndays;
            })
        },
        clearInputs() {
            this.dataValues = {
                name: '',
            }
            this.errors = [];
        },
        storeData() {
            axios.post('/borrowtools/store', this.dataValues).then(response => {
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
                if(errors.response.data.message.length > 0) {
                    Swal.fire({
                        title: "Warning",
                        text: errors.response.data.message,
                        icon: 'warning',
                        timer: 3000
                    });

                    this.errors = errors.response.data.errors;
                }
            })
        },
    },
    mounted() {
            this.getData();
        }
        


}

</script>
