<template>
    <div class="p-3">
        <BreadCrumbComponent tab_title="Product"></BreadCrumbComponent>
        
        <!-- ADD PRODUCT -->
        <div class="card">
            <div class="card-body">
                <FormComponent 
                    :data="data"
                    :columns="columns"
                    :options="options"
                    btnName="Add Product"
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
                    <h4>Add Product</h4>
                </div>
            </template>
            <template #modalBody>
                <div class="row">
                    <div class="col-12">
                        <label for="">Brand</label>
                        <select v-model="dataValues.brand" class="form-control">
                            <option value="" disabled selected>Brand</option>
                            <option value="Bosch">Bosch</option>
                            <option value="Dewalt">Dewalt</option>
                            <option value="Makita">Makita</option>
                            <option value="Milwaukee">Milwaukee</option>
                            <option value="Black+Decker">Black+Decker</option>
                            <option value="Craftsman">Craftsman</option>
                            <option value="Hitachi">Hitachi</option>
                            <option value="Ingersoll">Ingersoll</option>
                            <option value="Porter-Cable">Porter-Cable</option>
                            <option value="Snap-on">Snap-on</option>
                            <option value="Ridgid">Ridgid</option>
                            <option value="Metabo">Metabo</option>
                            <option value="Ryobi">Ryobi</option>
                            <option value="Other">Other</option>
                        </select>
                        <input type="text" v-if="dataValues.brand === 'Other'" v-model="dataValues.other_brand" class="form-control mt-2" placeholder="Enter other brand">
                    </div>  
                    <div class="col-lg-12">
                        <label for="">Tools</label>
                        <select v-model="dataValues.tool" class="form-control">
                            <option value="" disabled selected>Tools</option> 
                            <option value="Drill">Drill</option>
                            <option value="Screwdriver">Screwdriver</option>
                            <option value="Wrench">Wrench</option>
                            <option value="Grinder">Grinder</option>
                            <option value="Jigsaw">Jigsaw</option>
                            <option value="Saw">Saw</option>
                            <option value="Other">Other</option>
                        </select>
                        <input type="text" v-if="dataValues.tool === 'Other'" v-model="dataValues.other_tool" class="form-control mt-2" placeholder="Enter other tool">
                    </div>
                    <div class="col-12">
                        <label for="image">Image</label>
                        <input type="file" id="image" class="form-control" @change="onFileChange">
                        <div class="text-danger" v-if="errors.image">{{ errors.image[0] }}</div>
                    </div>
                    <div class="col-12" v-if="dataValues.image">
                        <label for="currentImage">Current Image</label>
                        <img :src="'/images/' + dataValues.image" alt="Current Image" class="img-fluid">
                    </div>
                    <div class="col-lg-12 mt-3">
                        <label for="">Power Source</label>
                        <div class="d-flex justify-content-around">
                            <div class="form-check">
                                <input type="radio" class="form-check-input" id="cordedRadio" value="corded" v-model="dataValues.powerSources">
                                <label class="form-check-label" for="cordedRadio">Corded</label>
                            </div>
                            <div class="form-check mt-2">
                                <input type="radio" class="form-check-input" id="batteryRadio" value="battery" v-model="dataValues.powerSources">
                                <label class="form-check-label" for="batteryRadio">Battery</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <label for="">Voltage</label>
                        <input type="text" class="form-control" v-model="dataValues.voltage">
                    </div>
                    <div class="col-lg-6">
                        <label for="">Weight</label>
                        <input type="text" class="form-control" v-model="dataValues.weight">
                    </div>
                    <div class="col-lg-6">
                        <label for="">Dimensions</label>
                        <input type="text" class="form-control" v-model="dataValues.dimensions">
                    </div>
                    <div class="col-lg-6">
                        <label for="">Material</label>
                        <input type="text" class="form-control" v-model="dataValues.material">
                    </div>
                    <div class="col-12 mt-2">
                        <label for="">Stocks</label>
                        <input type="number" class="form-control" v-model="dataValues.stocks">
                        <div class="text-danger" v-if="errors.stocks">{{ errors.stocks[0] }}</div>
                    </div>
                    <div class="col-12">
                        <label for="">Price per pc</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">₱</span>
                            </div>
                            <input type="text" class="form-control" v-model="dataValues.price">
                        </div>
                        <div class="text-danger" v-if="errors.price">{{ errors.price[0] }}</div>
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
                powerSources : '',
                imageData: null,
                columns : ['brand', 'tool', 'powerSources', 'voltage', 'weight', 'dimensions', 'material', 'stocks', 'price' ,'action'],
                errors: [],
                options : {
                    headings : {
                        brand: 'Brand',
                        tool: 'Tool',
                        powerSources: 'Power Source',
                        voltage: 'Voltage',
                        weight: 'Weight',
                        dimensions: 'Dimensions',
                        material: 'Material',
                        stocks: 'Stocks',
                        price: 'Price',
                        action: 'Action'
                    },
                    filterable: false,
                    sortable: []
                },
                dataValues: {
                    name: '',
                },
                modalId : 'modal-product',
                modalTitle : 'Add a Product',
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
        onFileChange(event) {
            const file = event.target.files[0];
            if (file) {
                this.imageData = file;
            }
        },
        addClicked(){
            this.clearInputs();
            $('#' + this.modalId).modal('show');
        },
        getData() {
            axios.get('/products/show').then(response => {
                this.data = response.data.data;
            })
        },
        clearInputs() {
            this.dataValues = {
                name: '',
            }
            this.imageData = null;
            this.errors = [];
        },
        editClicked(props) {
            this.modalTitle = 'Edit Data';

            axios.get('/products/edit/' + props.data.id).then(response => {
                this.dataValues = response.data.data;
                $('#' + this.modalId).modal('show');
            }).catch(errors => {
                // Handle errors
                if (errors.response.data.message.length > 0) {
                    Swal.fire({
                        title: "Failed",
                        text: errors.response.data.message,
                        icon: 'error',
                        timer: 3000
                    });
                    this.errors = errors.response.data.errors;
                }
            });
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
                    axios.get('/products/destroy/' + props.data.id).then(response => {
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
            const formData = new FormData();
            // Check if id is present and not null before appending it to the form data
            if (this.dataValues.id !== null && this.dataValues.id !== undefined) {
                formData.append('id', this.dataValues.id);
            }
            formData.append('brand', this.dataValues.brand);
            formData.append('tool', this.dataValues.tool);
            formData.append('other_brand', this.dataValues.other_brand);
            formData.append('other_tool', this.dataValues.other_tool);
            formData.append('image', this.dataValues.image);
            formData.append('powerSources', this.dataValues.powerSources);
            formData.append('voltage', this.dataValues.voltage);
            formData.append('weight', this.dataValues.weight);
            formData.append('dimensions', this.dataValues.dimensions);
            formData.append('material', this.dataValues.material);
            formData.append('stocks', this.dataValues.stocks);
            formData.append('price', this.dataValues.price);

            if (this.imageData) {
                formData.append('image', this.imageData);
            }
                axios.post('/products/store', formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data',
                    },
                }).then(response => {
                    if (response.status === 200) {
                        Swal.fire({
                            title: 'Success',
                            text: response.data.message,
                            icon: 'success',
                            timer: 3000
                        });
                    }
                    this.getData();
                    $('#' + this.modalId).modal('hide');
                }).catch(errors => {
                    this.errors = errors.response.data.errors;
                });
            },
        },
        mounted() {
            this.getData();
        }
        
}

</script>
