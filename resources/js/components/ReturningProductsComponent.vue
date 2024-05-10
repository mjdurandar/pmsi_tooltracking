<template>
    <div class="p-3">
        <BreadCrumbComponent tab_title="Returning Products"></BreadCrumbComponent>
        <div class="card">
            <div class="card-body">
                <FormComponent 
                    :data="data"
                    :columns="columns"
                    :options="options"
                    btnName="Add Unit"
                    :option1Switch="false"
                    :option2Switch="false"
                    :option3Switch="true"
                    option3Name=""
                    option3Icon="fa fa-eye mr-2"
                    option3Color="color: #168118"
                    @optionalClicked="viewClicked"
                    :addButton="false"
                >
                </FormComponent>
            </div>
        </div>

        <!-- VIEW ORDER -->
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
                            Serial Numbers are: <b>{{ this.dataValues.serial_numbers }}</b>
                        </p>
                        <b>Arrival Date:</b> {{ this.dataValues.arrival_date }}
                        <p>
                            User Details:
                            <ul>
                                <li>
                                    Name: <b>{{ this.dataValues.user_name }}</b> 
                                </li>
                                <li>
                                    Email: <b>{{ this.dataValues.user_email }}</b> 
                                </li>
                                <li>
                                    Contact Number: <b>{{ this.dataValues.contact_address }}</b>
                                </li>
                                <li>
                                    Location: <b>{{ this.dataValues.location }}</b>
                                </li>
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
                columns : ['brand_name', 'tool_name', 'serial_numbers' ,'arrival_date' ,'action'],
                errors: [],
                userAdmin : [],
                options : {
                    headings : {
                        brand_name : 'Brand',
                        tool_name : 'Tool',
                        serial_numbers : 'Serial Numbers',
                        arrival_date : 'Arrival Date',
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
        addClicked(props){
            $('#' + this.modalId).modal('show');
            this.clearInputs();
        },
        getData() {
            axios.get('/returning-products/show').then(response => {
                this.data = response.data.data;
                this.userAdmin = response.data.userAdmin;
            })
        },
        clearInputs() {
            this.dataValues = {
                name: '',
            }
            this.errors = [];
        },
        viewClicked(props){
            this.dataValues = props.data;
            $('#' + this.modalId).modal('show');
        },
        },
        mounted() {
            this.getData();
        }
        


}

</script>
