<template>
    <div class="p-3">
        <BreadCrumbComponent tab_title="Canceled Order"></BreadCrumbComponent>
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
                    option3Name=""
                    option3Icon="fa-solid fa-eye"
                    @optionalClicked="viewOrder"
                >
                </FormComponent>
            </div>
        </div>

        <ModalComponent :id="modalId" :title="modalTitle" :size="modalPosition" :position="modalSize">
            <template #modalHeader>
                <div class="m-auto">
                    <h4>Canceled Product</h4>
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
                            User ordered it At : <b>{{this.dataValues.ordered_at}}</b>
                        </p>
                        <p>
                            User canceled it At : <b>{{this.dataValues.canceled_at}}</b>
                        </p>
                        <p>
                            Reason : <b>{{this.dataValues.reason}}</b>
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
                columns : ['brand_name', 'tool_name', 'ordered_at', 'canceled_at' ,'action'],
                errors: [],
                options : {
                    headings : {
                        brand_name : 'Brand',
                        tool_name : 'Tool',
                        ordered_at : 'Placed Order At',
                        canceled_at : 'Canceled Order At',
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
            axios.get('/canceled-order-admin-product/canceledOrderAdminShow').then(response => {
                this.data = response.data.data;
                this.data.forEach(item => {
                    item.created_at = new Date(item.created_at).toLocaleString(); // Format to the user's locale
                });
            })
        },
        clearInputs() {
            this.dataValues = {
                name: '',
            }
            this.errors = [];
        },
        viewOrder(props){
            this.dataValues = props.data;
            $('#' + this.modalId).modal('show');    
        }   ,
        },
        mounted() {
            this.getData();
        }
        


}

</script>
