<template>
    <div class="p-3">
        <BreadCrumbComponent tab_title="Return a Damaged Product"></BreadCrumbComponent>
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
                    option3Icon="fa-solid fa-people-carry-box"
                    option3Name="Return"
                    @optionalClicked="returnDamagedProduct"
                >
                </FormComponent>
            </div>
        </div>

        <ModalComponent :id="modalId" :title="modalTitle" :size="modalSize" :position="modalPosition">
            <template #modalHeader>
                <div class="m-auto">
                    <h4>Return this Product?</h4>
                </div>
            </template>
            <template #modalBody>
                <div class="row">
                    <div class="col-6">
                        <label for="">Brand</label>
                        <input type="text" class="form-control" v-model="dataValues.brand_name" disabled>
                    </div>  
                    <div class="col-6">
                        <label for="">Tool</label>
                        <input type="text" class="form-control" v-model="dataValues.tool_name" disabled>
                    </div>  
                    <div class="col-12">
                        <label for="">Product Code</label>
                        <input type="text" class="form-control" v-model="dataValues.product_code" disabled>
                    </div>  
                    <div class="col-12">
                        <label for="">Ship the Product to this Location: </label>
                        <textarea type="text" class="form-control" v-model="userAdmin.location" disabled></textarea>
                    </div>  
                    <div class="col-12">
                        <label for="">Delivery Date</label>
                        <input type="date" class="form-control" v-model="dataValues.delivery_date" :min="today" :disabled="dataValues.status_damage === 'Damaged'">
                        <div class="text-danger" v-if="errors.delivery_date">{{ errors.delivery_date[0] }}</div>  
                    </div>
                    <div class="col-12">
                        <label for="">Please describe the damaged of this product</label>
                        <textarea type="text" class="form-control" v-model="dataValues.damaged_description" :disabled="dataValues.status_damage === 'Damaged'"></textarea>
                        <div class="text-danger" v-if="errors.damaged_description">{{ errors.damaged_description[0] }}</div>  
                    </div>
                </div>
            </template>
            <template #modalFooter>
                <div class="text-right">
                    <button class="btn btn-success" v-on:click="storeData" :disabled="dataValues.status_damage === 'Damaged'">
                        {{ dataValues.status_damage === 'Damaged' ? 'You are already returning this product' : 'Return' }}
                    </button>
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
                userAdmin : [],
                columns : ['brand_name', 'tool_name', 'product_code', 'status' ,'action'],
                errors: [],
                options : {
                    headings : {
                        brand_name : 'Brand',
                        tool_name : 'Tool',
                        product_code : 'Product Code',
                        status : 'Status',
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
    computed: {
        today() {
            const today = new Date();
            const year = today.getFullYear();
            const month = String(today.getMonth() + 1).padStart(2, '0');
            const day = String(today.getDate()).padStart(2, '0');
            return `${year}-${month}-${day}`;
        }
    },
    methods: {
        returnDamagedProduct(props){
            this.dataValues = props.data;
            $('#' + this.modalId).modal('show');
        },
        getData() {
            axios.get('/damaged-return/show').then(response => {
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
        editClicked(props) {
            this.dataValues = props.data;
            this.modalTitle= 'Edit Data';

            axios.get('/unit/edit/' + this.dataValues.id).then(response => {
                this.dataValues = response.data.data;
                $('#' + this.modalId).modal('show');
            })
            .catch(errors => {
                if(errors.response.data.message.length > 0) {
                    Swal.fire({
                        title: "Failed",
                        text: errors.response.data.message,
                        icon: 'error',
                        timer: 3000
                    });
                    this.errors = errors.response.data.errors;
                }
            })
        },
        deleteClicked(props) {
            Swal.fire({
                title: 'Are you sure?',
                text: 'You will not be able to recover this data!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel',
            }).then((result) => {
                if (result.isConfirmed) {
                    // User confirmed, proceed with deletion
                    axios.get('/unit/destroy/' + props.data.id).then(response => {
                        if(response.status === 200) {
                            Swal.fire({
                                title: "Success",
                                text: response.data.message,
                                icon: 'success',
                                timer: 3000
                            });
                        }
                        this.getData();
                    }).catch(errors => {
                        if(errors.response.data.message.length > 0) {
                            Swal.fire({
                                title: "Failed",
                                text: errors.response.data.message,
                                icon: 'error',
                                timer: 3000
                            });
                        }
                    });
                }
            });
        },
        storeData() {
                axios.post('/damaged-return/store', this.dataValues).then(response => {
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
                        this.errors = errors.response.data.errors;
                })
            },
        },
        mounted() {
            this.getData();
        }
        


}

</script>
