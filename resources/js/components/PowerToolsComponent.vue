<template>
    <div class="p-3">
        <BreadCrumbComponent tab_title="Power Tools"></BreadCrumbComponent>
        <div class="card">
            <div class="card-body">
                <FormComponent 
                    :data="data"
                    :columns="columns"
                    :options="options"
                    btnName="Add Power Tools"
                    @deleteClicked="deleteClicked"
                    @editClicked="editClicked"
                    @addClicked="addClicked"
                >
                </FormComponent>
            </div>
        </div>

        <ModalComponent :id="modalId" :title="modalTitle" :size="modalSize" :position="modalPosition">
            <template #modalHeader>
                <div class="m-auto">
                    <h4>Add Power Tools</h4>
                </div>
            </template>
            <template #modalBody>
                <div class="row">
                    <div class="col-12">
                        <label for="">Name</label>
                        <input type="text" class="form-control" v-model="dataValues.name">
                        <div class="text-danger" v-if="errors.name">{{ errors.name[0] }}</div>
                    </div>  
                    <div class="col-12">
                        <label for="">Quantity</label>
                        <input type="number" class="form-control" v-model="dataValues.quantity" @change="onChange()">
                        <div class="text-danger" v-if="errors.quantity">{{ errors.quantity[0] }}</div>
                    </div>  
                    <div class="col-12">
                        <label for="">Price</label>
                        <input type="number" class="form-control" v-model="dataValues.price" @change="onChange()">
                        <div class="text-danger" v-if="errors.price">{{ errors.price[0] }}</div>
                    </div>  
                    <div class="col-12">
                        <label for="">Category</label>
                        <select class="form-control" v-model="dataValues.category_id">
                            <option v-for="category in categories" :value="category.id">{{ category.name }}</option>
                        </select>  
                        <div class="text-danger" v-if="errors.category">{{ errors.category[0] }}</div>
                    </div>  
                    <div class="col-12">
                        <label for="">Unit</label>
                        <select class="form-control" v-model="dataValues.unit_id">
                            <option v-for="unit in units" :value="unit.id">{{ unit.name }}</option>
                        </select>  
                        <div class="text-danger" v-if="errors.unit">{{ errors.unit[0] }}</div>
                    </div>  
                    <div class="col-12">
                        <label for="">Supplier</label>
                        <select class="form-control" v-model="dataValues.supplier_id">
                            <option v-for="supplier in suppliers" :value="supplier.id">{{ supplier.name }}</option>
                        </select>
                        <div class="text-danger" v-if="errors.supplier">{{ errors.supplier[0] }}</div>
                    </div>  
                    <div class="col-12">
                        <label for="">Total</label>
                        <input type="number" class="form-control" v-model="dataValues.total">
                        <div class="text-danger" v-if="errors.total">{{ errors.total[0] }}</div>
                    </div>  
                </div>
            </template>
            <template #modalFooter>
                <div class="text-right">
                    <button class="btn btn-success" v-on:click="storeData">Save</button>
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
                categories : [],
                units : [],
                suppliers : [],
                columns : ['name', 'quantity', 'price', 'category_name', 'unit_name', 'supplier_name', 'total' ,'action'],
                errors: [],
                options : {
                    headings : {
                        name : 'Unit',
                        quantity: 'Quantity',
                        price: 'Price',
                        category_name: 'Category',
                        unit_name: 'Unit',
                        supplier_name: 'Supplier',
                        total: 'Total',
                        action : 'Action',
                    },
                    filterable: false,
                    sortable: []
                },
                dataValues: {
                    name: '',
                },
                modalId : 'modal-powertools',
                modalTitle : 'Power Tools',
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
        onChange(){
            this.dataValues.total = this.dataValues.quantity * this.dataValues.price;
        },
        addClicked(props){
            $('#' + this.modalId).modal('show');
            this.clearInputs();
        },
        getData() {
            axios.get('/powertools/show').then(response => {
                this.data = response.data.data;
                this.categories = response.data.categories;
                this.units = response.data.units;
                this.suppliers = response.data.suppliers;
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

            axios.get('/powertools/edit/' + this.dataValues.id).then(response => {
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
                    axios.get('/powertools/destroy/' + props.data.id).then(response => {
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
                axios.post('/powertools/store', this.dataValues).then(response => {
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
