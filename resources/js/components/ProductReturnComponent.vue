<template>
    <div class="p-3">
        <BreadCrumbComponent tab_title="Return a Product"></BreadCrumbComponent>
        <div class="card">
            <div class="card-body">
                <FormComponent 
                    :data="product"
                    :columns="columns"
                    :options="options"
                    :addButton="false"
                    :option1Switch="false"
                    :option2Switch="false"
                    :option3Switch="true"
                    option3Icon="fa-solid fa-people-carry-box"
                    option3Name="Return" 
                    @optionalClicked="returnProductNow"
                >
                </FormComponent>
            </div>
        </div>

        <!-- RETURN PRODUCTS -->
        <ModalComponent :id="modalId" :title="modalTitle" :size="modalSize" :position="modalPosition">
            <template #modalHeader>
                <div class="m-auto">
                    <h4>Return a Product</h4>
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
                        <label for="">Return Date</label>
                        <input type="text" class="form-control" v-model="dataValues.return_date" disabled>
                        <div class="text-danger" v-if="errors.return_date">{{ errors.return_date[0] }}</div>
                    </div>
                    <div class="col-12">
                        <label for="">Penalty if not return by the given date</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">₱</span>
                            </div>
                            <input type="text" class="form-control" v-model="dataValues.penalty" disabled>
                        </div>
                    </div>
                    <div class="col-12" v-if="dataValues.detail != 'Returning' || dataValues.detail != 'Damaged'">
                        <label for="">Delivery Date</label>
                        <input type="date" class="form-control" v-model="dataValues.delivery_date" :min="today">          
                      </div>
                    <div class="col-12">
                        <label for="">Ship the Borrowed Product to this Location: </label>
                        <textarea type="text" class="form-control" v-model="userAdmin.location" disabled>
                        </textarea>
                    </div>
                    <div class="col-12 pt-3" v-if="new Date() > new Date(dataValues.return_date)">
                        <label for="">Since you did not return the product by the specified date, you will be charged a penalty:</label>
                        <div class="input-group" style="outline:2px solid red">
                            <div class="input-group-prepend">
                                <span class="input-group-text">₱</span>
                            </div>
                            <input type="text" class="form-control" v-model="dataValues.penalty" disabled>
                        </div>
                    </div>
                    <div class="col-12 mt-3" v-if="dataValues.detail === 'Damaged'" style="outline: 2px solid red;">
                        <label for="">Note: </label>
                        <div>
                            After inspecting the product, it appears to be damaged. This may have occurred during delivery or for some other reason. The administrator will contact you soon. Please keep your lines open.
                        </div>
                    </div>
                </div>
            </template>
            <template #modalFooter>
            <div class="text-right">
                <button class="btn btn-success" v-on:click="returnThisProduct" v-bind:disabled="dataValues.detail === 'Returning' || dataValues.detail === 'Damaged' || dataValues.detail === 'Completed'">
                    {{ dataValues.detail === 'Returning' || dataValues.detail === 'Damaged' || dataValues.detail === 'Completed' ? 'You are already returning this product' : 'Return' }}
                </button>
            </div>
        </template>
        </ModalComponent>

        <!-- Due Product Modal -->
        <div class="modal fade" id="dueProductModal" tabindex="-1" role="dialog" aria-labelledby="dueProductModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="dueProductModalLabel">Product Due for Return</h5>
            </div>
            <div class="modal-body">
                <p>You have the following products due for return. Please return them to avoid penalties.</p>
                <ul>
                    <li v-for="product in productsDueForReturn" :key="product.id">
                        {{ product.brand_name }} - {{ product.tool_name }} - {{ product.product_code }}
                    </li>
                </ul>
            </div>
            </div>
        </div>
        </div>

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
                product : [],
                userAdmin : [],
                columns : ['brand_name', 'tool_name', 'product_code', 'detail' ,'action'],
                errors: [],
                options : {
                    headings : {
                        brand_name : 'Brand',
                        tool_name : 'Tool',
                        product_code : 'Product Code',
                        detail : 'Status',
                        action : 'Action',
                    },
                    filterable: false,
                    sortable: []
                },
                dataValues: {
                    name: '',
                },
                modalId : 'modal-return-product-penalty',
                modalTitle : 'Return a Product',
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
        productsDueForReturn() {
            // Get the date 3 days from now
            const threeDaysFromNow = new Date();
            threeDaysFromNow.setDate(threeDaysFromNow.getDate() + 3);

            // Filter the products that are due for return within the next 3 days
            return this.product.filter(product => {
                const productReturnDate = new Date(product.return_date);
                return productReturnDate <= threeDaysFromNow;
            });
        },
        today() {
            const today = new Date();
            const year = today.getFullYear();
            const month = String(today.getMonth() + 1).padStart(2, '0');
            const day = String(today.getDate()).padStart(2, '0');
            return `${year}-${month}-${day}`;
        }
    },
    watch: {
        productsDueForReturn(newVal) {
            if (newVal.length > 0) {
                // Show the modal if there are any products due for return
                $('#dueProductModal').modal('show');
            }
        }
    },
    methods: {
        addClicked(props){
            $('#' + this.modalId).modal('show');
            this.clearInputs();
        },
        getData() {
            axios.get('/return-product/show').then(response => {
                this.product = response.data.product;
                this.userAdmin = response.data.userAdmin;
            })
        },
        clearInputs() {
            this.dataValues = {
                name: '',
            }
            this.errors = [];
        },
        returnProductNow(props){
            this.dataValues = props.data;
            $('#' + this.modalId).modal('show');
        },
        returnThisProduct(){
            Swal.fire({
                title: 'Are you sure you want to Return the Product?',
                text: 'You will not be able to recover this Action!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, Return it!',
                cancelButtonText: 'Cancel',
            }).then((result) => {
                if (result.isConfirmed) {
                    // User confirmed, proceed with deletion
                    axios.post('/return-product/store', this.dataValues).then(response => {
                        if(response.status === 200) {
                            Swal.fire({
                                title: "Success",
                                text: response.data.message,
                                icon: 'success',
                                timer: 3000
                            });
                        }
                        $('#' + this.modalId).modal('hide');
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
                axios.post('/unit/store', this.dataValues).then(response => {
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
