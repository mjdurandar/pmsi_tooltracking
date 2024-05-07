<template>
    <div class="p-3">
        <BreadCrumbComponent tab_title="Returned Products"></BreadCrumbComponent>
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
                    @optionalClicked="viewProduct"
                    option3Name=""
                    option3Icon="fa fa-eye"
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
                            User request to return the product : <b>{{this.dataValues.returned_at}}</b>
                        </p>
                        <p>
                            Reason : <b>{{this.dataValues.reason}}</b>
                        </p>
                        <p>
                            Please Contact the user with this information.
                            <ul>
                                <li><b>{{ this.dataValues.user_name }}</b></li>
                                <li><b>{{ this.dataValues.user_email }}</b></li>
                                <li><b>{{ this.dataValues.user_phone }}</b></li>
                                <li><b>{{ this.dataValues.location }}</b></li>
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
                columns : ['brand_name', 'tool_name', 'serial_number', 'returned_at' ,'action'],
                errors: [],
                options : {
                    headings : {
                        brand_name : 'Brand',
                        tool_name : 'Tool',
                        serial_number : 'Serial Number',
                        returned_at : 'Returned At',
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
            axios.get('returned-products-supplier/show').then(response => {
                this.data = response.data.data;
            })
        },
        clearInputs() {
            this.dataValues = {
                name: '',
            }
            this.errors = [];
        },
        viewProduct(props){
            this.dataValues = props.data;
            $('#' + this.modalId).modal('show');
        },
        },
        mounted() {
            this.getData();
        }
        


}

</script>
