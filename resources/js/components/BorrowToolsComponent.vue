<style>
    .sold-out .card-body {
        background-color: #f8f9fa; /* Change background color for sold-out cards */
        opacity: 0.5; /* Set opacity to 80% */
    }

    .btn-sold-out {
        color: #000 !important; /* Change button text color to black */
    }
</style>

<template>
    <div class="p-3">
        <BreadCrumbComponent tab_title="Borrow Tools"></BreadCrumbComponent>
        <div class="row mb-3">
            <div class="col-lg-2">
                <select v-model="selectedBrand" class="form-control">
                    <option value="" disabled selected>Select Brand</option>
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
                </select>
            </div>
            <div class="col-lg-2">
                <select v-model="selectedTool" class="form-control">
                    <option value="" disabled selected>Select Tools</option> 
                    <option value="Drill">Drill</option>
                    <option value="Screwdriver">Screwdriver</option>
                    <option value="Wrench">Wrench</option>
                    <option value="Grinder">Grinder</option>
                    <option value="Jigsaw">Jigsaw</option>
                    <option value="Saw">Saw</option>
                </select>
            </div>
            <div class="col-lg-2">
                <select v-model="selectedSpecs" class="form-control">
                    <option value="" disabled selected>Select Specifications</option> 
                    <option value="battery">Battery</option>
                    <option value="corded">Corded</option>
                </select>
            </div>
            <div>
                <button class="btn btn-primary" @click="filterData">Search</button>
                <button class="btn btn-success ml-1" @click="refresh"><i class="fas fa-sync-alt"></i></button>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4" v-for="(tool, index) in data" :key="index">
                <div class="card" :class="{ 'sold-out': tool.status === 'Borrowed' }">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div style="width: 50%;">
                                <h5 class="card-title">{{ tool.brand_name }} {{ tool.tool_name }}</h5>
                                <p class="card-text">Price: {{ tool.price }}</p>
                                <p class="card-text">Product Code: {{ tool.product_code }}</p>
                            </div>
                            <div style="width: 50%;">
                                <img v-if="tool.product_image" :src="'/images/' + tool.product_image" alt="Tool Image" class="img-fluid" style="height: 250px;">
                                <p v-else>No Image</p>
                            </div>
                        </div>
                        <button :class="tool.status === 'Borrowed' ? 'btn btn-dark' : 'btn btn-primary'"
                                v-on:click="showDetails(tool)"
                                :disabled="tool.status === 'Borrowed'">
                            {{ tool.status === 'Borrowed' ? 'Borrowed' : 'Borrow Tool' }}
                        </button>
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
                    <div class="col-6 pb-2">
                        <label for="">Brand</label>
                        <input type="text" v-model="dataValues.brand_name" class="form-control" disabled> 
                    </div>  
                    <div class="col-6 pb-2">
                        <label for="">Tool</label>
                        <input type="text" v-model="dataValues.tool_name" class="form-control" disabled> 
                    </div>
                    <div class="col-12 pb-2">
                        <label for="">Product Code</label>
                        <input type="text" v-model="dataValues.product_code" class="form-control" disabled> 
                    </div>
                    <div class="col-12 pb-2">
                        <label for="">Power Source</label>
                        <input type="text" v-model="dataValues.powerSources" class="form-control" disabled> 
                    </div>
                    <div class="col-6 pb-2">
                        <label for="">Voltage</label>
                        <input type="text" v-model="dataValues.voltage" class="form-control" disabled> 
                    </div> 
                    <div class="col-6 pb-2">
                        <label for="">Weight</label>
                        <input type="text" v-model="dataValues.weight" class="form-control" disabled> 
                    </div>   
                    <div class="col-6 pb-2">
                        <label for="">Dimension</label>
                        <input type="text" v-model="dataValues.dimensions" class="form-control" disabled> 
                    </div> 
                    <div class="col-6 pb-2">
                        <label for="">Material</label>
                        <input type="text" v-model="dataValues.material" class="form-control" disabled> 
                    </div>   
                    <div class="col-12 pb-2">
                        <label for="">Return Days</label>
                        <select class="form-control" v-model="dataValues.return_days_id">
                            <option v-for="returnday in returndays" :value="returnday.id">{{ returnday.number_of_days }} Days</option>
                        </select>
                        <div class="text-danger" v-if="errors.returnday">{{ errors.returnday[0] }}</div>
                    </div> 
                    <div class="col-12 pb-2">
                        <label for="">Price</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">₱</span>
                            </div>
                            <input type="text" class="form-control" v-model="dataValues.price" disabled>
                        </div>
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
            returndays: '',
            selectedBrand: '',
            selectedTool: '',
            selectedSpecs: '',
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
        showDetails(tool) {
            this.dataValues = tool;
            $('#' + this.modalId).modal('show');
        },
        refresh(){
            window.location.reload();
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
        filterData() {
            const searchData = {
                brand: this.selectedBrand,
                tool: this.selectedTool,
                specs: this.selectedSpecs
            };

            axios.post('/borrowtools/filterData', searchData)
                .then(response => {
                    this.data = response.data.data;
                    if (this.data.length === 0) {
                        Swal.fire({
                            title: "No Products available!",
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
        storeData() {
            Swal.fire({
                title: 'Are you sure?',
                text: 'Do you want to Borrow this tool?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Yes, Borrow it!',
                cancelButtonText: 'No, cancel!',
            }).then((result) => {
                if (result.isConfirmed) {
                    axios.post('/borrowtools/store', this.dataValues)
                    .then(response => {
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
                    });
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    Swal.fire('Cancelled', 'Your borrow has been cancelled.', 'error');
                }
            });
        },
    },
    mounted() {
            this.getData();
        }
        


}

</script>
