<template>
    <div class="p-3">
        <BreadCrumbComponent tab_title="Defective Products"></BreadCrumbComponent>
        <div class="card">
            <div class="card-body">
                <FormComponent 
                    :data="data"
                    :columns="columns"
                    :options="options"
                    :addButton='false'
                    :option1Switch="false"
                    :option2Switch="false"
                    :option3Switch="true"
                    option3Name="Release"
                    option3Icon="fa-solid fa-people-carry-box"
                    @optionalClicked="confirmationProduct"
                >
                </FormComponent>
            </div>
        </div>

        <!-- CONFIRMATION MODAL -->
        <ModalComponent :id="modalIdConfirmation" :title="modalTitle" :size="modalSize" :position="modalPosition">
            <template #modalHeader>
                <div class="m-auto">
                    <h4>Have You Received the Product?</h4>
                </div>
            </template>
            <template #modalBody>
                <div class="row">
                    <div class="col-12">
                        <label for="">If you have not yet received the product, please contact the customer at this following details:</label>
                    </div>  
                    <div class="col-12">
                        <label for="">Customer Name:</label>
                        <input type="text" class="form-control" v-model="dataValues.user_name" disabled>
                    </div>  
                    <div class="col-12">
                        <label for="">Customer Email Address:</label>
                        <input type="text" class="form-control" v-model="dataValues.user_email" disabled>
                    </div> 
                    <div class="col-12">
                        <label for="">Customer Contact Number:</label>
                        <input type="number" class="form-control" v-model="dataValues.user_phone" disabled>
                    </div> 
                    <div class="col-12">
                        <label for="">The Product Delivery Date is:</label>
                        <input type="text" class="form-control" v-model="dataValues.delivery_date" disabled>
                    </div>  
                </div>
            </template>
            <template #modalFooter>
                <div class="text-right">
                    <button class="btn btn-success" v-on:click="releasedProduct">Yes</button>
                </div>
            </template>
        </ModalComponent>

        <!-- check product MODAL -->
        <ModalComponent :id="modalId" :title="modalTitle" :size="modalSize" :position="modalPosition">
            <template #modalHeader>
                <div class="m-auto">
                    <h4>Returned Product Check</h4>
                </div>
            </template>
            <template #modalBody>
                <div class="row">
                    <div class="col-12">
                        <label for="">Please describe the damages of the product? If No damages just leave it blank</label>
                        <textarea type="text" class="form-control" v-model="dataValues.damaged_description"></textarea>
                    </div>  
                </div>
            </template>
            <template #modalFooter>
                <div class="text-right">
                    <button class="btn btn-success" v-on:click="confirmProduct">Confirm</button>
                </div>
            </template>
        </ModalComponent>

        <!-- release MODAL -->
        <ModalComponent :id="modalIdRelease" :title="modalTitle" :size="modalSize" :position="modalPosition">
            <template #modalHeader>
                <div class="m-auto">
                    <h4>Would you like to refund the Customer after confirming the product is damaged?</h4>
                </div>
            </template>
            <template #modalFooter>
                <div class="text-center">
                    <button class="btn btn-success mb-3" v-on:click="forBorrowing">Yes. Refund the Customer</button>
                    <button class="btn btn-secondary" v-on:click="forContacting">No. The Product is damaged and will need to contact the customer for more information.</button>
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
                columns : ['brand_name', 'tool_name', 'product_code', 'damaged_description' ,'action'],
                errors: [],
                options : {
                    headings : {
                        brand_name : 'Brand',
                        tool_name : 'Tool',
                        product_code : 'Product Code',
                        damaged_description : 'Damaged Description',
                        action : 'Action',
                    },
                    filterable: false,
                    sortable: []
                },
                dataValues: {
                    name: '',
                },
                modalId : 'modal-returned-product',
                modalIdConfirmation : 'modal-returned-product-confirmation',
                modalIdRelease : 'modal-returned-product-release',
                modalTitle : 'Returned Product',
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
        confirmationProduct(props){   
            this.dataValues = props.data;
            $('#'+this.modalIdConfirmation).modal('show');
        },
        releasedProduct(){
            $('#'+this.modalId).modal('show');
            $('#'+this.modalIdConfirmation).modal('hide');
        },
        confirmProduct(){
            $('#'+this.modalId).modal('hide');
            $('#'+this.modalIdConfirmation).modal('hide');
            axios.post('/defective-products/damaged', this.dataValues).then(response => {
                    if(response.status === 200) {
                        Swal.fire({
                            title: "Success",
                            text: response.data.message,
                            icon: 'success',
                            timer: 3000
                        });
                    }
                    this.getData();
                    $('#'+this.modalIdRelease).modal('show');
                    $('#' + this.modalId).modal('hide');
                })
                .catch(errors => {
                        this.errors = errors.response.data.errors;
                })
        },
        forContacting(){
            $('#'+this.modalIdRelease).modal('hide');
        },
        forBorrowing()
        {
            axios.post('/defective-products/store', this.dataValues).then(response => {
                    if(response.status === 200) {
                        Swal.fire({
                            title: "Success",
                            text: response.data.message,
                            icon: 'success',
                            timer: 3000
                        });
                    }
                    this.getData();
                    $('#' + this.modalIdRelease).modal('hide');
                })
                .catch(errors => {
                        this.errors = errors.response.data.errors;
                })
        },
        getData() {
            axios.get('/defective-products/show').then(response => {
                this.data = response.data.data;
            })
        }
        },
        mounted() {
            this.getData();
        }
        


}

</script>
